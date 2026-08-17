@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/category.css') }}?v=10">
@endpush

@section('title','Tambah Kategori')

@section('content')

@php
    $activeArea = request('area') === 'cashier' || auth()->user()?->role === 'cashier' ? 'cashier' : 'admin';
    $areaQuery = $activeArea === 'cashier' ? ['area' => 'cashier'] : [];
@endphp

<div class="category-page">

    <!-- HEADER -->

    <div class="page-header">

        <div>

            <h1>Tambah Kategori</h1>

            <div class="breadcrumb-custom">

                @if($activeArea === 'admin')
                <a href="{{ route('dashboard') }}">
                    Dashboard
                </a>

                <span>></span>
                @endif

                <a href="{{ route('categories.index', $areaQuery) }}">
                    Kategori
                </a>

                <span>></span>

                <span>Tambah</span>

            </div>

        </div>

    </div>

    <!-- CARD -->

    <div class="category-card">

        <form
            action="{{ route('categories.store', $areaQuery) }}"
            method="POST">

            @csrf

            <!-- Nama -->

            <div class="form-group mb-4">

                <label class="form-label">

                    Nama Kategori

                </label>

                <input
                    type="text"
                    name="name"
                    class="form-control"
                    value="{{ old('name') }}"
                    placeholder="Masukkan nama kategori"
                    required>

                @error('name')

                    <small class="text-danger">

                        {{ $message }}

                    </small>

                @enderror

            </div>

            <!-- Deskripsi -->

            <div class="form-group">

                <label class="form-label">

                    Deskripsi

                </label>

                <textarea
                    name="description"
                    class="form-control"
                    rows="6"
                    placeholder="Masukkan deskripsi kategori...">{{ old('description') }}</textarea>

                @error('description')

                    <small class="text-danger">

                        {{ $message }}

                    </small>

                @enderror

            </div>

            <!-- BUTTON -->

            <div class="form-footer">

                <a
                    href="{{ route('categories.index', $areaQuery) }}"
                    class="btn-cancel">

                    <i class="bi bi-arrow-left"></i>

                    Batal

                </a>

                <button
                    type="submit"
                    class="btn-save">

                    <i class="bi bi-check-lg"></i>

                    Simpan

                </button>

            </div>

        </form>

    </div>

</div>

@endsection
