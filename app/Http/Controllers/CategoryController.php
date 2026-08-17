<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private function areaQuery(): array
    {
        return request('area') === 'cashier' || auth()->user()?->role === 'cashier'
            ? ['area' => 'cashier']
            : [];
    }

    // Menampilkan daftar kategori
    public function index(Request $request)
{
    $query = Category::query();

    // Search
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Filter Status
    if ($request->status !== null && $request->status !== '') {
        $query->where('status', $request->status);
    }

    $categories = $query->latest()->paginate(8);

    return view('categories.index', compact('categories'));
}

    // Menampilkan form tambah kategori
    public function create()
    {
        return view('categories.create');
    }

    // Menyimpan kategori baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => true
        ]);

        return redirect()->route('categories.index', $this->areaQuery())
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    // Menampilkan form edit
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    // Menyimpan hasil edit
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()->route('categories.index', $this->areaQuery())
            ->with('success', 'Kategori berhasil diupdate');
    }

    // Menghapus kategori
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index', $this->areaQuery())
            ->with('success', 'Kategori berhasil dihapus');
    }
}
