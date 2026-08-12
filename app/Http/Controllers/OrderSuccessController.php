<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderSuccessController extends Controller
{
    public function index(Order $order)
    {
        $order->load(['table', 'items.menu.category']);

        return view('customer.succes.success', compact('order'));
    }

    public function status(Order $order)
    {
        return response()->json([
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'is_cancelled' => $order->status === 'cancelled',
            'updated_at' => optional($order->updated_at)->format('d M Y - H:i'),
        ]);
    }
}
