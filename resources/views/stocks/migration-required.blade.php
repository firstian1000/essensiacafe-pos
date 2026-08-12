@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu.css') }}?v=23">
@endpush

@section('title','Stok')

@section('content')
<div class="menu-page">
    <div class="page-header">
        <div>
            <h1>Stok</h1>
            <div class="breadcrumb-custom">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <span>></span>
                <span>Stok</span>
            </div>
        </div>
    </div>

    <div class="form-card">
        <div class="text-center py-5">
            <i class="bi bi-database-exclamation fs-1 d-block mb-3 text-warning"></i>
            <h3 class="fw-bold">Database stok belum aktif</h3>
            <p class="text-muted mb-3">Jalankan migrasi agar Stok internal bisa digunakan.</p>
            <code>php artisan migrate</code>
        </div>
    </div>
</div>
@endsection
