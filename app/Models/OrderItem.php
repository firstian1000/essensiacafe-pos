<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'menu_id',
        'menu_variant_id',
        'variant_name',
        'qty',
        'price',
        'subtotal',
        'sugar_level',
        'temperature',
        'ice_level',
        'add_on',
        'add_on_menu_id',
        'add_on_price',
        'note',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
