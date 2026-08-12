<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'operational_settings_enabled' => Setting::get('operational_settings_enabled', '1'),
            'cafe_open_time' => Setting::get('cafe_open_time', '08:00'),
            'cafe_close_time' => Setting::get('cafe_close_time', '22:00'),
            'shift_settings_enabled' => Setting::get('shift_settings_enabled', '1'),
            'shift_duration_hours' => Setting::get('shift_duration_hours', '7'),
            'close_order_settings_enabled' => Setting::get('close_order_settings_enabled', '1'),
            'before_close_notif_minutes' => Setting::get('before_close_notif_minutes', '15'),
            'before_shift_notif_minutes' => Setting::get('before_shift_notif_minutes', '15'),
            'order_limit_minutes' => Setting::get('order_limit_minutes', '10'),
            'wifi_username' => Setting::get('wifi_username', 'Harina Studio'),
            'wifi_password' => Setting::get('wifi_password', '-'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'operational_settings_enabled' => 'required|boolean',
            'cafe_open_time' => 'required|date_format:H:i',
            'cafe_close_time' => 'required|date_format:H:i|after:cafe_open_time',
            'shift_settings_enabled' => 'required|boolean',
            'shift_duration_hours' => 'required|integer|min:1|max:24',
            'close_order_settings_enabled' => 'required|boolean',
            'before_close_notif_minutes' => 'required|integer|min:0|max:60',
            'before_shift_notif_minutes' => 'required|integer|min:0|max:60',
            'order_limit_minutes' => 'required|integer|min:0|max:60',
            'wifi_username' => 'nullable|string|max:100',
            'wifi_password' => 'nullable|string|max:100',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan waktu operasional berhasil diperbarui.');
    }
}
