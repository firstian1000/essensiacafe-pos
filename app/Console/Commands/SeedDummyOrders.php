<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use App\Models\CafeTable;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SeedDummyOrders extends Command
{
    protected $signature = 'db:seed-dummy-orders';
    protected $description = 'Seed dummy orders and payments for dashboard testing';

    public function handle()
    {
        $this->info('Seeding dummy orders...');

        $menus = Menu::all();
        if ($menus->count() == 0) {
            $this->error('No menus found. Please sync from Aronium first.');
            return;
        }

        $tables = CafeTable::all();
        if ($tables->count() == 0) {
            // Create some tables if not exist
            for ($i = 1; $i <= 5; $i++) {
                CafeTable::create(['table_number' => 'Meja ' . $i, 'status' => 'available']);
            }
            $tables = CafeTable::all();
        }

        $statuses = ['completed' => 70, 'processing' => 15, 'pending' => 15]; // percentages
        $paymentStatuses = ['paid' => 75, 'pending' => 20, 'failed' => 5];
        $paymentMethods = ['cash' => 40, 'midtrans' => 60];

        $names = ['Budi', 'Siti', 'Agus', 'Rina', 'Joko', 'Ayu', 'Wati', 'Hendra', 'Dian', 'Toni'];

        // Generate 50 dummy orders over the past 7 days
        for ($i = 0; $i < 50; $i++) {
            $date = Carbon::now()->subDays(rand(0, 6))->subHours(rand(1, 10))->subMinutes(rand(1, 59));
            
            // Randomly select items (1 to 4 items)
            $orderMenus = $menus->random(rand(1, 4));
            
            $total = 0;
            $items = [];
            foreach ($orderMenus as $menu) {
                $qty = rand(1, 3);
                $subtotal = $menu->price * $qty;
                $total += $subtotal;
                $items[] = [
                    'menu_id' => $menu->id,
                    'qty' => $qty,
                    'price' => $menu->price,
                    'subtotal' => $subtotal,
                ];
            }

            // Random status based on weight
            $status = $this->weightedRandom($statuses);
            $paymentStatus = $this->weightedRandom($paymentStatuses);
            $paymentMethod = $this->weightedRandom($paymentMethods);
            
            // If processing or completed, payment is usually paid (but could be cash pending if at cashier, let's keep it simple)
            if ($status == 'completed' || $status == 'processing') {
                $paymentStatus = 'paid';
            }
            
            if ($paymentStatus == 'failed') {
                $status = 'pending';
            }

            $order = Order::create([
                'invoice' => 'INV/' . $date->format('Ymd') . '/' . str_pad($i + 1, 4, '0', STR_PAD_LEFT) . strtoupper(Str::random(3)),
                'cafe_table_id' => $tables->random()->id,
                'customer_name' => $names[array_rand($names)],
                'phone' => '0812' . rand(10000000, 99999999),
                'total' => $total,
                'status' => $status,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            foreach ($items as $item) {
                $item['order_id'] = $order->id;
                OrderItem::create($item);
            }
        }

        // Make sure we have some orders exactly TODAY for the "Hari Ini" stats
        for ($i = 0; $i < 10; $i++) {
            $date = Carbon::now()->subHours(rand(1, 10));
            $orderMenus = $menus->random(rand(1, 3));
            
            $total = 0;
            $items = [];
            foreach ($orderMenus as $menu) {
                $qty = rand(1, 2);
                $subtotal = $menu->price * $qty;
                $total += $subtotal;
                $items[] = [
                    'menu_id' => $menu->id,
                    'qty' => $qty,
                    'price' => $menu->price,
                    'subtotal' => $subtotal,
                ];
            }
            
            $status = $this->weightedRandom($statuses);
            $paymentStatus = $this->weightedRandom($paymentStatuses);
            $paymentMethod = $this->weightedRandom($paymentMethods);
            
            if ($status == 'completed' || $status == 'processing') {
                $paymentStatus = 'paid';
            }

            $order = Order::create([
                'invoice' => 'INV/' . $date->format('Ymd') . '/TDY' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'cafe_table_id' => $tables->random()->id,
                'customer_name' => 'Tamu Hari Ini ' . ($i+1),
                'phone' => '0812' . rand(10000000, 99999999),
                'total' => $total,
                'status' => $status,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            foreach ($items as $item) {
                $item['order_id'] = $order->id;
                OrderItem::create($item);
            }
        }

        $this->info('Successfully seeded 60 dummy orders!');
    }

    private function weightedRandom($weights)
    {
        $rand = rand(1, 100);
        $total = 0;
        foreach ($weights as $key => $weight) {
            $total += $weight;
            if ($rand <= $total) {
                return $key;
            }
        }
        return array_key_last($weights);
    }
}
