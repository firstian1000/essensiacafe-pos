<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected static $cache = [];

    public static function get(string $key, $default = null)
    {
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }

        $setting = self::where('key', $key)->first();
        $value = $setting ? $setting->value : $default;
        
        self::$cache[$key] = $value;
        return $value;
    }

    public static function set(string $key, $value)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        self::$cache[$key] = $value;
        return $setting;
    }

    public static function isOrderingClosed(): bool
    {
        if (self::get('operational_settings_enabled', '1') !== '1' || self::get('close_order_settings_enabled', '1') !== '1') {
            return false;
        }

        $openTimeStr = self::get('cafe_open_time', '08:00');
        $closeTimeStr = self::get('cafe_close_time', '22:00');
        $limitMinutes = (int) self::get('order_limit_minutes', '10');

        $now = \Carbon\Carbon::now();
        $openTime = \Carbon\Carbon::createFromTimeString($openTimeStr);
        $closeTime = \Carbon\Carbon::createFromTimeString($closeTimeStr);
        $limitTime = (clone $closeTime)->subMinutes($limitMinutes);

        // If today's close time is earlier than open time (e.g. cross midnight, which isn't the case here, but good practice)
        if ($now->lessThan($openTime) || $now->greaterThanOrEqualTo($limitTime)) {
            return true;
        }

        return false;
    }
}
