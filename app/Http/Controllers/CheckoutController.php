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

        if (! session('table_id')) {
            return redirect()->route('cart.index')
                ->with('error', 'Silakan scan QR meja terlebih dahulu.');
        }

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang kosong.');
        }

        DB::beginTransaction();

        try {

            $total = 0;

            foreach ($cart as $item) {
                $total += $item['price'] * $item['qty'];
            }

            $order = Order::create([
                'invoice' => OrderService::generateInvoice(),
                'cafe_table_id' => session('table_id'),
                'customer_name' => $request->customer_name,
                'phone' => $request->phone,
                'total' => $total,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
            ]);

            // Simpan item pesanan
            foreach ($cart as $item) {

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
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