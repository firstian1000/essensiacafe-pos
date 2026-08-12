@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu.css') }}?v=23">
@endpush

@section('title','Pengeluaran')

@section('content')
<div class="menu-page">
    <div class="page-header">
        <div>
            <h1>Pengeluaran</h1>
            <div class="breadcrumb-custom">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <span>></span>
                <span>Pengeluaran</span>
            </div>
        </div>
    </div>

    <div class="menu-card">
        <div class="empty">
            <i class="bi bi-database-exclamation fs-1 d-block mb-3"></i>
            Tabel pengeluaran belum tersedia. Jalankan migration terlebih dahulu.
            <div class="mt-3"><code>php artisan migrate</code></div>
        </div>
    </div>
</div>
@endsection
