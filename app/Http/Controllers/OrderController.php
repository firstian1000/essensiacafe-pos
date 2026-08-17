<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()?->role === 'cashier' && $request->query('area') !== 'cashier') {
            return redirect()->route('orders.index', array_merge($request->query(), ['area' => 'cashier']));
        }

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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
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
        $this->ensureCashierCanUpdateStatus();

        $order->update([
            'status' => 'processing'
        ]);

        return redirect()
            ->back()
            ->with('success', 'Pesanan #' . $order->invoice . ' sedang diproses dapur/bar.');
    }

    public function unprocess(Order $order)
    {
        $this->ensureCashierCanUpdateStatus();

        $order->update([
            'status' => 'pending'
        ]);

        return redirect()
            ->back()
            ->with('success', 'Status pesanan #' . $order->invoice . ' dikembalikan ke Pending.');
    }

    public function complete(Order $order)
    {
        $this->ensureCashierCanUpdateStatus();

        $order->update([
            'status' => 'completed'
        ]);

        $this->checkAndResetTableStatus($order);

        return redirect()
            ->back()
            ->with('success', 'Pesanan #' . $order->invoice . ' telah selesai dan diantar ke meja.');
    }

    public function cancel(Request $request, Order $order)
    {
        $this->ensureCashierCanUpdateStatus();

        $data = $request->validate([
            'cancel_reason' => ['required', 'in:Ganti Pesanan,Ganti Pembayaran,Lain lain'],
        ]);

        $order->update([
            'status' => 'cancelled',
            'payment_status' => $order->payment_status === 'paid' ? 'paid' : 'failed',
            'cancel_reason' => $data['cancel_reason'],
        ]);

        $this->checkAndResetTableStatus($order);

        return redirect()
            ->back()
            ->with('success', 'Pesanan #' . $order->invoice . ' berhasil dibatalkan. Alasan: ' . $data['cancel_reason'] . '.');
    }

    public function paid(Order $order)
    {
        $this->ensureCashierCanUpdateStatus();
        $area = request('area') === 'cashier' || auth('cashier')->check() ? 'cashier' : 'admin';

        $order->update([
            'payment_status' => 'paid',
        ]);

        return redirect()
            ->route('orders.index', ['area' => $area])
            ->with('success', 'Pembayaran cash untuk pesanan #' . $order->invoice . ' berhasil dikonfirmasi lunas.');
    }

    protected function ensureCashierCanUpdateStatus(): void
    {
        if (! auth('cashier')->check() && auth()->user()?->role !== 'cashier') {
            abort(403);
        }
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

    public function checkNew(Request $request)
    {
        $lastId = (int) $request->get('last_id', 0);

        $newOrders = Order::with('table')
            ->where('id', '>', $lastId)
            ->orderBy('id', 'asc')
            ->get();

        if ($newOrders->isEmpty()) {
            $maxId = Order::max('id') ?: 0;
            return response()->json([
                'has_new' => false,
                'latest_id' => max($lastId, $maxId),
                'orders' => []
            ]);
        }

        $latestId = $newOrders->last()->id;

        $formattedOrders = $newOrders->map(function ($order) {
            $tableNum = $order->table ? $order->table->table_number : null;
            $tableLabel = $tableNum ? "Meja {$tableNum}" : "Kasir Take Away";
            $speechText = $tableNum 
                ? "Pesanan dari meja nomor {$tableNum} telah masuk" 
                : "Pesanan baru di kasir telah masuk";

            return [
                'id' => $order->id,
                'invoice' => $order->invoice ?? ('#' . $order->id),
                'customer_name' => $order->customer_name ?: 'Pelanggan',
                'table_number' => $tableNum,
                'table_label' => $tableLabel,
                'total_amount' => 'Rp ' . number_format($order->total ?? 0, 0, ',', '.'),
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'time' => $order->created_at ? $order->created_at->format('H:i') : date('H:i'),
                'speech_text' => $speechText,
                'url' => route('orders.show', ['order' => $order->id, 'area' => 'cashier']),
            ];
        });

        return response()->json([
            'has_new' => true,
            'latest_id' => $latestId,
            'orders' => $formattedOrders
        ]);
    }
}
