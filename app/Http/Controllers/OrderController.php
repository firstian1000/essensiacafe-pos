<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('table');

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('invoice', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhereHas('table', function ($table) use ($search) {

                        $table->where('table_number', 'like', "%{$search}%");

                  });

            });

        }

        $orders = $query->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load([
            'table',
            'items.menu'
        ]);

        return view('orders.show', compact('order'));
    }

    public function process(Order $order)
{
    $order->update([
        'status' => 'processing'
    ]);

    return redirect()
        ->route('orders.show', $order)
        ->with('success', 'Pesanan sedang diproses.');
}

public function complete(Order $order)
{
    $order->update([
        'status' => 'completed'
    ]);

    $this->checkAndResetTableStatus($order);

    return redirect()
        ->route('orders.show', $order)
        ->with('success', 'Pesanan selesai.');
}

public function paid(Order $order)
{
    $order->update([
        'payment_status' => 'paid',
        'status'         => 'completed',
    ]);

    $this->checkAndResetTableStatus($order);

    return redirect()
        ->route('payments.receipt', $order)
        ->with('success', 'Pembayaran berhasil dikonfirmasi.');
}

protected function checkAndResetTableStatus(Order $order)
{
    if ($order->cafe_table_id) {
        $hasActiveOrders = Order::where('cafe_table_id', $order->cafe_table_id)
            ->where('id', '!=', $order->id)
            ->where(function ($q) {
                $q->where('status', '!=', 'completed')
                  ->orWhere('payment_status', '!=', 'paid');
            })
            ->exists();

        if (!$hasActiveOrders && $order->table) {
            $order->table->update(['status' => 'available']);
        }
    }
}
}