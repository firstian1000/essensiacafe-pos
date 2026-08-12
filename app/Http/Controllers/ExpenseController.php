<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        if (! Schema::hasTable('expenses')) {
            return view('expenses.migration-required');
        }

        $perPage = (int) $request->get('per_page', 15);
        if (! in_array($perPage, [10, 15, 20, 25], true)) {
            $perPage = 15;
        }

        $query = Expense::query()
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('category', 'like', '%'.$request->search.'%')
                        ->orWhere('note', 'like', '%'.$request->search.'%');
                });
            });

        $totalExpense = (clone $query)->sum('amount');

        $expenses = $query
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->paginate($perPage);

        return view('expenses.index', compact('expenses', 'totalExpense'));
    }

    public function create()
    {
        if (! Schema::hasTable('expenses')) {
            return view('expenses.migration-required');
        }

        return view('expenses.create');
    }

    public function store(Request $request)
    {
        if (! Schema::hasTable('expenses')) {
            return redirect()->route('expenses.index');
        }

        Expense::create($this->validatedData($request));

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    public function edit(Expense $expense)
    {
        if (! Schema::hasTable('expenses')) {
            return view('expenses.migration-required');
        }

        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        if (! Schema::hasTable('expenses')) {
            return redirect()->route('expenses.index');
        }

        $expense->update($this->validatedData($request));

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Expense $expense)
    {
        if (! Schema::hasTable('expenses')) {
            return redirect()->route('expenses.index');
        }

        $expense->delete();

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'expense_date' => ['required', 'date'],
            'name' => ['required', 'in:Gaji Karyawan,Lain-lain'],
            'category' => ['required', 'in:Internal,External'],
            'amount' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);
    }
}
