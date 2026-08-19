@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu.css') }}?v=23">
<style>
.expense-table-header,.expense-item{grid-template-columns:70px 160px minmax(260px,2fr) 170px minmax(220px,1.4fr) 150px!important}
.expense-badge{display:inline-flex;align-items:center;justify-content:center;min-width:92px;height:36px;border-radius:999px;padding:0 14px;font-weight:950;background:#EAF4FB;color:#2E7DB8}
.expense-total-box{display:flex;align-items:center;gap:14px;padding:18px 22px;margin-bottom:18px;border:1px solid #DDEAF7;border-radius:18px;background:#F8FBFF}
.expense-total-box i{width:52px;height:52px;border-radius:16px;background:#FFF8E1;color:#D97706;display:inline-flex;align-items:center;justify-content:center;font-size:24px}
.expense-total-box span{display:block;color:#64748B;font-weight:800}.expense-total-box strong{font-size:28px;color:#0F172A}
</style>
@endpush

@section('title','Pengeluaran')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

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
        <a href="{{ route('expenses.create') }}" class="btn-add">
            <i class="bi bi-plus-lg"></i>
            Tambah Pengeluaran
        </a>
    </div>

    <div class="menu-card">
        <div class="expense-total-box">
            <i class="bi bi-wallet2"></i>
            <div>
                <span>Total Pengeluaran Terfilter</span>
                <strong>Rp {{ number_format($totalExpense,0,',','.') }}</strong>
            </div>
        </div>

        <div class="toolbar">
            <form action="{{ route('expenses.index') }}" method="GET" class="toolbar-form" style="display:flex;justify-content:space-between;align-items:center;width:100%;gap:15px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="font-size:15px;font-weight:600;color:#4B5563;">Tampilkan:</span>
                    <select name="per_page" class="status-select" style="width:80px;height:48px;border-radius:12px;border:1px solid #D1D5DB;padding:0 12px;font-size:14px;background-color:#fff;cursor:pointer;" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('per_page', '15') == '15' ? 'selected' : '' }}>15</option>
                        <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20</option>
                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                    </select>
                </div>

                <div style="display:flex;gap:10px;align-items:center;">
                    <div class="search-box" style="flex:initial;width:280px;margin-bottom:0;border:2px solid #2563EB;box-shadow:0 4px 12px rgba(37,99,235,.08);background-color:#F8FAFC!important;overflow:hidden;">
                        <i class="bi bi-search" style="color:#2563EB;font-weight:bold;"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pengeluaran..." style="background-color:transparent!important;border:none!important;outline:none!important;box-shadow:none!important;width:100%;height:100%;font-size:15px;">
                    </div>
                    <button type="submit" class="btn-save" style="width:auto;height:48px;padding:0 24px;border-radius:12px;margin:0;">Cari</button>
                </div>
            </form>
        </div>

        <div class="table-wrapper">
            <div class="table-header expense-table-header">
                <div>No</div>
                <div>Tanggal</div>
                <div>Nama</div>
                <div>Nominal</div>
                <div>Catatan</div>
                <div>Aksi</div>
            </div>

            @forelse($expenses as $expense)
            <div class="menu-item expense-item">
                <div class="number" data-label="No">{{ ($expenses->currentPage() - 1) * $expenses->perPage() + $loop->iteration }}</div>
                <div data-label="Tanggal">{{ $expense->expense_date->format('d M Y') }}</div>
                <div class="menu-info" data-label="Nama">
                    <h5>{{ $expense->name }}</h5>
                </div>
                <div data-label="Nominal"><strong>Rp {{ number_format($expense->amount,0,',','.') }}</strong></div>
                <div data-label="Catatan">{{ $expense->note ?: '-' }}</div>
                <div class="action-buttons" data-label="Aksi">
                    <a href="{{ route('expenses.edit', $expense) }}" class="btn-edit"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Hapus pengeluaran ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <div class="empty">
                <i class="bi bi-wallet2 fs-1 d-block mb-3"></i>
                Belum ada data pengeluaran.
            </div>
            @endforelse
        </div>

        <div class="pagination-wrapper">{{ $expenses->withQueryString()->links() }}</div>
    </div>
</div>

@endsection
