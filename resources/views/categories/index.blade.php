@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/category.css') }}?v=10">
@endpush

@section('title','Kategori')

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

<div class="category-page">

    <!-- HEADER -->

    <div class="page-header">

        <div>

            <h1>Daftar Kategori</h1>

            <div class="breadcrumb-custom">

                <a href="{{ route('dashboard') }}">

                    Dashboard

                </a>

                <span>></span>

                <span>Kategori</span>

            </div>

        </div>

        <a
            href="{{ route('categories.create') }}"
            class="btn-add">

            <i class="bi bi-plus-lg"></i>

            Tambah Kategori

        </a>

    </div>

    <!-- CARD -->

    <div class="category-card">

        <!-- TOOLBAR -->

        <div class="toolbar">

            <form
                action="{{ route('categories.index') }}"
                method="GET"
                class="toolbar-form"
                style="display: flex; justify-content: space-between; align-items: center; width: 100%; gap: 15px; flex-wrap: wrap;">

                <div style="display: flex; gap: 10px; align-items: center; width: 100%; max-width: 480px;">
                    <div class="search-box" style="flex: 1; margin-bottom: 0; border: 2px solid #2563EB; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08); background-color: #F8FAFC !important; overflow: hidden; height: 55px; border-radius: 16px; display: flex; align-items: center; padding: 0 18px; gap: 12px;">

                        <i class="bi bi-search" style="color: #2563EB; font-weight: bold;"></i>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari kategori..."
                            style="background-color: transparent !important; border: none !important; outline: none !important; box-shadow: none !important; width: 100%; height: 100%; font-size: 15px; background: transparent !important;">

                    </div>
                    <button type="submit" class="btn-add" style="width: auto; height: 48px; padding: 0 24px; border-radius: 12px; margin: 0; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                        Cari
                    </button>
                </div>

            </form>

        </div>

        <!-- HEADER TABLE -->

        <div class="table-wrapper">

            <div class="table-header">

                <div>No</div>

                <div>Nama Kategori</div>

                <div>Deskripsi</div>

                <div>Aksi</div>

            </div>

            @forelse($categories as $category)

            <div class="category-item">

                <!-- NOMOR -->

                <div class="number" data-label="No">

                    {{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}

                </div>

                <!-- NAMA -->

                <div class="category-name" data-label="Nama Kategori">

                    {{ $category->name }}

                </div>

                <!-- DESKRIPSI -->

                <div class="description" data-label="Deskripsi">

                    {{ $category->description ?: '-' }}

                </div>

                                <!-- AKSI -->

                <div class="action-buttons" data-label="Aksi">

                    <a
                        href="{{ route('categories.edit',$category->id) }}"
                        class="btn-edit">

                        <i class="bi bi-pencil"></i>

                    </a>

                    <form
                        action="{{ route('categories.destroy',$category->id) }}"
                        method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">

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

                <i class="bi bi-folder fs-1 d-block mb-3"></i>

                Belum ada data kategori.

            </div>

            @endforelse

        </div>

        @if(method_exists($categories,'links'))

        <div class="pagination-wrapper">

            {{ $categories->withQueryString()->links() }}

        </div>

        @endif

    </div>

</div>

@endsection