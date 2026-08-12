<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'expense_date',
        'name',
        'category',
        'amount',
        'note',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'integer',
    ];
}
