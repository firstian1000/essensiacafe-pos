@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/category.css') }}?v=10">
@endpush

@section('title','Kategori')

@section('content')

<div class="category-page">

    {{-- Header --}}
    <div class="page-header">

        <div>

            <h1>Kategori</h1>

            <div class="breadcrumb-custom">

                <a href="{{ route('dashboard') }}">
                    Dashboard
                </a>

                <span>></span>

                <span>Kategori</span>

            </div>

        </div>

        <a href="{{ route('categories.create') }}" class="btn-add">

            <i class="bi bi-plus-lg"></i>

            Tambah Kategori

        </a>

    </div>

    {{-- Alert --}}
    @if(session('success'))

    <div class="alert alert-success alert-dismissible fade show">

        {{ session('success') }}

        <button class="btn-close" data-bs-dismiss="alert"></button>

    </div>

    @endif

    <div class="category-card">

        {{-- Toolbar --}}
        <form action="{{ route('categories.index') }}" method="GET" class="toolbar-form">

            <div class="search-box">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari kategori...">

            </div>

            <select
                name="status"
                class="status-filter"
                onchange="this.form.submit()">

                <option value="">Semua Status</option>

                <option value="1"
                    {{ request('status')==='1' ? 'selected':'' }}>

                    Aktif

                </option>

                <option value="0"
                    {{ request('status')==='0' ? 'selected':'' }}>

                    Nonaktif

                </option>

            </select>

        </form>

        {{-- Header Table --}}
        <div class="table-header">

            <div>No</div>

            <div>Nama Kategori</div>

            <div>Deskripsi</div>

            <div>Status</div>

            <div>Aksi</div>

        </div>

        {{-- Data --}}
        @forelse($categories as $category)

        <div class="category-item">

            <div class="number">

                {{ $categories->firstItem() + $loop->index }}

            </div>

            <div class="category-name">

                <div class="category-icon">

                    @php

                        $nama = strtolower($category->name);

                    @endphp

                    @if(str_contains($nama,'coffee'))

                        <i class="bi bi-cup-hot"></i>

                    @elseif(str_contains($nama,'food'))

                        <i class="bi bi-egg-fried"></i>

                    @elseif(str_contains($nama,'dessert'))

                        <i class="bi bi-cake2"></i>

                    @elseif(str_contains($nama,'snack'))

                        <i class="bi bi-cookie"></i>

                    @else

                        <i class="bi bi-grid"></i>

                    @endif

                </div>

                <div>

                    <h5>{{ $category->name }}</h5>

                    <small>Kategori Menu</small>

                </div>

            </div>

            <div class="description">

                {{ $category->description ?: '-' }}

            </div>

            <div>

                @if($category->status)

                    <span class="badge-active">

                        ● Aktif

                    </span>

                @else

                    <span class="badge-inactive">

                        ● Nonaktif

                    </span>

                @endif

            </div>

            <div class="action-buttons">

                <a
                    href="{{ route('categories.edit',$category) }}"
                    class="btn-edit">

                    <i class="bi bi-pencil"></i>

                    Edit

                </a>

                <form
                    action="{{ route('categories.destroy',$category) }}"
                    method="POST">

                    @csrf

                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn-delete"
                        onclick="return confirm('Yakin ingin menghapus kategori ini?')">

                        <i class="bi bi-trash"></i>

                        Hapus

                    </button>

                </form>

            </div>

        </div>

        @empty

        <div class="empty">

            Belum ada kategori.

        </div>

        @endforelse

        <div class="pagination-wrapper">

            {{ $categories->withQueryString()->links() }}

        </div>

    </div>

</div>

@endsection