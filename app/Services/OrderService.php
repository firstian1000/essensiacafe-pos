<?php

namespace App\Services;

class OrderService
{
    public static function generateInvoice()
    {
        return 'INV-' . now()->format('YmdHis');
    }
}