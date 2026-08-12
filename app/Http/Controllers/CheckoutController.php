<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderService;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        if (\App\Models\Setting::isOrderingClosed()) {
            return redirect()->route('cart.index')
                ->with('error', 'Pemesanan sudah ditutup untuk hari ini karena telah melewati batas jam operasional.');
        }

        if (! session('table_id')) {
            return redirect()->route('cart.index')
                ->with('error', 'Silakan scan QR meja terlebih dahulu.');
        }

        if (count($cart) == 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang masih kosong.');
        }

        $total = 0;

        foreach ($cart as $item) {
            $canUseAddOn = in_array(strtolower(trim($item['category'] ?? '')), ['main course', 'main cource'], true);
            $addOnPrice = $canUseAddOn ? (int) ($item['add_on_price'] ?? 0) : 0;
            $total += ((int) $item['price'] + $addOnPrice) * $item['qty'];
        }

        return view('customer.checkout.checkout', compact('cart', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'payment_method' => 'required',
        ]);

        $cart = session()->get('cart', []);

        if (\App\Models\Setting::isOrderingClosed()) {
            return redirect()->route('cart.index')
                ->with('error', 'Pemesanan sudah ditutup untuk hari ini karena telah melewati batas jam operasional.');
        }

        $table = \App\Models\CafeTable::find(session('table_id'));
        if (!$table) {
            return redirect()->route('cart.index')
                ->with('error', 'Meja tidak ditemukan, silakan scan ulang QR Code meja Anda.');
        }

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang kosong.');
        }

        DB::beginTransaction();

        try {

            $total = 0;
            $validatedItems = [];

            foreach ($cart as $item) {
                $dbMenu = \App\Models\Menu::find($item['id']);
                if (!$dbMenu || !$dbMenu->status) {
                    throw new \Exception("Menu '{$item['name']}' sedang tidak tersedia / habis. Silakan hapus dari keranjang.");
                }

                $realPrice = (int) $item['price'];
                $canUseAddOn = in_array(strtolower(trim($item['category'] ?? '')), ['main course', 'main cource'], true);
                $addOnPrice = $canUseAddOn ? (int) ($item['add_on_price'] ?? 0) : 0;
                $subtotal = ($realPrice + $addOnPrice) * $item['qty'];
                $total += $subtotal;

                $validatedItems[] = [
                    'menu_id' => $dbMenu->id,
                    'menu_variant_id' => $item['variant_id'] ?? null,
                    'variant_name' => $item['variant_name'] ?? null,
                    'qty' => $item['qty'],
                    'price' => $realPrice,
                    'subtotal' => $subtotal,
                    'sugar_level' => $item['sugar_level'] ?? 'normal',
                    'temperature' => $item['temperature'] ?? 'ice',
                    'ice_level' => $item['ice_level'] ?? 'normal',
                    'add_on' => $canUseAddOn ? ($item['add_on'] ?? null) : null,
                    'add_on_menu_id' => $canUseAddOn ? ($item['add_on_menu_id'] ?? null) : null,
                    'add_on_price' => $addOnPrice,
                    'note' => $item['note'] ?? null,
                ];
            }

            $order = Order::create([
                'invoice' => OrderService::generateInvoice(),
                'cafe_table_id' => $table->id,
                'customer_name' => $request->customer_name,
                'phone' => $request->phone,
                'service_type' => session('service_type', 'dine_in'),
                'total' => $total,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
            ]);

            // Update status meja menjadi terisi (occupied)
            $table->update(['status' => 'occupied']);

            // Simpan item pesanan
            foreach ($validatedItems as $vItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $vItem['menu_id'],
                    'menu_variant_id' => $vItem['menu_variant_id'],
                    'variant_name' => $vItem['variant_name'],
                    'qty' => $vItem['qty'],
                    'price' => $vItem['price'],
                    'subtotal' => $vItem['subtotal'],
                    'sugar_level' => $vItem['sugar_level'],
                    'temperature' => $vItem['temperature'],
                    'ice_level' => $vItem['ice_level'],
                    'add_on' => $vItem['add_on'],
                    'add_on_menu_id' => $vItem['add_on_menu_id'],
                    'add_on_price' => $vItem['add_on_price'],
                    'note' => $vItem['note'],
                ]);
            }

            // Generate Snap Token jika Midtrans
            if ($request->payment_method == 'midtrans') {

                $params = [

                    'transaction_details' => [
                        'order_id' => $order->invoice,
                        'gross_amount' => (int) $order->total,
                    ],

                    'customer_details' => [
                        'first_name' => $order->customer_name,
                        'phone' => $order->phone,
                    ],

                ];

               $snapToken = Snap::getSnapToken($params);

                $order->update([
                    'snap_token' => $snapToken,
                ]);
            }

            DB::commit();

            session()->forget('cart');

            return redirect()->route('order.success', $order->id);

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());

        }
    }
    
}
