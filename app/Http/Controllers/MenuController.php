<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    private function areaQuery(): array
    {
        return request('area') === 'cashier' || auth()->user()?->role === 'cashier'
            ? ['area' => 'cashier']
            : [];
    }

    public function index(Request $request)
{
    $perPage = (int) $request->get('per_page', 15);
    if (!in_array($perPage, [10, 15, 20, 25])) {
        $perPage = 15;
    }

    $menus = Menu::with('category')

        ->when($request->search, function ($query) use ($request) {

            $query->where('name', 'like', '%'.$request->search.'%');

        })

        ->orderByDesc('is_recommended')
        ->paginate($perPage);

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
        'allow_ice' => 'nullable|boolean',
        'allow_hot' => 'nullable|boolean',
        'variants' => 'nullable|array',
        'variants.*.name' => 'nullable|string|max:100',
        'variants.*.price' => 'nullable|numeric|min:0',
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

    [$allowIce, $allowHot] = $this->temperatureOptions($request);

    $menu = Menu::create([
        'category_id' => $request->category_id,
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'image' => $image,
        'status' => 1,
        'is_recommended' => $request->boolean('is_recommended'),
        'allow_ice' => $allowIce,
        'allow_hot' => $allowHot,
    ]);

    $this->syncVariants($menu, $request->input('variants', []));

    return redirect()->route('menus.index', $this->areaQuery())
        ->with('success', 'Menu berhasil ditambahkan');
}

public function edit(Menu $menu)
{
    $menu->load('variants');
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
        'allow_ice' => 'nullable|boolean',
        'allow_hot' => 'nullable|boolean',
        'variants' => 'nullable|array',
        'variants.*.name' => 'nullable|string|max:100',
        'variants.*.price' => 'nullable|numeric|min:0',
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

    [$allowIce, $allowHot] = $this->temperatureOptions($request);

    $menu->update([

        'category_id' => $request->category_id,
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'image' => $image,
        'is_recommended' => $request->boolean('is_recommended'),
        'allow_ice' => $allowIce,
        'allow_hot' => $allowHot,

    ]);

    $this->syncVariants($menu, $request->input('variants', []));

    return redirect()
        ->route('menus.index', $this->areaQuery())
        ->with('success', 'Menu berhasil diperbarui');
}
public function destroy(Menu $menu)
{
    if ($menu->image && Storage::disk('public')->exists($menu->image)) {

        Storage::disk('public')->delete($menu->image);

    }

    $menu->delete();

    return redirect()
        ->route('menus.index', $this->areaQuery())
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

private function syncVariants(Menu $menu, array $variants): void
{
    $menu->variants()->delete();

    foreach ($variants as $variant) {
        $name = trim((string) ($variant['name'] ?? ''));
        $price = $variant['price'] ?? null;

        if ($name === '' || $price === null || $price === '') {
            continue;
        }

        $menu->variants()->create([
            'name' => $name,
            'price' => (int) $price,
            'status' => true,
        ]);
    }
}

private function temperatureOptions(Request $request): array
{
    $allowIce = $request->boolean('allow_ice');
    $allowHot = $request->boolean('allow_hot');

    if (! $allowIce && ! $allowHot) {
        $allowIce = true;
    }

    return [$allowIce, $allowHot];
}

}
