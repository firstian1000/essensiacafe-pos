@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu.css') }}?v=23">
@endpush

@section('title','Edit Pengeluaran')

@section('content')
<div class="menu-page">
    <div class="page-header">
        <div>
            <h1>Edit Pengeluaran</h1>
            <div class="breadcrumb-custom">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <span>></span>
                <a href="{{ route('expenses.index') }}">Pengeluaran</a>
                <span>></span>
                <span>Edit</span>
            </div>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('expenses.update', $expense) }}" method="POST">
            @csrf
            @method('PUT')

            @include('expenses.form', ['expense' => $expense])
        </form>
    </div>
</div>
@endsection
