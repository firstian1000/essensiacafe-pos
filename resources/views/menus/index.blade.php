@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu.css') }}?v=11">
@endpush

@section('title','Menu')

@section('content')

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

                <a href="{{ route('dashboard') }}">

                    Dashboard

                </a>

                <span>></span>

                <span>Menu</span>

            </div>

        </div>

        <a
            href="{{ route('menus.create') }}"
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
                class="toolbar-form">

                <div class="search-box">

                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari menu...">

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

                    {{ $loop->iteration }}

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
                        action="{{ route('menus.status',$menu->id) }}"
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
                        action="{{ route('menus.recommendation',$menu->id) }}"
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
                        href="{{ route('menus.edit',$menu->id) }}"
                        class="btn-edit">

                        <i class="bi bi-pencil"></i>
                    </a>

                    <form
                        action="{{ route('menus.destroy',$menu->id) }}"
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
