<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Notification;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    // public function callback(Request $request)
    // {
    //     $notification = new Notification();

    //     $transaction = $notification->transaction_status;
    //     $orderId = $notification->order_id;
    //     $fraud = $notification->fraud_status;

    //     $order = Order::where('invoice', $orderId)->first();

    //     if (!$order) {
    //         return response()->json([
    //             'message' => 'Order tidak ditemukan'
    //         ], 404);
    //     }

    //     if ($transaction == 'capture') {

    //         if ($fraud == 'accept') {

    //             $order->update([
    //                 'payment_status' => 'paid',
    //                 'status' => 'processing',
    //             ]);

    //         }

    //     } elseif ($transaction == 'settlement') {

    //         $order->update([
    //             'payment_status' => 'paid',
    //             'status' => 'processing',
    //         ]);

    //     } elseif ($transaction == 'pending') {

    //         $order->update([
    //             'payment_status' => 'pending',
    //         ]);

    //     } elseif (in_array($transaction, ['deny', 'expire', 'cancel'])) {

    //         $order->update([
    //             'payment_status' => 'failed',
    //         ]);
    //     }

    //     return response()->json([
    //         'message' => 'OK'
    //     ]);
    // }

 public function callback(Request $request)
{
    Log::info('===== CALLBACK MASUK =====');
    Log::info($request->all());

    return response()->json([
        'success' => true
    ]);
}
}