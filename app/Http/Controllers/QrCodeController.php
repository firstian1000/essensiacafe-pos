<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\CafeTable;

class QrCodeController extends Controller
{
    // Download QR Code
public function download(CafeTable $table)
{
    $pdf = Pdf::loadView('tables.qr-pdf', compact('table'));

    $pdf->setPaper('a4', 'portrait');

    return $pdf->download('QR-'.$table->table_number.'.pdf');
}

    // Cetak Semua QR Code
    public function printAll()
    {
        $tables = CafeTable::orderBy('table_number')->get();

        return view('tables.print-all', compact('tables'));
    }
}