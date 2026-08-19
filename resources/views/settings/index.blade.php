@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu.css') }}?v=11">
<style>
.setting-section-title{display:flex;align-items:center;justify-content:space-between;gap:16px}
.setting-switch{display:inline-flex;align-items:center;gap:10px;font-size:14px;font-weight:900;color:#64748B}
.setting-switch input{position:absolute;opacity:0;pointer-events:none}
.setting-switch-slider{width:58px;height:32px;border-radius:999px;background:#E5E7EB;position:relative;transition:.2s ease}
.setting-switch-slider::after{content:"";width:24px;height:24px;border-radius:50%;background:#fff;position:absolute;top:4px;left:4px;box-shadow:0 4px 10px rgba(15,23,42,.18);transition:.2s ease}
.setting-switch input:checked + .setting-switch-slider{background:#2E7DB8}
.setting-switch input:checked + .setting-switch-slider::after{transform:translateX(26px)}
.settings-disabled{opacity:.45;filter:grayscale(.25)}
</style>
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

            <h4 class="mb-4 fw-bold text-dark border-bottom pb-2 setting-section-title">
                <span><i class="bi bi-clock-fill text-primary me-2"></i> Jam Operasional Kafe</span>
                <label class="setting-switch">
                    <span>Aktif</span>
                    <input type="hidden" name="operational_settings_enabled" value="0">
                    <input type="checkbox" name="operational_settings_enabled" value="1" class="js-setting-switch" data-target="operational-settings-fields" {{ old('operational_settings_enabled', $settings['operational_settings_enabled']) === '1' ? 'checked' : '' }}>
                    <span class="setting-switch-slider"></span>
                </label>
            </h4>

            <div class="form-row" data-setting-fields="operational-settings-fields">
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

            <h4 class="mt-5 mb-4 fw-bold text-dark border-bottom pb-2 setting-section-title">
                <span><i class="bi bi-people-fill text-primary me-2"></i> Pergantian Shift Pekerja</span>
                <label class="setting-switch">
                    <span>Aktif</span>
                    <input type="hidden" name="shift_settings_enabled" value="0">
                    <input type="checkbox" name="shift_settings_enabled" value="1" class="js-setting-switch" data-target="shift-settings-fields" {{ old('shift_settings_enabled', $settings['shift_settings_enabled']) === '1' ? 'checked' : '' }}>
                    <span class="setting-switch-slider"></span>
                </label>
            </h4>

            <div class="form-row" data-setting-fields="shift-settings-fields">
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

            <h4 class="mt-5 mb-4 fw-bold text-dark border-bottom pb-2 setting-section-title">
                <span><i class="bi bi-bell-fill text-primary me-2"></i> Peringatan Tutup & Batas Order</span>
                <label class="setting-switch">
                    <span>Aktif</span>
                    <input type="hidden" name="close_order_settings_enabled" value="0">
                    <input type="checkbox" name="close_order_settings_enabled" value="1" class="js-setting-switch" data-target="close-order-settings-fields" {{ old('close_order_settings_enabled', $settings['close_order_settings_enabled']) === '1' ? 'checked' : '' }}>
                    <span class="setting-switch-slider"></span>
                </label>
            </h4>

            <div class="form-row" data-setting-fields="close-order-settings-fields">
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

            <h4 class="mt-5 mb-4 fw-bold text-dark border-bottom pb-2">
                <i class="bi bi-wifi text-primary me-2"></i> Pengaturan WiFi Nota
            </h4>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="wifi_username">Username / Nama WiFi</label>
                    <input
                        type="text"
                        id="wifi_username"
                        name="wifi_username"
                        class="form-control"
                        value="{{ old('wifi_username', $settings['wifi_username']) }}"
                        placeholder="Contoh: Esensia Koffie">
                    @error('wifi_username')
                        <small class="text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="wifi_password">Password WiFi</label>
                    <input
                        type="text"
                        id="wifi_password"
                        name="wifi_password"
                        class="form-control"
                        value="{{ old('wifi_password', $settings['wifi_password']) }}"
                        placeholder="Masukkan password WiFi">
                    @error('wifi_password')
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

@push('scripts')
<script>
document.querySelectorAll('.js-setting-switch').forEach(toggle => {
    function syncSection() {
        const target = document.querySelector(`[data-setting-fields="${toggle.dataset.target}"]`);
        if (!target) return;
        target.classList.toggle('settings-disabled', !toggle.checked);
    }

    toggle.addEventListener('change', syncSection);
    syncSection();
});
</script>
@endpush
