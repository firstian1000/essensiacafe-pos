@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu.css') }}?v=23">
<style>
.stock-name-actions{display:flex;gap:10px;align-items:center}
.stock-name-actions select{flex:1}
.btn-add-stock-name{width:68px;min-width:68px;height:68px;border:0;border-radius:18px;background:#EAF4FB;color:#2E7DB8;font-weight:950;padding:0;display:inline-flex;align-items:center;justify-content:center;font-size:24px}
.stock-new-row{display:none;grid-template-columns:minmax(0,1fr) 58px;gap:10px;margin-top:12px}
.stock-new-row.is-visible{display:grid}
.stock-new-row .stock-new-name{margin:0}
.btn-clear-stock-name{width:58px;height:68px;border:0;border-radius:18px;background:#FFF1F2;color:#DC2626;display:inline-flex;align-items:center;justify-content:center;font-size:22px}
</style>
@endpush

@section('title','Tambah Stok')

@section('content')
<div class="menu-page">
    <div class="page-header">
        <div>
            <h1>Tambah Stok</h1>
            <div class="breadcrumb-custom">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <span>></span>
                <a href="{{ route('stocks.index') }}">Stok</a>
                <span>></span>
                <span>Tambah</span>
            </div>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('stocks.store') }}" method="POST">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Stok <span class="text-danger">*</span></label>
                    <div class="stock-name-actions">
                        <select name="name" class="form-control js-stock-name-select" required>
                            <option value="">-- Pilih Nama Stok --</option>
                            @foreach($stockNames as $stockName)
                                <option value="{{ $stockName }}" {{ old('name') === $stockName ? 'selected' : '' }}>{{ $stockName }}</option>
                            @endforeach
                            <option value="__new__" {{ old('name') === '__new__' ? 'selected' : '' }}>Nama stok baru</option>
                        </select>
                        <button type="button" class="btn-add-stock-name js-add-stock-name" title="Tambah Nama Stok" aria-label="Tambah Nama Stok">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    <div class="stock-new-row js-new-stock-row {{ old('name') === '__new__' ? 'is-visible' : '' }}">
                        <input type="text" name="new_name" class="form-control stock-new-name js-new-stock-name" value="{{ old('new_name') }}" placeholder="Tulis nama stok baru">
                        <button type="button" class="btn-clear-stock-name js-clear-stock-name" title="Hapus Nama Stok Baru" aria-label="Hapus Nama Stok Baru">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </div>
                    @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                    @error('new_name')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Jumlah Stok <span class="text-danger">*</span></label>
                    <input type="number" name="stock" class="form-control" value="{{ old('stock') }}" min="0" placeholder="Masukkan jumlah stok" required>
                    @error('stock')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('stocks.index') }}" class="btn-cancel">
                    <i class="bi bi-arrow-left"></i>
                    Batal
                </a>
                <button type="submit" class="btn-save">
                    <i class="bi bi-check-lg"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.js-add-stock-name').forEach(button => {
    button.addEventListener('click', () => {
        const wrapper = button.closest('.form-group');
        const select = wrapper.querySelector('.js-stock-name-select');
        const row = wrapper.querySelector('.js-new-stock-row');
        const input = wrapper.querySelector('.js-new-stock-name');
        select.value = '__new__';
        row.classList.add('is-visible');
        input.required = true;
        input.focus();
    });
});

document.querySelectorAll('.js-stock-name-select').forEach(select => {
    select.addEventListener('change', () => {
        const wrapper = select.closest('.form-group');
        const row = wrapper.querySelector('.js-new-stock-row');
        const input = wrapper.querySelector('.js-new-stock-name');
        row.classList.toggle('is-visible', select.value === '__new__');
        input.required = select.value === '__new__';
        if (select.value === '__new__') input.focus();
    });
});

document.querySelectorAll('.js-clear-stock-name').forEach(button => {
    button.addEventListener('click', () => {
        const wrapper = button.closest('.form-group');
        const select = wrapper.querySelector('.js-stock-name-select');
        const row = wrapper.querySelector('.js-new-stock-row');
        const input = wrapper.querySelector('.js-new-stock-name');
        input.value = '';
        input.required = false;
        select.value = '';
        row.classList.remove('is-visible');
        select.focus();
    });
});

document.querySelectorAll('.js-stock-name-select').forEach(select => {
    const input = select.closest('.form-group').querySelector('.js-new-stock-name');
    input.required = select.value === '__new__';
});
</script>
@endpush
