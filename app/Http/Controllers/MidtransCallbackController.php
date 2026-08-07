<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class MidtransCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $notif = json_decode($request->getContent());

        $order = Order::where('invoice', $notif->order_id)->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order tidak ditemukan'
            ]);
        }

        if ($notif->transaction_status == 'settlement') {

            $order->update([
                'payment_status' => 'paid'
            ]);

        } elseif ($notif->transaction_status == 'expire') {

            $order->update([
                'payment_status' => 'expired'
            ]);

        } elseif ($notif->transaction_status == 'cancel') {

            $order->update([
                'payment_status' => 'failed'
            ]);

        }

        return response()->json([
            'success' => true
        ]);
    }
}