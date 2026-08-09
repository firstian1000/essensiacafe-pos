@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/table.css') }}?v=11">
@endpush

@section('title','Edit Meja')

@section('content')

<div class="table-page">

    <!-- Header -->

    <div class="page-header">

        <div>

            <h1>Edit Meja</h1>

            <div class="breadcrumb-custom">

                <a href="{{ route('dashboard') }}">
                    Dashboard
                </a>

                <span>></span>

                <a href="{{ route('tables.index') }}">
                    Meja
                </a>

                <span>></span>

                <span>Edit</span>

            </div>

        </div>

    </div>

    <!-- Form -->

    <div class="table-form-card">

        <form
            action="{{ route('tables.update',$table->id) }}"
            method="POST">

            @csrf
            @method('PUT')

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
                        value="{{ old('table_number',$table->table_number) }}"
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

                        <option
                            value="available"
                            {{ old('status',$table->status)=='available' ? 'selected' : '' }}>

                            Tersedia

                        </option>

                        <option
                            value="occupied"
                            {{ old('status',$table->status)=='occupied' ? 'selected' : '' }}>

                            Terisi

                        </option>

                        <option
                            value="inactive"
                            {{ old('status',$table->status)=='inactive' ? 'selected' : '' }}>

                            Tidak Tersedia

                        </option>

                    </select>

                </div>

            </div>

            <!-- QR Information -->

            <div class="qr-preview">

                <label class="form-label">

                    QR Code Meja

                </label>

                <div class="qr-box">

                    <img
                        src="{{ $table->qr_image_url }}"
                        alt="QR Code">

                </div>

            </div>

            <!-- Button -->

            <div class="form-footer">

                <a
                    href="{{ route('tables.index') }}"
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
