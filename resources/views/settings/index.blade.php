@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu.css') }}?v=11">
@endpush

@section('title', 'Pengaturan Operasional')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="menu-page">

    <!-- Header -->
    <div class="page-header">
        <div>
            <h1>Pengaturan Operasional</h1>
            <div class="breadcrumb-custom">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <span>></span>
                <span>Pengaturan</span>
            </div>
        </div>
    </div>

    <!-- Form Pengaturan -->
    <div class="form-card">
        <form action="{{ route('settings.store') }}" method="POST">
            @csrf

            <h4 class="mb-4 fw-bold text-dark border-bottom pb-2">
                <i class="bi bi-clock-fill text-primary me-2"></i> Jam Operasional Kafe
            </h4>

            <div class="form-row">
                <!-- Jam Buka -->
                <div class="form-group">
                    <label class="form-label" for="cafe_open_time">Jam Buka Kafe</label>
                    <input
                        type="time"
                        id="cafe_open_time"
                        name="cafe_open_time"
                        class="form-control"
                        value="{{ old('cafe_open_time', $settings['cafe_open_time']) }}"
                        required>
                    @error('cafe_open_time')
                        <small class="text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Jam Tutup -->
                <div class="form-group">
                    <label class="form-label" for="cafe_close_time">Jam Tutup Kafe</label>
                    <input
                        type="time"
                        id="cafe_close_time"
                        name="cafe_close_time"
                        class="form-control"
                        value="{{ old('cafe_close_time', $settings['cafe_close_time']) }}"
                        required>
                    @error('cafe_close_time')
                        <small class="text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <h4 class="mt-5 mb-4 fw-bold text-dark border-bottom pb-2">
                <i class="bi bi-people-fill text-primary me-2"></i> Pergantian Shift Pekerja
            </h4>

            <div class="form-row">
                <!-- Durasi Shift (Jam) -->
                <div class="form-group">
                    <label class="form-label" for="shift_duration_hours">Durasi Shift Kerja (Jam setelah buka)</label>
                    <div class="input-group">
                        <input
                            type="number"
                            id="shift_duration_hours"
                            name="shift_duration_hours"
                            class="form-control"
                            min="1"
                            max="24"
                            value="{{ old('shift_duration_hours', $settings['shift_duration_hours']) }}"
                            required>
                        <span class="input-group-text bg-light text-muted border-start-0" style="border-radius: 0 16px 16px 0; border: 1px solid #E5E7EB;">Jam</span>
                    </div>
                    @error('shift_duration_hours')
                        <small class="text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Waktu Notifikasi Shift (Menit) -->
                <div class="form-group">
                    <label class="form-label" for="before_shift_notif_minutes">Peringatan Sebelum Ganti Shift (Menit)</label>
                    <div class="input-group">
                        <input
                            type="number"
                            id="before_shift_notif_minutes"
                            name="before_shift_notif_minutes"
                            class="form-control"
                            min="0"
                            max="60"
                            value="{{ old('before_shift_notif_minutes', $settings['before_shift_notif_minutes']) }}"
                            required>
                        <span class="input-group-text bg-light text-muted border-start-0" style="border-radius: 0 16px 16px 0; border: 1px solid #E5E7EB;">Menit</span>
                    </div>
                    @error('before_shift_notif_minutes')
                        <small class="text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <h4 class="mt-5 mb-4 fw-bold text-dark border-bottom pb-2">
                <i class="bi bi-bell-fill text-primary me-2"></i> Peringatan Tutup & Batas Order
            </h4>

            <div class="form-row">
                <!-- Waktu Notifikasi Tutup (Menit) -->
                <div class="form-group">
                    <label class="form-label" for="before_close_notif_minutes">Peringatan Sebelum Kafe Tutup (Menit)</label>
                    <div class="input-group">
                        <input
                            type="number"
                            id="before_close_notif_minutes"
                            name="before_close_notif_minutes"
                            class="form-control"
                            min="0"
                            max="60"
                            value="{{ old('before_close_notif_minutes', $settings['before_close_notif_minutes']) }}"
                            required>
                        <span class="input-group-text bg-light text-muted border-start-0" style="border-radius: 0 16px 16px 0; border: 1px solid #E5E7EB;">Menit</span>
                    </div>
                    @error('before_close_notif_minutes')
                        <small class="text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Batas Akhir Order (Menit sebelum tutup) -->
                <div class="form-group">
                    <label class="form-label" for="order_limit_minutes">Batas Akhir Pemesanan (Menit sebelum tutup)</label>
                    <div class="input-group">
                        <input
                            type="number"
                            id="order_limit_minutes"
                            name="order_limit_minutes"
                            class="form-control"
                            min="0"
                            max="60"
                            value="{{ old('order_limit_minutes', $settings['order_limit_minutes']) }}"
                            required>
                        <span class="input-group-text bg-light text-muted border-start-0" style="border-radius: 0 16px 16px 0; border: 1px solid #E5E7EB;">Menit</span>
                    </div>
                    @error('order_limit_minutes')
                        <small class="text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Footer Form -->
            <div class="form-footer">
                <a href="{{ route('dashboard') }}" class="btn-cancel">
                    <i class="bi bi-x-lg"></i> Batal
                </a>
                <button type="submit" class="btn-save">
                    <i class="bi bi-check-lg"></i> Simpan
                </button>
            </div>
        </form>
    </div>

</div>

@endsection
