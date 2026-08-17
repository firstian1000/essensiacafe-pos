@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/table.css') }}?v=12">
@endpush

@section('title','Tambah Meja')

@section('content')

@php
    $activeArea = request('area') === 'cashier' || auth()->user()?->role === 'cashier' ? 'cashier' : 'admin';
    $areaQuery = $activeArea === 'cashier' ? ['area' => 'cashier'] : [];
@endphp

<div class="table-page">

    <!-- Header -->

    <div class="page-header">

        <div>

            <h1>Tambah Meja</h1>

            <div class="breadcrumb-custom">

                @if($activeArea === 'admin')
                <a href="{{ route('dashboard') }}">
                    Dashboard
                </a>

                <span>></span>
                @endif

                <a href="{{ route('tables.index', $areaQuery) }}">
                    Meja
                </a>

                <span>></span>

                <span>Tambah</span>

            </div>

        </div>

    </div>

    <!-- Form -->

    <div class="table-form-card">

        <form
            action="{{ route('tables.store', $areaQuery) }}"
            method="POST">

            @csrf

            <div class="form-row">

                <!-- Nomor Meja -->

                <div class="form-group">

                    <label class="form-label">

                        Nomor Meja

                    </label>

                    <input
                        type="text"
                        name="table_number"
                        class="form-control"
                        value="{{ old('table_number', $nextNumber) }}"
                        placeholder="Contoh : Meja 1"
                        required>

                    @error('table_number')

                        <small class="text-danger">

                            {{ $message }}

                        </small>

                    @enderror

                </div>

                <!-- Status -->

                <div class="form-group">

                    <label class="form-label">

                        Status

                    </label>

                    <select
                        name="status"
                        class="form-control">

                        <option value="available">

                            Tersedia

                        </option>

                        <option value="occupied">

                            Terisi

                        </option>

                        <option value="reserved">

                            Dipesan

                        </option>

                    </select>

                </div>

            </div>

            <div class="form-footer">

                <a
                    href="{{ route('tables.index', $areaQuery) }}"
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
