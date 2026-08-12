<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CafeTable;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index($token)
{
    $table = CafeTable::where('qr_token',$token)->firstOrFail();

    $menus = Menu::with(['category', 'variants' => fn ($query) => $query->where('status', true)->orderBy('price')])
        ->where('status', 1)
        ->orderByDesc('is_recommended')
        ->orderBy('name')
        ->get();

    $categories = \App\Models\Category::where('status', 1)->orderBy('name')->get();

    session([
    'table_id'     => $table->id,
    'table_token'  => $token,
    'table_number' => $table->table_number,
]);

    return view(
        'customer.menu.menu',
        compact(
            'menus',
            'categories',
            'table',
            'token'
        )
    );
}
}
