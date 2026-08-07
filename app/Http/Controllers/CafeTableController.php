<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\QrCodeService;

class CafeTableController extends Controller
{
    public function index(Request $request)
{
    $query = CafeTable::query();

    // Search nomor meja
    if ($request->filled('search')) {
        $query->where('table_number', 'like', '%' . $request->search . '%');
    }

    // Filter status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $tables = $query->latest()->paginate(9);

    return view('tables.index', compact('tables'));
}

    public function create()
    {
        return view('tables.create');
    }

  public function store(Request $request)
{
    $request->validate([
        'table_number' => 'required|unique:cafe_tables,table_number'
    ]);

    // Generate token
    $token = Str::random(10);

    // Generate file QR
    $qrImage = QrCodeService::generate($token);

    // Simpan ke database
    CafeTable::create([
        'table_number' => $request->table_number,
        'qr_token'     => $token,
        'qr_image'     => $qrImage,
        'status'       => 'available'
    ]);

    return redirect()->route('tables.index')
        ->with('success', 'Meja berhasil ditambahkan');
}

public function edit(CafeTable $table)
{
    return view('tables.edit', compact('table'));
}

public function update(Request $request, CafeTable $table)
{
    $request->validate([
        'table_number' => 'required|unique:cafe_tables,table_number,' . $table->id
    ]);

    $table->update([
        'table_number' => $request->table_number,
        'status'       => $request->status
    ]);

    return redirect()
        ->route('tables.index')
        ->with('success', 'Meja berhasil diupdate');
}

public function destroy(CafeTable $table)
{
    // Hapus file QR jika ada
    if ($table->qr_image) {

        $file = storage_path('app/public/qrcodes/' . $table->qr_image);

        if (file_exists($file)) {
            unlink($file);
        }
    }

    $table->delete();

    return redirect()
        ->route('tables.index')
        ->with('success', 'Meja berhasil dihapus');
}

}