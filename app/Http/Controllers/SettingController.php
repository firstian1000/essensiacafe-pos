<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'cafe_open_time' => Setting::get('cafe_open_time', '08:00'),
            'cafe_close_time' => Setting::get('cafe_close_time', '22:00'),
            'shift_duration_hours' => Setting::get('shift_duration_hours', '7'),
            'before_close_notif_minutes' => Setting::get('before_close_notif_minutes', '15'),
            'before_shift_notif_minutes' => Setting::get('before_shift_notif_minutes', '15'),
            'order_limit_minutes' => Setting::get('order_limit_minutes', '10'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cafe_open_time' => 'required|date_format:H:i',
            'cafe_close_time' => 'required|date_format:H:i|after:cafe_open_time',
            'shift_duration_hours' => 'required|integer|min:1|max:24',
            'before_close_notif_minutes' => 'required|integer|min:0|max:60',
            'before_shift_notif_minutes' => 'required|integer|min:0|max:60',
            'order_limit_minutes' => 'required|integer|min:0|max:60',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan waktu operasional berhasil diperbarui.');
    }
}
