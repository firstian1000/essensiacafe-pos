<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderSuccessController extends Controller
{
    public function index(Order $order)
    {
        $order->load(['table', 'items.menu.category']);

        return view('customer.succes.success', compact('order'));
    }
}