<div class="form-row">
    <div class="form-group">
        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
        <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', optional($expense?->expense_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
        @error('expense_date')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="form-group">
        <label class="form-label">Nama Pengeluaran <span class="text-danger">*</span></label>
        <select name="name" class="form-control" required>
            @php($selectedName = old('name', $expense?->name ?? 'Lain-lain'))
            <option value="Gaji Karyawan" {{ $selectedName === 'Gaji Karyawan' ? 'selected' : '' }}>Gaji Karyawan</option>
            <option value="Lain-lain" {{ $selectedName === 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
        </select>
        @error('name')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Kategori <span class="text-danger">*</span></label>
        <select name="category" class="form-control" required>
            @php($selectedCategory = old('category', $expense?->category ?? 'Internal'))
            <option value="Internal" {{ $selectedCategory === 'Internal' ? 'selected' : '' }}>Internal</option>
            <option value="External" {{ $selectedCategory === 'External' ? 'selected' : '' }}>External</option>
        </select>
        @error('category')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="form-group">
        <label class="form-label">Nominal <span class="text-danger">*</span></label>
        <input type="number" name="amount" class="form-control" value="{{ old('amount', $expense?->amount) }}" min="0" placeholder="Masukkan nominal" required>
        @error('amount')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="form-group">
    <label class="form-label">Catatan</label>
    <textarea name="note" class="form-control" rows="5" placeholder="Catatan internal pengeluaran...">{{ old('note', $expense?->note) }}</textarea>
    @error('note')<small class="text-danger">{{ $message }}</small>@enderror
</div>

<div class="form-footer">
    <a href="{{ route('expenses.index') }}" class="btn-cancel">
        <i class="bi bi-arrow-left"></i>
        Batal
    </a>
    <button type="submit" class="btn-save">
        <i class="bi bi-check-lg"></i>
        Simpan
    </button>
</div>
