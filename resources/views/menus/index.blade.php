@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu.css') }}?v=23">
@endpush

@section('title','Menu')

@section('content')

@php
    $activeArea = request('area') === 'cashier' || auth()->user()?->role === 'cashier' ? 'cashier' : 'admin';
    $areaQuery = $activeArea === 'cashier' ? ['area' => 'cashier'] : [];
@endphp

@if(session('success'))

<div class="alert alert-success alert-dismissible fade show">

    {{ session('success') }}

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert">
    </button>

</div>

@endif

<div class="menu-page">

    <!-- Header -->

    <div class="page-header">

        <div>

            <h1>Daftar Menu</h1>

            <div class="breadcrumb-custom">

                @if($activeArea === 'admin')
                <a href="{{ route('dashboard') }}">

                    Dashboard

                </a>

                <span>></span>
                @endif

                <span>Menu</span>

            </div>

        </div>

        <a
            href="{{ route('menus.create', $areaQuery) }}"
            class="btn-add">

            <i class="bi bi-plus-lg"></i>

            Tambah Menu

        </a>

    </div>

    <!-- Card -->

    <div class="menu-card">

        <!-- Toolbar -->

        <div class="toolbar">

            <form
                action="{{ route('menus.index') }}"
                method="GET"
                class="toolbar-form"
                style="display: flex; justify-content: space-between; align-items: center; width: 100%; gap: 15px; flex-wrap: wrap;">

                @if($activeArea === 'cashier')
                    <input type="hidden" name="area" value="cashier">
                @endif

                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 15px; font-weight: 600; color: #4B5563;">Tampilkan:</span>
                    <select
                        name="per_page"
                        class="status-select"
                        style="width: 80px; height: 48px; border-radius: 12px; border: 1px solid #D1D5DB; padding: 0 12px; font-size: 14px; background-color: #fff; cursor: pointer;"
                        onchange="this.form.submit()">
                        <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('per_page', '15') == '15' ? 'selected' : '' }}>15</option>
                        <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20</option>
                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px; align-items: center;">
                    <div class="search-box" style="flex: initial; width: 280px; margin-bottom: 0; border: 2px solid #2563EB; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08); background-color: #F8FAFC !important; overflow: hidden;">

                        <i class="bi bi-search" style="color: #2563EB; font-weight: bold;"></i>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari menu..."
                            style="background-color: transparent !important; border: none !important; outline: none !important; box-shadow: none !important; width: 100%; height: 100%; font-size: 15px; background: transparent !important;">

                    </div>
                    <button type="submit" class="btn-save" style="width: auto; height: 48px; padding: 0 24px; border-radius: 12px; margin: 0;">
                        Cari
                    </button>
                </div>

            </form>

        </div>

        <!-- Header Table -->

        <div class="table-wrapper">

            <div class="table-header">

                <div>No</div>

                <div>Foto</div>

                <div>Nama Menu</div>

                <div>Kategori</div>

                <div>Harga</div>

                <div>Status</div>

                <div>Rekomendasi</div>

                <div>Aksi</div>

            </div>

            @forelse($menus as $menu)

            <div class="menu-item">

                <!-- Nomor -->

                <div class="number" data-label="No">

                    {{ ($menus->currentPage() - 1) * $menus->perPage() + $loop->iteration }}

                </div>

                <!-- Foto -->

                <div class="menu-photo" data-label="Foto">

                    @if($menu->image)

                        <img
                            src="{{ asset('storage/'.$menu->image) }}"
                            alt="{{ $menu->name }}">

                    @else

                        <img
                            src="{{ asset('images/no-image.png') }}"
                            alt="No Image">

                    @endif

                </div>

                <!-- Nama -->

                <div class="menu-info" data-label="Nama Menu">

                    <h5>

                        {{ $menu->name }}

                    </h5>

                    <p>

                        {{ $menu->description ?: '-' }}

                    </p>

                </div>

                <!-- Kategori -->

                <div>

                    <span class="badge-category">

                        {{ optional($menu->category)->name ?? 'Tanpa Kategori' }}

                    </span>

                </div>

                <!-- Harga -->

                <div class="price" data-label="Harga">

                    Rp {{ number_format($menu->price,0,',','.') }}

                </div>

                                <!-- Status -->

                <div>

                    <form
                        action="{{ route('menus.status', ['menu' => $menu->id] + $areaQuery) }}"
                        method="POST">

                        @csrf
                        @method('PUT')

                        <select
                            name="status"
                            class="status-select"
                            onchange="this.form.submit()">

                            <option
                                value="1"
                                {{ $menu->status ? 'selected' : '' }}>

                                Aktif

                            </option>

                            <option
                                value="0"
                                {{ !$menu->status ? 'selected' : '' }}>

                                Habis

                            </option>

                        </select>

                    </form>

                </div>

                <!-- Rekomendasi -->

                <div data-label="Rekomendasi">

                    <form
                        action="{{ route('menus.recommendation', ['menu' => $menu->id] + $areaQuery) }}"
                        method="POST">

                        @csrf
                        @method('PUT')

                        <select
                            name="is_recommended"
                            class="recommendation-select {{ $menu->is_recommended ? 'is-featured' : '' }}"
                            onchange="this.form.submit()">

                            <option
                                value="1"
                                {{ $menu->is_recommended ? 'selected' : '' }}>

                                Pilihan

                            </option>

                            <option
                                value="0"
                                {{ !$menu->is_recommended ? 'selected' : '' }}>

                                Normal

                            </option>

                        </select>

                    </form>

                </div>

                <!-- Aksi -->

                <div class="action-buttons" data-label="Aksi">

                    <a
                        href="{{ route('menus.edit', ['menu' => $menu->id] + $areaQuery) }}"
                        class="btn-edit">

                        <i class="bi bi-pencil"></i>
                    </a>

                    <form
                        action="{{ route('menus.destroy', ['menu' => $menu->id] + $areaQuery) }}"
                        method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus menu ini?')">

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn-delete">

                            <i class="bi bi-trash"></i>

                        </button>

                    </form>

                </div>

            </div>

            @empty

            <div class="empty">

                <i class="bi bi-cup-hot fs-1 d-block mb-3"></i>

                Belum ada data menu.

            </div>

            @endforelse

        </div>

        @if(method_exists($menus,'links'))

        <div class="pagination-wrapper">

            {{ $menus->withQueryString()->links() }}

        </div>

        @endif

    </div>

</div>

@endsection
