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
                class="toolbar-form">

                <div class="search-box">

                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari kategori...">

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

                    {{ $loop->iteration }}

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