<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class CartController extends Controller
{
    public function add(Request $request)
    {
        if (\App\Models\Setting::isOrderingClosed()) {
            return back()->with('error', 'Pemesanan sudah ditutup untuk hari ini karena telah melewati batas jam operasional.');
        }

        $menu = Menu::findOrFail($request->menu_id);

        if (!$menu->status) {
            return back()->with('error', 'Menu ini sedang tidak tersedia / habis.');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$menu->id])) {

            $cart[$menu->id]['qty']++;

        } else {

            $cart[$menu->id] = [
                    'id'          => $menu->id,
                    'name'        => $menu->name,
                    'price'       => $menu->price,
                    'qty'         => 1,
                    'image'       => $menu->image,
                    'description' => $menu->description,
                    'category'    => optional($menu->category)->name,
                ];

        }

        session()->put('cart', $cart);

        return back()->with('success', 'Menu ditambahkan ke keranjang');
    }

public function index()
{
    $cart = session()->get('cart', []);
    $cartIds = array_keys($cart);
    $token = session('table_token');

    $recommendations = Menu::with('category')
        ->where('status', 1)
        ->where('is_recommended', true)
        ->when(! empty($cartIds), function ($query) use ($cartIds) {
            $query->whereNotIn('id', $cartIds);
        })
        ->latest()
        ->limit(4)
        ->get();

    return view('customer.cart', compact('cart','token','recommendations'));
}

    public function increase($id)
{
    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        $cart[$id]['qty']++;
    }

    session()->put('cart', $cart);

    return back();
}

public function decrease($id)
{
    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {

        if ($cart[$id]['qty'] > 1) {

            $cart[$id]['qty']--;

        } else {

            unset($cart[$id]);

        }

    }

    session()->put('cart', $cart);

    return back();
}

public function remove($id)
{
    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {

        unset($cart[$id]);

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
}
