<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StockController extends Controller
{
    public function index(Request $request)
    {
        if (! Schema::hasTable('stock_items')) {
            return view('stocks.migration-required');
        }

        $perPage = (int) $request->get('per_page', 15);
        if (! in_array($perPage, [10, 15, 20, 25], true)) {
            $perPage = 15;
        }

        $stockItems = StockItem::query()
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->search.'%');
            })
            ->orderBy('name')
            ->paginate($perPage);

        return view('stocks.index', compact('stockItems'));
    }

    public function create()
    {
        if (! Schema::hasTable('stock_items')) {
            return view('stocks.migration-required');
        }

        $stockNames = $this->stockNames();

        return view('stocks.create', compact('stockNames'));
    }

    public function store(Request $request)
    {
        if (! Schema::hasTable('stock_items')) {
            return redirect()->route('stocks.index');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'new_name' => ['required_if:name,__new__', 'nullable', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:0'],
            'purchase_amount' => ['required', 'integer', 'min:0'],
        ]);

        $name = $data['name'] === '__new__' ? trim((string) ($data['new_name'] ?? '')) : $data['name'];
        if ($name === '') {
            return back()
                ->withInput()
                ->withErrors(['new_name' => 'Nama stok baru wajib diisi.']);
        }

        $stockItem = StockItem::updateOrCreate(['name' => $name], [
            'stock' => $data['stock'] ?? null,
            'status' => $data['stock'] !== 0,
        ]);

        $this->recordStockExpense($stockItem, (int) ($data['purchase_amount'] ?? 0));

        return redirect()
            ->route('stocks.index')
            ->with('success', 'Stok berhasil disimpan.');
    }

    public function edit(StockItem $stock)
    {
        if (! Schema::hasTable('stock_items')) {
            return view('stocks.migration-required');
        }

        $stockNames = $this->stockNames($stock->name);

        return view('stocks.edit', ['stockItem' => $stock, 'stockNames' => $stockNames]);
    }

    public function update(Request $request, StockItem $stock)
    {
        if (! Schema::hasTable('stock_items')) {
            return redirect()->route('stocks.index');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'new_name' => ['required_if:name,__new__', 'nullable', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'boolean'],
            'purchase_amount' => ['required', 'integer', 'min:0'],
        ]);

        $name = $data['name'] === '__new__' ? trim((string) ($data['new_name'] ?? '')) : $data['name'];
        if ($name === '') {
            return back()
                ->withInput()
                ->withErrors(['new_name' => 'Nama stok baru wajib diisi.']);
        }

        $stock->update([
            'name' => $name,
            'stock' => $data['stock'],
            'status' => (bool) $data['status'],
        ]);

        $this->recordStockExpense($stock, (int) ($data['purchase_amount'] ?? 0));

        return redirect()
            ->route('stocks.index')
            ->with('success', 'Stok berhasil diperbarui.');
    }

    public function destroy(StockItem $stock)
    {
        if (! Schema::hasTable('stock_items')) {
            return redirect()->route('stocks.index');
        }

        $stock->delete();

        return redirect()
            ->route('stocks.index')
            ->with('success', 'Stok berhasil dihapus.');
    }

    private function recordStockExpense(StockItem $stockItem, int $amount): void
    {
        if ($amount <= 0 || ! Schema::hasTable('expenses')) {
            return;
        }

        Expense::create([
            'expense_date' => now()->toDateString(),
            'name' => 'Lain-lain',
            'category' => 'Internal',
            'amount' => $amount,
            'note' => 'Pembelian stok '.$stockItem->name.'. Dicatat otomatis dari menu Stok internal admin.',
        ]);
    }

    private function stockNames(?string $currentName = null): array
    {
        $names = ['Susu Diamond', 'Matcha', 'Choco', 'Kopi', 'Air Mineral', 'Galon'];

        if (Schema::hasTable('stock_items')) {
            $names = array_merge($names, StockItem::orderBy('name')->pluck('name')->all());
        }

        if ($currentName) {
            $names[] = $currentName;
        }

        return array_values(array_unique($names));
    }
}
