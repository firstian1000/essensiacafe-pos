<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'cafe_open_time' => '08:00',
            'cafe_close_time' => '22:00',
            'shift_duration_hours' => '7',
            'before_close_notif_minutes' => '15',
            'before_shift_notif_minutes' => '15',
            'order_limit_minutes' => '10',
        ];

        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
