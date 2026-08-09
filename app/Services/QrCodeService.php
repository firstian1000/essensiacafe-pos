<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use App\Models\CafeTable;

class QrCodeService
{
    public static function svg(string $token): string
    {
        $result = (new Builder())->build(
            writer: new SvgWriter(),
            data: route('customer.menu', $token),
            size: 300,
            margin: 10
        );

        return $result->getString();
    }

    public static function dataUri(string $token): string
    {
        return 'data:image/svg+xml;base64,' . base64_encode(self::svg($token));
    }

    public static function generate(string $token): string
    {
        $fileName = $token . '.svg';
        $directory = storage_path('app/public/qrcodes');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($directory . '/' . $fileName, self::svg($token));

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
