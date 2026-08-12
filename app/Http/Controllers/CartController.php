<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\Category;

class CartController extends Controller
{
    public function add(Request $request)
    {
        if (\App\Models\Setting::isOrderingClosed()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan sudah ditutup untuk hari ini karena telah melewati batas jam operasional.',
                ], 422);
            }

            return back()->with('error', 'Pemesanan sudah ditutup untuk hari ini karena telah melewati batas jam operasional.');
        }

        $menu = Menu::with(['category', 'variants'])->findOrFail($request->menu_id);

        if (!$menu->status) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu ini sedang tidak tersedia / habis.',
                ], 422);
            }

            return back()->with('error', 'Menu ini sedang tidak tersedia / habis.');
        }

        $cart = session()->get('cart', []);
        $variant = null;


        if ($request->filled('variant_id')) {
            $variant = MenuVariant::where('menu_id', $menu->id)
                ->where('status', true)
                ->findOrFail($request->variant_id);
        }

        $cartKey = $variant ? $menu->id.'-'.$variant->id : (string) $menu->id;

        if (isset($cart[$cartKey])) {

            $cart[$cartKey]['qty']++;

        } else {
            $disableDrinkOptions = $this->shouldDisableDrinkOptions(optional($menu->category)->name);
            $defaultTemperature = ($menu->allow_ice ?? true) ? 'ice' : 'hot';

            $cart[$cartKey] = [
                    'id'          => $menu->id,
                    'cart_key'    => $cartKey,
                    'variant_id'  => $variant?->id,
                    'variant_name'=> $variant?->name,
                    'name'        => $menu->name,
                    'price'       => $variant?->price ?? $menu->price,
                    'qty'         => 1,
                    'image'       => $menu->image,
                    'description' => $menu->description,
                    'category'    => optional($menu->category)->name,
                    'sugar_level' => 'normal',
                    'allow_ice'   => $menu->allow_ice ?? true,
                    'allow_hot'   => $menu->allow_hot ?? true,
                    'temperature' => $disableDrinkOptions ? 'ice' : $defaultTemperature,
                    'ice_level'   => 'normal',
                    'add_on'      => '',
                    'add_on_menu_id' => null,
                    'add_on_price' => 0,
                    'note'        => '',
                ];

        }

        session()->put('cart', $cart);

        if ($request->expectsJson()) {
            $cartItems = array_values($cart);

            return response()->json([
                'success' => true,
                'message' => 'Menu ditambahkan ke keranjang',
                'cart_count' => collect($cart)->sum('qty'),
                'cart_total' => collect($cart)->sum(fn ($item) => ((int) $item['price'] + (int) ($item['add_on_price'] ?? 0)) * $item['qty']),
                'preview_items' => array_slice($cartItems, 0, 3),
                'cart_items' => $cartItems,
            ]);
        }

        return back()->with('success', 'Menu ditambahkan ke keranjang');
    }

public function restore(Request $request)
{
    $items = $request->input('cart', []);

    if (! is_array($items) || empty($items)) {
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada data keranjang untuk dipulihkan.',
        ], 422);
    }

    $cart = [];

    foreach ($items as $item) {
        if (! is_array($item) || empty($item['id'])) {
            continue;
        }

        $menu = Menu::with(['category', 'variants'])->where('status', 1)->find($item['id']);
        if (! $menu) {
            continue;
        }

        $variant = null;
        if (! empty($item['variant_id'])) {
            $variant = $menu->variants
                ->where('status', true)
                ->firstWhere('id', (int) $item['variant_id']);
        }

        $cartKey = $variant ? $menu->id.'-'.$variant->id : (string) $menu->id;
        $qty = max(1, (int) ($item['qty'] ?? 1));
        $disableDrinkOptions = $this->shouldDisableDrinkOptions(optional($menu->category)->name);
        $defaultTemperature = ($menu->allow_ice ?? true) ? 'ice' : 'hot';

        $cart[$cartKey] = [
            'id' => $menu->id,
            'cart_key' => $cartKey,
            'variant_id' => $variant?->id,
            'variant_name' => $variant?->name,
            'name' => $menu->name,
            'price' => $variant?->price ?? $menu->price,
            'qty' => $qty,
            'image' => $menu->image,
            'description' => $menu->description,
            'category' => optional($menu->category)->name,
            'sugar_level' => $disableDrinkOptions ? 'normal' : ($item['sugar_level'] ?? 'normal'),
            'allow_ice' => $menu->allow_ice ?? true,
            'allow_hot' => $menu->allow_hot ?? true,
            'temperature' => $disableDrinkOptions ? 'ice' : ($item['temperature'] ?? $defaultTemperature),
            'ice_level' => $disableDrinkOptions ? 'normal' : ($item['ice_level'] ?? 'normal'),
            'add_on' => '',
            'add_on_menu_id' => null,
            'add_on_price' => 0,
            'note' => $item['note'] ?? '',
        ];
    }

    if (empty($cart)) {
        return response()->json([
            'success' => false,
            'message' => 'Keranjang tidak bisa dipulihkan.',
        ], 422);
    }

    session()->put('cart', $cart);

    return response()->json([
        'success' => true,
        'message' => 'Keranjang berhasil dipulihkan.',
    ]);
}

public function index()
{
    $cart = session()->get('cart', []);
    $cartIds = collect($cart)->pluck('id')->all();
    $token = session('table_token');
    $variantMenus = Menu::with(['variants' => function ($query) {
            $query->where('status', true)->orderBy('name');
        }])
        ->whereIn('id', $cartIds)
        ->get()
        ->keyBy('id');

    $addOns = Menu::with('category')
        ->where('status', 1)
        ->whereHas('category', function ($query) {
            $query->whereRaw('LOWER(name) IN (?, ?)', ['add on', 'addon']);
        })
        ->orderBy('name')
        ->get();

    $recommendations = Menu::with('category')
        ->where('status', 1)
        ->whereDoesntHave('category', function ($query) {
            $query->whereRaw('LOWER(name) IN (?, ?)', ['add on', 'addon']);
        })
        ->where('is_recommended', true)
        ->when(! empty($cartIds), function ($query) use ($cartIds) {
            $query->whereNotIn('id', $cartIds);
        })
        ->latest()
        ->limit(4)
        ->get();

    return view('customer.cart', compact('cart','token','recommendations','addOns','variantMenus'));
}

    public function increase($id)
{
    $cart = session()->get('cart', []);
    $key = $this->resolveCartKey($cart, $id);

    if ($key !== null) {
        $cart[$key]['qty']++;
    }

    session()->put('cart', $cart);

    return back();
}

public function decrease($id)
{
    $cart = session()->get('cart', []);
    $key = $this->resolveCartKey($cart, $id);

    if ($key !== null) {

        if ($cart[$key]['qty'] > 1) {

            $cart[$key]['qty']--;

        } else {

            unset($cart[$key]);

        }

    }

    session()->put('cart', $cart);

    return back();
}

public function remove($id)
{
    $cart = session()->get('cart', []);
    $key = $this->resolveCartKey($cart, $id);

    if ($key !== null) {

        unset($cart[$key]);

    }

    session()->put('cart', $cart);

    return back();
}

public function clear()
{
    session()->forget('cart');

    return redirect()->route('cart.index')
        ->with('success', 'Keranjang berhasil dikosongkan.');
}

public function updateServiceType(Request $request)
{
    $data = $request->validate([
        'service_type' => ['required', 'in:dine_in,take_away'],
    ]);

    session()->put('service_type', $data['service_type']);

    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Pilihan layanan berhasil disimpan.',
        ]);
    }

    return back()->with('success', 'Pilihan layanan berhasil disimpan.');
}

public function updateOptions(Request $request, $id)
{
    $data = $request->validate([
        'variant_id' => ['nullable', 'exists:menu_variants,id'],
        'sugar_level' => ['required', 'in:normal,less,no'],
        'temperature' => ['required', 'in:ice,hot'],
        'ice_level' => ['required', 'in:normal,less'],
        'add_on_menu_id' => ['nullable', 'exists:menus,id'],
        'note' => ['nullable', 'string', 'max:255'],
    ]);

    $cart = session()->get('cart', []);

    if (! isset($cart[$id])) {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan di keranjang.',
            ], 404);
        }

        return back()->with('error', 'Item tidak ditemukan di keranjang.');
    }

    $menu = Menu::with(['variants' => function ($query) {
            $query->where('status', true);
        }])
        ->find($cart[$id]['id']);

    $variant = null;
    if ($menu && ! empty($data['variant_id'])) {
        $variant = $menu->variants->firstWhere('id', (int) $data['variant_id']);

        if (! $variant) {
            return response()->json([
                'success' => false,
                'message' => 'Varian tidak tersedia untuk menu ini.',
            ], 422);
        }
    }

    $allowIce = $cart[$id]['allow_ice'] ?? true;
    $allowHot = $cart[$id]['allow_hot'] ?? true;
    $disableDrinkOptions = $this->shouldDisableDrinkOptions($cart[$id]['category'] ?? null);
    $canUseAddOn = $this->canUseAddOn($cart[$id]['category'] ?? null);

    if ($disableDrinkOptions) {
        $data['sugar_level'] = 'normal';
        $data['temperature'] = 'ice';
        $data['ice_level'] = 'normal';
    } elseif (($data['temperature'] === 'ice' && ! $allowIce) || ($data['temperature'] === 'hot' && ! $allowHot)) {
        return response()->json([
            'success' => false,
            'message' => 'Pilihan temperature tidak tersedia untuk menu ini.',
        ], 422);
    }

    $addOn = null;
    if ($canUseAddOn && ! empty($data['add_on_menu_id'])) {
        $addOn = Menu::with('category')
            ->where('status', 1)
            ->whereHas('category', function ($query) {
                $query->whereRaw('LOWER(name) IN (?, ?)', ['add on', 'addon']);
            })
            ->findOrFail($data['add_on_menu_id']);
    }

    $cart[$id]['sugar_level'] = $data['sugar_level'];
    $cart[$id]['temperature'] = $data['temperature'];
    $cart[$id]['ice_level'] = $data['ice_level'];
    $cart[$id]['variant_id'] = $variant?->id;
    $cart[$id]['variant_name'] = $variant?->name;
    $cart[$id]['price'] = $variant ? (int) $variant->price : (int) ($menu?->price ?? $cart[$id]['price']);
    $cart[$id]['add_on'] = $addOn?->name ?? '';
    $cart[$id]['add_on_menu_id'] = $addOn?->id;
    $cart[$id]['add_on_price'] = $addOn ? (int) $addOn->price : 0;
    $cart[$id]['note'] = $data['note'] ?? '';

    $newCartKey = $variant ? $cart[$id]['id'].'-'.$variant->id : (string) $cart[$id]['id'];
    $cart[$id]['cart_key'] = $newCartKey;

    if ($newCartKey !== (string) $id) {
        if (isset($cart[$newCartKey])) {
            $cart[$newCartKey]['qty'] += $cart[$id]['qty'];
            $cart[$newCartKey]['sugar_level'] = $cart[$id]['sugar_level'];
            $cart[$newCartKey]['temperature'] = $cart[$id]['temperature'];
            $cart[$newCartKey]['ice_level'] = $cart[$id]['ice_level'];
            $cart[$newCartKey]['add_on'] = $cart[$id]['add_on'];
            $cart[$newCartKey]['add_on_menu_id'] = $cart[$id]['add_on_menu_id'];
            $cart[$newCartKey]['add_on_price'] = $cart[$id]['add_on_price'];
            $cart[$newCartKey]['note'] = $cart[$id]['note'];
            unset($cart[$id]);
        } else {
            $cart[$newCartKey] = $cart[$id];
            unset($cart[$id]);
        }
    }

    session()->put('cart', $cart);

    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Pilihan menu berhasil disimpan.',
        ]);
    }

    return back()->with('success', 'Pilihan menu berhasil disimpan.');
}

private function shouldDisableDrinkOptions(?string $category): bool
{
    return in_array(strtolower(trim((string) $category)), [
        'snack',
        'snacks',
        'dimsum',
        'main course',
        'main cource',
        'add on',
        'addon',
    ], true);
}

private function canUseAddOn(?string $category): bool
{
    return in_array(strtolower(trim((string) $category)), [
        'main course',
        'main cource',
    ], true);
}

private function resolveCartKey(array $cart, string|int $id): string|int|null
{
    if (isset($cart[$id])) {
        return $id;
    }

    foreach ($cart as $key => $item) {
        if ((string) ($item['cart_key'] ?? '') === (string) $id) {
            return $key;
        }

        if ((string) ($item['id'] ?? '') === (string) $id) {
            return $key;
        }
    }

    return null;
}
}
