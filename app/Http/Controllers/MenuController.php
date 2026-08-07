<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
{
    $menus = Menu::with('category')

        ->when($request->search, function ($query) use ($request) {

            $query->where('name', 'like', '%'.$request->search.'%');

        })

        ->orderByDesc('is_recommended')
        ->paginate(10);

    return view('menus.index', compact('menus'));
}

    public function create()
    {
        $categories = Category::all();

        return view('menus.create', compact('categories'));
    }

    public function store(Request $request)
{
    $request->validate([
        'category_id' => 'required',
        'name' => 'required',
        'price' => 'required|numeric',
        'description' => 'nullable',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'is_recommended' => 'nullable|boolean',
    ]);

    $image = null;

if ($request->hasFile('image')) {

    $extension = $request->image->getClientOriginalExtension();

    $filename =
        Str::slug($request->name)
        . '-'
        . now()->format('YmdHis')
        . '.'
        . $extension;

    $image = $request->image->storeAs(
        'menus',
        $filename,
        'public'
    );

}

    Menu::create([
        'category_id' => $request->category_id,
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'image' => $image,
        'status' => 1,
        'is_recommended' => $request->boolean('is_recommended'),
    ]);

    return redirect()->route('menus.index')
        ->with('success', 'Menu berhasil ditambahkan');
}

public function edit(Menu $menu)
{
    $categories = Category::all();

    return view('menus.edit', compact('menu', 'categories'));
}

public function update(Request $request, Menu $menu)
{
    $request->validate([
        'category_id' => 'required',
        'name' => 'required',
        'price' => 'required|numeric',
        'description' => 'nullable',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'is_recommended' => 'nullable|boolean',
    ]);

    $image = $menu->image;

    if ($request->hasFile('image')) {

        // hapus gambar lama
        if ($menu->image && Storage::disk('public')->exists($menu->image)) {

            Storage::disk('public')->delete($menu->image);

        }

        $extension = $request->image->getClientOriginalExtension();

        $filename =
            Str::slug($request->name)
            . '-'
            . now()->format('YmdHis')
            . '.'
            . $extension;

        $image = $request->image->storeAs(
            'menus',
            $filename,
            'public'
        );
    }

    $menu->update([

        'category_id' => $request->category_id,
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'image' => $image,
        'is_recommended' => $request->boolean('is_recommended'),

    ]);

    return redirect()
        ->route('menus.index')
        ->with('success', 'Menu berhasil diperbarui');
}
public function destroy(Menu $menu)
{
    if ($menu->image && Storage::disk('public')->exists($menu->image)) {

        Storage::disk('public')->delete($menu->image);

    }

    $menu->delete();

    return redirect()
        ->route('menus.index')
        ->with('success', 'Menu berhasil dihapus');
}

public function updateStatus(Request $request, Menu $menu)
{
    $request->validate([
        'status' => 'required|boolean',
    ]);

    $menu->update([
        'status' => $request->status
    ]);

    return back()->with('success', 'Status menu berhasil diperbarui.');
}

public function updateRecommendation(Request $request, Menu $menu)
{
    $request->validate([
        'is_recommended' => 'required|boolean',
    ]);

    $menu->update([
        'is_recommended' => $request->boolean('is_recommended'),
    ]);

    return back()->with('success', 'Rekomendasi menu berhasil diperbarui.');
}

}
