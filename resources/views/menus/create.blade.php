@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu.css') }}?v=11">
@endpush

@section('title','Tambah Menu')

@section('content')

<div class="menu-page">

    <!-- Header -->

    <div class="page-header">

        <div>

            <h1>Tambah Menu</h1>

            <div class="breadcrumb-custom">

                <a href="{{ route('dashboard') }}">
                    Dashboard
                </a>

                <span>></span>

                <a href="{{ route('menus.index') }}">
                    Menu
                </a>

                <span>></span>

                <span>Tambah</span>

            </div>

        </div>

    </div>

    <!-- Card -->

    <div class="form-card">

        <form
            action="{{ route('menus.store') }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            <div class="form-row">

                <!-- Kategori -->

                <div class="form-group">

                    <label class="form-label">

                        Kategori

                    </label>

                    <select
                        name="category_id"
                        class="form-control"
                        required>

                        <option value="">

                            -- Pilih Kategori --

                        </option>

                        @foreach($categories as $category)

                            <option
                                value="{{ $category->id }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>

                                {{ $category->name }}

                            </option>

                        @endforeach

                    </select>

                    @error('category_id')

                        <small class="text-danger">

                            {{ $message }}

                        </small>

                    @enderror

                </div>

                <!-- Nama Menu -->

                <div class="form-group">

                    <label class="form-label">

                        Nama Menu

                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        placeholder="Masukkan nama menu"
                        required>

                    @error('name')

                        <small class="text-danger">

                            {{ $message }}

                        </small>

                    @enderror

                </div>

            </div>

            <div class="form-row">

                <!-- Harga -->

                <div class="form-group">

                    <label class="form-label">

                        Harga

                    </label>

                    <input
                        type="number"
                        name="price"
                        class="form-control"
                        value="{{ old('price') }}"
                        placeholder="Contoh : 25000"
                        required>

                    @error('price')

                        <small class="text-danger">

                            {{ $message }}

                        </small>

                    @enderror

                </div>

                <!-- Gambar -->

                <div class="form-group">

                    <label class="form-label">

                        Gambar Menu

                    </label>

                    <input
                        type="file"
                        name="image"
                        class="form-control"
                        accept="image/*">

                    @error('image')

                        <small class="text-danger">

                            {{ $message }}

                        </small>

                    @enderror

                </div>

            </div>

            <div class="recommendation-field">
                <label class="recommendation-check">
                    <input type="checkbox" name="is_recommended" value="1" {{ old('is_recommended') ? 'checked' : '' }}>
                    <span>
                        <strong>Tampilkan di Rekomendasi Untukmu</strong>
                        <small>Menu ini akan muncul di bagian rekomendasi customer.</small>
                    </span>
                </label>
            </div>

            <!-- Deskripsi -->

            <div class="form-group">

                <label class="form-label">

                    Deskripsi

                </label>

                <textarea
                    name="description"
                    class="form-control"
                    placeholder="Masukkan deskripsi menu...">{{ old('description') }}</textarea>

                @error('description')

                    <small class="text-danger">

                        {{ $message }}

                    </small>

                @enderror

            </div>

            <!-- Button -->

            <div class="form-footer">

                <a
                    href="{{ route('menus.index') }}"
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