@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu.css') }}?v=22">
<style>
.menu-page input[type="file"].form-control{height:46px!important;min-height:46px!important;padding:0!important;line-height:46px!important;overflow:hidden!important;color:#64748B!important;font-weight:800!important;background:#fff!important}
.menu-page input[type="file"].form-control::file-selector-button{height:46px!important;margin:0 14px 0 0!important;padding:0 18px!important;border:0!important;border-right:1px solid #DDE8F5!important;background:#EAF4FB!important;color:#2E7DB8!important;font-weight:950!important;cursor:pointer!important}
.menu-page input[type="file"].form-control:hover::file-selector-button{background:#DBEEF9!important}
.menu-page .variant-field{padding:18px!important;border:1px solid #DDE8F5!important;border-radius:18px!important;background:#F8FBFF!important}
.menu-page .variant-field>.form-label{display:block;margin-bottom:12px!important;font-size:15px!important;font-weight:900!important;color:#0F172A!important}
.menu-page .variant-rows{display:grid!important;gap:12px!important;margin-bottom:14px!important}
.menu-page .variant-row{display:grid!important;grid-template-columns:minmax(220px,1fr) minmax(170px,220px) 46px!important;gap:12px!important;align-items:center!important}
.menu-page .variant-row .form-control{height:46px!important;border:1px solid #D6E4F5!important;border-radius:14px!important;background:#fff!important;padding:0 14px!important;box-shadow:none!important}
.menu-page .btn-add-variant,.menu-page .btn-remove-variant{border:0!important;text-decoration:none!important;display:inline-flex!important;align-items:center!important;justify-content:center!important;gap:8px!important;font-weight:900!important}
.menu-page .btn-add-variant{height:44px!important;padding:0 18px!important;border-radius:14px!important;background:#EAF4FB!important;color:#2E7DB8!important}
.menu-page .btn-remove-variant{width:46px!important;height:46px!important;border-radius:14px!important;background:#FFF1F2!important;color:#DC2626!important}
@media(max-width:720px){.menu-page .variant-row{grid-template-columns:1fr!important}.menu-page .btn-remove-variant{width:100%!important}}
</style>
@endpush

@section('title','Edit Menu')

@section('content')

@php
    $activeArea = request('area') === 'cashier' || auth()->user()?->role === 'cashier' ? 'cashier' : 'admin';
    $areaQuery = $activeArea === 'cashier' ? ['area' => 'cashier'] : [];
@endphp

<div class="menu-page">

    <div class="page-header">

        <div>

            <h1>Edit Menu</h1>

            <div class="breadcrumb-custom">
                @if($activeArea === 'admin')
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <span>></span>
                @endif
                <a href="{{ route('menus.index', $areaQuery) }}">Menu</a>
                <span>></span>
                <span>Edit</span>
            </div>

        </div>

    </div>

    <div class="menu-card">

        <form action="{{ route('menus.update', ['menu' => $menu->id] + $areaQuery) }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-md-6 mb-4">

                    <label class="form-label">
                        Kategori
                    </label>

                    <select
                        name="category_id"
                        class="form-control">

                        @foreach($categories as $category)

                            <option
                                value="{{ $category->id }}"
                                {{ $menu->category_id == $category->id ? 'selected' : '' }}>

                                {{ $category->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-6 mb-4">

                    <label class="form-label">
                        Nama Menu
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name',$menu->name) }}"
                        placeholder="Masukkan nama menu">

                </div>

                <div class="col-md-6 mb-4">

                    <label class="form-label">
                        Harga
                    </label>

                    <input
                        type="number"
                        name="price"
                        class="form-control"
                        value="{{ old('price',$menu->price) }}"
                        placeholder="Contoh : 25000">

                </div>

                <div class="col-md-6 mb-4">

                    <label class="form-label">
                        Gambar Menu
                    </label>

                    <input
                        type="file"
                        name="image"
                        class="form-control">

                </div>

            </div>

            <div class="recommendation-field mb-4">
                <label class="recommendation-check">
                    <input type="checkbox" name="is_recommended" value="1" {{ old('is_recommended', $menu->is_recommended) ? 'checked' : '' }}>
                    <span>
                        <strong>Tampilkan di Rekomendasi Untukmu</strong>
                        <small>Menu ini akan muncul di bagian rekomendasi customer.</small>
                    </span>
                </label>
            </div>

            <div class="recommendation-field mb-4">
                <div class="recommendation-check" style="align-items:flex-start;">
                    <i class="bi bi-thermometer-half" style="font-size:22px;color:#2E7DB8;"></i>
                    <span style="width:100%;">
                        <strong>Opsi Temperature</strong>
                        <small>Aktifkan pilihan temperature yang boleh dipilih customer.</small>
                        <span style="display:flex;gap:16px;flex-wrap:wrap;margin-top:12px;">
                            <label style="display:inline-flex;align-items:center;gap:8px;font-weight:800;color:#334155;">
                                <input type="checkbox" name="allow_ice" value="1" {{ old('allow_ice', $menu->allow_ice ?? true) ? 'checked' : '' }}>
                                Ice
                            </label>
                            <label style="display:inline-flex;align-items:center;gap:8px;font-weight:800;color:#334155;">
                                <input type="checkbox" name="allow_hot" value="1" {{ old('allow_hot', $menu->allow_hot ?? true) ? 'checked' : '' }}>
                                Hot
                            </label>
                        </span>
                    </span>
                </div>
            </div>

            <div class="variant-field mb-4">
                <label class="form-label">Varian Menu</label>
                @php
                    $variantRows = old('variants', $menu->variants->map(fn($variant) => [
                        'name' => $variant->name,
                        'price' => $variant->price,
                    ])->values()->all());
                @endphp
                <div id="variantRows" class="variant-rows">
                    @foreach($variantRows as $index => $variant)
                    <div class="variant-row">
                        <input type="text" name="variants[{{ $index }}][name]" class="form-control" value="{{ $variant['name'] ?? '' }}" placeholder="Nama varian, contoh: Small / Large">
                        <input type="number" name="variants[{{ $index }}][price]" class="form-control" value="{{ $variant['price'] ?? '' }}" placeholder="Harga varian">
                        <button type="button" class="btn-remove-variant"><i class="bi bi-trash"></i></button>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="btn-add-variant" id="addVariant">
                    <i class="bi bi-plus-lg"></i> Tambah Varian
                </button>
                <small class="text-muted d-block mt-2">Kosongkan jika menu tidak punya varian. Jika varian dipilih customer, harga mengikuti harga varian.</small>
            </div>

            <div class="mb-4">

                <label class="form-label">
                    Deskripsi
                </label>

                <textarea
                    name="description"
                    class="form-control"
                    rows="6"
                    placeholder="Masukkan deskripsi menu">{{ old('description',$menu->description) }}</textarea>

            </div>

            @if($menu->image)

                <div class="current-image">

                    <label class="form-label">
                        Gambar Saat Ini
                    </label>

                    <div class="mt-3">

                        <img
                            src="{{ asset('storage/'.$menu->image) }}"
                            class="preview-image">

                    </div>

                </div>

                @endif

            <div class="form-footer">

                <a
                    href="{{ route('menus.index', $areaQuery) }}"
                    class="btn-cancel">

                    Batal

                </a>

                <button
                    type="submit"
                    class="btn-save">

                    <i class="bi bi-check-lg"></i>

                    Update

                </button>

            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
<script>
let variantIndex = document.querySelectorAll('.variant-row').length;
document.getElementById('addVariant').addEventListener('click', () => {
    document.getElementById('variantRows').insertAdjacentHTML('beforeend', `
        <div class="variant-row">
            <input type="text" name="variants[${variantIndex}][name]" class="form-control" placeholder="Nama varian, contoh: Small / Large">
            <input type="number" name="variants[${variantIndex}][price]" class="form-control" placeholder="Harga varian">
            <button type="button" class="btn-remove-variant"><i class="bi bi-trash"></i></button>
        </div>
    `);
    variantIndex++;
});
document.addEventListener('click', event => {
    const button = event.target.closest('.btn-remove-variant');
    if (!button) return;
    button.closest('.variant-row').remove();
});
</script>
@endpush
