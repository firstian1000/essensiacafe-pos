<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Snap;

class CashierController extends Controller
{
    public function index()
    {
        $menus = Menu::with(['category', 'variants' => function ($query) {
                $query->where('status', true)->orderBy('name');
            }])
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('cashier.index', compact('menus', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'service_type' => ['required', 'in:dine_in,take_away'],
            'payment_method' => ['required', 'in:cash,midtrans'],
            'submit_action' => ['required', 'in:print_receipt,pay_midtrans'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'exists:menus,id'],
            'items.*.variant_id' => ['nullable', 'exists:menu_variants,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        if (\App\Models\Setting::isOrderingClosed()) {
            return back()
                ->withInput()
                ->with('error', 'Pemesanan sudah ditutup untuk hari ini karena telah melewati batas jam operasional.');
        }

        $menuIds = collect($data['items'])->pluck('menu_id')->all();
        $menus = Menu::with(['variants' => function ($query) {
                $query->where('status', true);
            }])
            ->whereIn('id', $menuIds)
            ->get()
            ->keyBy('id');

        foreach ($data['items'] as $item) {
            $menu = $menus[$item['menu_id']] ?? null;
            if (! $menu || ! $menu->status) {
                return back()->withInput()->with('error', 'Ada menu yang sedang tidak tersedia.');
            }

        }

        $total = collect($data['items'])->sum(function ($item) use ($menus) {
            $menu = $menus[$item['menu_id']] ?? null;
            $variant = $menu && ! empty($item['variant_id'])
                ? $menu->variants->firstWhere('id', (int) $item['variant_id'])
                : null;

            return ($variant?->price ?? $menu?->price ?? 0) * $item['qty'];
        });

        $isCash = $data['payment_method'] === 'cash';
        $isMidtransPayment = $data['payment_method'] === 'midtrans' && $data['submit_action'] === 'pay_midtrans';

        if ($isCash && $data['paid_amount'] < $total) {
            return back()
                ->withInput()
                ->with('error', 'Nominal bayar kurang dari total pesanan.');
        }

        $order = DB::transaction(function () use ($data, $menus, $total, $isCash) {
            $order = Order::create([
                'invoice' => OrderService::generateInvoice(),
                'cafe_table_id' => null,
                'customer_name' => $data['customer_name'],
                'phone' => null,
                'service_type' => $data['service_type'],
                'total' => $total,
                'status' => 'completed',
                'payment_method' => $data['payment_method'],
                'payment_status' => $isCash ? 'paid' : 'pending',
            ]);

            foreach ($data['items'] as $item) {
                $menu = $menus[$item['menu_id']];
                $variant = ! empty($item['variant_id'])
                    ? $menu->variants->firstWhere('id', (int) $item['variant_id'])
                    : null;

                if (! empty($item['variant_id']) && ! $variant) {
                    throw new \Exception("Varian untuk menu '{$menu->name}' tidak tersedia.");
                }

                $price = (int) ($variant?->price ?? $menu->price);

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'menu_variant_id' => $variant?->id,
                    'variant_name' => $variant?->name,
                    'qty' => $item['qty'],
                    'price' => $price,
                    'subtotal' => $price * $item['qty'],
                ]);
            }

            return $order;
        });

        if ($isMidtransPayment) {
            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id' => $order->invoice,
                    'gross_amount' => (int) $order->total,
                ],
                'customer_details' => [
                    'first_name' => $order->customer_name,
                ],
            ]);

            $order->update([
                'snap_token' => $snapToken,
            ]);

            return redirect()->route('order.success', [
                'order' => $order->id,
                'auto_pay' => 1,
            ]);
        }

        return redirect()->route('cashier.receipt', [
            'order' => $order->id,
            'paid' => $isCash ? $data['paid_amount'] : $total,
        ]);
    }

    public function receipt(Request $request, Order $order)
    {
        $order->load(['items.menu', 'table']);
        $paidAmount = (float) $request->query('paid', $order->total);

        return view('cashier.receipt', compact('order', 'paidAmount'));
    }
}
