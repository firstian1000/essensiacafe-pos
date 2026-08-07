@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu.css') }}?v=11">
@endpush

@section('title','Edit Menu')

@section('content')

<div class="menu-page">

    <div class="page-header">

        <div>

            <h1>Edit Menu</h1>

            <div class="breadcrumb-custom">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <span>></span>
                <a href="{{ route('menus.index') }}">Menu</a>
                <span>></span>
                <span>Edit</span>
            </div>

        </div>

    </div>

    <div class="menu-card">

        <form action="{{ route('menus.update',$menu->id) }}"
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
                    href="{{ route('menus.index') }}"
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