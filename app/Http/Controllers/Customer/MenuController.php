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

    $menus = Menu::with('category')
        ->orderByDesc('is_recommended')
        ->orderBy('name')
        ->get();

    $categories = \App\Models\Category::all();

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