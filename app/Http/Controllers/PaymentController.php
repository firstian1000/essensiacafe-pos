<?php

namespace App\Http\Controllers;

use App\Models\Order;

class PaymentController extends Controller
{
    public function qris(Order $order)
    {
        abort_unless($order->payment_method === 'midtrans' && $order->snap_token, 404);

        $order->load(['table', 'items.menu']);

        return view('payments.qris', compact('order'));
    }

    public function midtrans(Order $order)
    {
        abort_unless($order->payment_method === 'midtrans' && $order->snap_token, 404);

        $order->load(['table', 'items.menu']);

        return view('payments.midtrans', compact('order'));
    }

    public function recap(Order $order)
    {
        $order->load(['table', 'items.menu']);

        return view('payments.recap', compact('order'));
    }

    public function receipt(Order $order)
    {
        $order->load(['table', 'items.menu']);
        return view('payments.receipt', compact('order'));
    }
}
