<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Menu;
use App\Models\CafeTable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        $user = User::firstOrCreate(
            ['email' => 'admin@cafe.com'],
            [
                'name' => 'Admin Cafe',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );
        $user->update(['role' => 'admin']);

        User::firstOrCreate(
            ['email' => 'kasir@essensia.test'],
            [
                'name' => 'Kasir Cafe',
                'password' => bcrypt('password'),
                'role' => 'cashier',
            ]
        );

        // Categories
        $catKopi = Category::firstOrCreate(['name' => 'Coffee'], ['status' => true]);
        $catNonKopi = Category::firstOrCreate(['name' => 'Non-Coffee'], ['status' => true]);
        $catFood = Category::firstOrCreate(['name' => 'Main Course'], ['status' => true]);
        $catSnack = Category::firstOrCreate(['name' => 'Snacks'], ['status' => true]);

        // Menus
        Menu::firstOrCreate(
            ['name' => 'Espresso Single'],
            ['category_id' => $catKopi->id, 'price' => 18000, 'description' => 'Rich concentrated coffee shot', 'status' => true, 'is_recommended' => true]
        );
        Menu::firstOrCreate(
            ['name' => 'Iced Cappuccino'],
            ['category_id' => $catKopi->id, 'price' => 25000, 'description' => 'Espresso with steamed milk foam and ice', 'status' => true, 'is_recommended' => true]
        );
        Menu::firstOrCreate(
            ['name' => 'Matcha Latte'],
            ['category_id' => $catNonKopi->id, 'price' => 28000, 'description' => 'Premium Uji Matcha with fresh milk', 'status' => true, 'is_recommended' => false]
        );
        Menu::firstOrCreate(
            ['name' => 'Nasi Goreng Special'],
            ['category_id' => $catFood->id, 'price' => 32000, 'description' => 'Indonesian fried rice with fried egg and chicken', 'status' => true, 'is_recommended' => true]
        );
        Menu::firstOrCreate(
            ['name' => 'French Fries'],
            ['category_id' => $catSnack->id, 'price' => 20000, 'description' => 'Crispy golden potato fries', 'status' => true, 'is_recommended' => false]
        );

        // Tables
        for ($i = 1; $i <= 6; $i++) {
            $table = CafeTable::firstOrCreate(
                ['table_number' => (string)$i],
                [
                    'qr_token' => Str::random(16),
                    'status' => 'available',
                ]
            );
            \App\Services\QrCodeService::ensureQrExists($table);
        }

        $this->call(SettingSeeder::class);
    }
}
