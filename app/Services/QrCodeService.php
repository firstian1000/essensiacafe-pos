<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use App\Models\CafeTable;

class QrCodeService
{
    public static function generate(string $token): string
    {
        $result = (new Builder())->build(
            writer: new SvgWriter(),
            data: route('customer.menu', $token),
            size: 300,
            margin: 10
        );

        $fileName = $token . '.svg';
        $directory = storage_path('app/public/qrcodes');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $result->saveToFile($directory . '/' . $fileName);

        return $fileName;
    }

    public static function ensureQrExists(CafeTable $table): string
    {
        $directory = storage_path('app/public/qrcodes');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        if ($table->qr_image && file_exists($directory . '/' . $table->qr_image)) {
            return $table->qr_image;
        }

        $token = $table->qr_token ?: \Illuminate\Support\Str::random(10);
        $fileName = self::generate($token);

        $table->update([
            'qr_token' => $token,
            'qr_image' => $fileName,
        ]);

        return $fileName;
    }
}