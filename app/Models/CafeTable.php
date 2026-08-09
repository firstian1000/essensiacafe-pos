<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CafeTable extends Model
{
    protected $fillable = [
        'table_number',
        'qr_token',
        'qr_image',
        'status'
    ];

    public function orders()
{
    return $this->hasMany(Order::class,'cafe_table_id');
}


    public function getDisplayNameAttribute(): string
    {
        $number = trim((string) $this->table_number);

        if ($number === '') {
            return '-';
        }

        return preg_match('/^meja\b/i', $number) ? $number : 'Meja ' . $number;
    }

    public function getQrImageUrlAttribute(): string
    {
        $token = $this->qr_token ?: \Illuminate\Support\Str::random(10);

        if (!$this->qr_token) {
            $this->forceFill(['qr_token' => $token])->save();
        }

        return \App\Services\QrCodeService::dataUri($token);
    }
}
