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
            $total += $item['price'] * $item['qty'];
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

                $realPrice = (int) $dbMenu->price;
                $subtotal = $realPrice * $item['qty'];
                $total += $subtotal;

                $validatedItems[] = [
                    'menu_id' => $dbMenu->id,
                    'qty' => $item['qty'],
                    'price' => $realPrice,
                    'subtotal' => $subtotal,
                ];
            }

            $order = Order::create([
                'invoice' => OrderService::generateInvoice(),
                'cafe_table_id' => $table->id,
                'customer_name' => $request->customer_name,
                'phone' => $request->phone,
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
                    'qty' => $vItem['qty'],
                    'price' => $vItem['price'],
                    'subtotal' => $vItem['subtotal'],
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