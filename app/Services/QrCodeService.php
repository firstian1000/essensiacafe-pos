<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;

class QrCodeService
{
    public static function generate(string $token): string
    {
        $result = (new Builder())->build(
            data: route('customer.menu', $token),
            size: 300,
            margin: 10
        );

        $fileName = $token . '.png';

        $directory = storage_path('app/public/qrcodes');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $result->saveToFile($directory . '/' . $fileName);

        return $fileName;
    }
}