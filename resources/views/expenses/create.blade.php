@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu.css') }}?v=23">
@endpush

@section('title','Tambah Pengeluaran')

@section('content')
<div class="menu-page">
    <div class="page-header">
        <div>
            <h1>Tambah Pengeluaran</h1>
            <div class="breadcrumb-custom">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <span>></span>
                <a href="{{ route('expenses.index') }}">Pengeluaran</a>
                <span>></span>
                <span>Tambah</span>
            </div>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('expenses.store') }}" method="POST">
            @csrf

            @include('expenses.form', ['expense' => null])
        </form>
    </div>
</div>
@endsection
