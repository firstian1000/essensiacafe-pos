<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
    'invoice',
    'cafe_table_id',
    'customer_name',
    'phone',
    'service_type',
    'total',
    'status',
    'payment_method',
    'payment_status',
    'snap_token',
];

    public function table()
    {
        return $this->belongsTo(CafeTable::class,'cafe_table_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
