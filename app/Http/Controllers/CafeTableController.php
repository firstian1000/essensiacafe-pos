<?php

namespace App\Http\Controllers;

use App\Models\CafeTable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\QrCodeService;

class CafeTableController extends Controller
{
    private const TABLE_STATUSES = ['available', 'occupied', 'reserved'];

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

    $tables = $query->orderByRaw('CAST(table_number AS INTEGER) ASC, table_number ASC')->paginate(9);

    return view('tables.index', compact('tables'));
}

    public function create()
    {
        // Auto-increment: ambil nomor meja terakhir + 1
        $lastNumber = CafeTable::selectRaw('MAX(CAST(table_number AS INTEGER)) as max_num')->value('max_num');
        $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        return view('tables.create', compact('nextNumber'));
    }

  public function store(Request $request)
{
    $request->validate([
        'table_number' => 'required|unique:cafe_tables,table_number',
        'status' => 'nullable|in:' . implode(',', self::TABLE_STATUSES),
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
        'status'       => $request->status ?? 'available'
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
        'table_number' => 'required|unique:cafe_tables,table_number,' . $table->id,
        'status' => 'required|in:' . implode(',', self::TABLE_STATUSES),
    ]);

    $table->update([
        'table_number' => $request->table_number,
        'status'       => $request->status
    ]);

    return redirect()
        ->route('tables.index')
        ->with('success', 'Meja berhasil diupdate');
}

public function updateStatus(Request $request, CafeTable $table)
{
    $request->validate([
        'status' => 'required|in:' . implode(',', self::TABLE_STATUSES),
    ]);

    $table->update([
        'status' => $request->status,
    ]);

    return back()->with('success', 'Status meja berhasil diperbarui');
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
