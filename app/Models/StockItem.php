<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    protected $fillable = [
        'name',
        'stock',
        'status',
    ];

    protected $casts = [
        'stock' => 'integer',
        'status' => 'boolean',
    ];
}
