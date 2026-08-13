<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        body {
            margin: 0;
            padding: 0;
            background: #FFFFFF;
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
        }

        .pdf-page {
            width: 100%;
            text-align: center;
        }

        .qr-item {
            width: 60mm;
            height: 88mm;
            margin: 0 auto;
            padding: 5mm;
            box-sizing: border-box;
            border: 1px solid #BDBDBD;
            border-radius: 8px;
            text-align: center;
            page-break-inside: avoid;
        }

        .company-name {
            font-size: 16px;
            line-height: 1.2;
            font-weight: 700;
            color: #111827;
            margin: 0 0 5px;
        }

        .table-name {
            font-size: 18px;
            line-height: 1.2;
            font-weight: 700;
            color: #2563EB;
            margin: 5px 0;
        }

        .qr-box {
            width: 50mm;
            height: 50mm;
            margin: 8mm auto 0;
            text-align: center;
        }

        .qr-box img {
            width: 50mm;
            height: 50mm;
            object-fit: contain;
        }

        .scan-text {
            margin: 6px 0 0;
            font-size: 11px;
            line-height: 1.35;
            color: #374151;
            font-weight: 600;
        }

        .website-text {
            margin: 6px 0 0;
            font-size: 10px;
            line-height: 1.25;
            color: #666666;
        }
    </style>
</head>
<body>
    <div class="pdf-page">
        <div class="qr-item">
            <div class="company-name">Essensia Koffie</div>

            <div class="table-name">{{ strtoupper($table->table_number) }}</div>

            <div class="qr-box">
                <img
                    src="{{ storage_path('app/public/qrcodes/' . \App\Services\QrCodeService::ensureQrExists($table)) }}"
                    alt="Meja {{ $table->table_number }}">
            </div>

            <div class="scan-text">Scan QR Code untuk melakukan pemesanan</div>

            <div class="website-text">{{ url('/') }}</div>
        </div>
    </div>
</body>
</html>
