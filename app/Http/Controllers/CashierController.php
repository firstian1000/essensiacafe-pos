<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function index()
    {
        $menus = Menu::with('category')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('cashier.index', compact('menus', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['required', 'in:cash,qris,card'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'exists:menus,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        if (\App\Models\Setting::isOrderingClosed()) {
            return back()
                ->withInput()
                ->with('error', 'Pemesanan sudah ditutup untuk hari ini karena telah melewati batas jam operasional.');
        }

        $menuIds = collect($data['items'])->pluck('menu_id')->all();
        $menus = Menu::whereIn('id', $menuIds)->get()->keyBy('id');

        $total = collect($data['items'])->sum(function ($item) use ($menus) {
            return ($menus[$item['menu_id']]->price ?? 0) * $item['qty'];
        });

        if ($data['paid_amount'] < $total) {
            return back()
                ->withInput()
                ->with('error', 'Nominal bayar kurang dari total pesanan.');
        }

        $order = DB::transaction(function () use ($data, $menus, $total) {
            $order = Order::create([
                'invoice' => OrderService::generateInvoice(),
                'cafe_table_id' => null,
                'customer_name' => $data['customer_name'] ?: 'Customer Kasir',
                'phone' => null,
                'total' => $total,
                'status' => 'completed',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'paid',
            ]);

            foreach ($data['items'] as $item) {
                $menu = $menus[$item['menu_id']];
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'qty' => $item['qty'],
                    'price' => $menu->price,
                    'subtotal' => $menu->price * $item['qty'],
                ]);
            }

            return $order;
        });

        return redirect()->route('cashier.receipt', [
            'order' => $order->id,
            'paid' => $data['paid_amount'],
        ]);
    }

    public function receipt(Request $request, Order $order)
    {
        $order->load(['items.menu', 'table']);
        $paidAmount = (float) $request->query('paid', $order->total);

        return view('cashier.receipt', compact('order', 'paidAmount'));
    }
}
