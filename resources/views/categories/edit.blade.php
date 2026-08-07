@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/category.css') }}?v=10">
@endpush

@section('title','Edit Kategori')

@section('content')

<div class="category-page">

    <!-- HEADER -->

    <div class="page-header">

        <div>

            <h1>Edit Kategori</h1>

            <div class="breadcrumb-custom">

                <a href="{{ route('dashboard') }}">
                    Dashboard
                </a>

                <span>></span>

                <a href="{{ route('categories.index') }}">
                    Kategori
                </a>

                <span>></span>

                <span>Edit</span>

            </div>

        </div>

    </div>

    <!-- CARD -->

    <div class="category-card">

        <form
            action="{{ route('categories.update',$category->id) }}"
            method="POST">

            @csrf
            @method('PUT')

            <!-- Nama -->

            <div class="form-group mb-4">

                <label class="form-label">

                    Nama Kategori

                </label>

                <input
                    type="text"
                    name="name"
                    class="form-control"
                    value="{{ old('name',$category->name) }}"
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
                    placeholder="Masukkan deskripsi kategori...">{{ old('description',$category->description) }}</textarea>

                @error('description')

                    <small class="text-danger">

                        {{ $message }}

                    </small>

                @enderror

            </div>

            <!-- BUTTON -->

            <div class="form-footer">

                <a
                    href="{{ route('categories.index') }}"
                    class="btn-cancel">

                    <i class="bi bi-arrow-left"></i>

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