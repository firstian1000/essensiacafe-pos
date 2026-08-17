<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Notification;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        Log::info('===== CALLBACK MIDTRANS MASUK =====', $request->all());

        try {
            $notification = new Notification();
        } catch (\Throwable $exception) {
            Log::error('Callback Midtrans tidak valid', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Callback tidak valid',
            ], 400);
        }

        $transaction = $notification->transaction_status;
        $orderId = $notification->order_id;
        $fraud = $notification->fraud_status ?? null;

        $order = Order::where('invoice', $orderId)->first();

        if (! $order) {
            return response()->json([
                'message' => 'Order tidak ditemukan',
            ], 404);
        }

        if ($transaction === 'capture' && $fraud === 'accept') {
            $order->update([
                'payment_status' => 'paid',
                'status' => $order->status === 'completed' ? 'completed' : 'processing',
            ]);
        } elseif ($transaction === 'settlement') {
            $order->update([
                'payment_status' => 'paid',
                'status' => $order->status === 'completed' ? 'completed' : 'processing',
            ]);
        } elseif ($transaction === 'pending') {
            $order->update([
                'payment_status' => 'pending',
            ]);
        } elseif (in_array($transaction, ['deny', 'cancel'], true)) {
            $order->update([
                'payment_status' => 'failed',
            ]);
        } elseif ($transaction === 'expire') {
            $order->update([
                'payment_status' => 'expired',
            ]);
        }

        return response()->json([
            'message' => 'OK',
        ]);
    }
}
