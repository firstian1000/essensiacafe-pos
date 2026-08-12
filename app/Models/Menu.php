<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'status',
        'stock',
        'is_recommended',
        'allow_ice',
        'allow_hot',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_recommended' => 'boolean',
        'allow_ice' => 'boolean',
        'allow_hot' => 'boolean',
        'stock' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function variants()
    {
        return $this->hasMany(MenuVariant::class);
    }
}
