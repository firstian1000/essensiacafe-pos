@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/menu.css') }}?v=23">
<style>
.stock-table-header,.stock-item{grid-template-columns:70px minmax(260px,2fr) 160px 160px 150px!important}
.stock-badge{display:inline-flex;align-items:center;justify-content:center;min-width:82px;height:36px;border-radius:999px;padding:0 14px;font-weight:950;background:#EAF4FB;color:#2E7DB8}
.stock-badge.empty{background:#FFF1F2;color:#DC2626}.stock-badge.unlimited{background:#F1F5F9;color:#64748B}.stock-badge.low{background:#FEF3C7;color:#B45309}
</style>
@endpush

@section('title','Stok')

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
            <h1>Stok</h1>
            <div class="breadcrumb-custom">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <span>></span>
                <span>Stok</span>
            </div>
        </div>
        <a href="{{ route('stocks.create') }}" class="btn-add">
            <i class="bi bi-plus-lg"></i>
            Tambah Stok
        </a>
    </div>

    <div class="menu-card">
        <div class="toolbar">
            <form action="{{ route('stocks.index') }}" method="GET" class="toolbar-form" style="display:flex;justify-content:space-between;align-items:center;width:100%;gap:15px;flex-wrap:wrap;">
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
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama stok..." style="background-color:transparent!important;border:none!important;outline:none!important;box-shadow:none!important;width:100%;height:100%;font-size:15px;">
                    </div>
                    <button type="submit" class="btn-save" style="width:auto;height:48px;padding:0 24px;border-radius:12px;margin:0;">Cari</button>
                </div>
            </form>
        </div>

        <div class="table-wrapper">
            <div class="table-header stock-table-header">
                <div>No</div>
                <div>Nama Stok</div>
                <div>Stok</div>
                <div>Status</div>
                <div>Aksi</div>
            </div>

            @forelse($stockItems as $stockItem)
            @php
                $stockClass = is_null($stockItem->stock) ? 'unlimited' : ($stockItem->stock <= 0 ? 'empty' : ($stockItem->stock <= 5 ? 'low' : ''));
                $stockLabel = is_null($stockItem->stock) ? 'Tidak dibatasi' : $stockItem->stock;
            @endphp
            <div class="menu-item stock-item">
                <div class="number" data-label="No">{{ ($stockItems->currentPage() - 1) * $stockItems->perPage() + $loop->iteration }}</div>
                <div class="menu-info" data-label="Nama Stok">
                    <h5>{{ $stockItem->name }}</h5>
                    <p>Stok internal admin</p>
                </div>
                <div data-label="Stok"><span class="stock-badge {{ $stockClass }}">{{ $stockLabel }}</span></div>
                <div data-label="Status">
                    <span class="stock-badge {{ $stockItem->status ? '' : 'empty' }}">{{ $stockItem->status ? 'Aktif' : 'Habis' }}</span>
                </div>
                <div class="action-buttons" data-label="Aksi">
                    <a href="{{ route('stocks.edit', $stockItem) }}" class="btn-edit"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('stocks.destroy', $stockItem) }}" method="POST" onsubmit="return confirm('Hapus stok ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete" title="Hapus Stok" aria-label="Hapus Stok"><i class="bi bi-trash3-fill"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <div class="empty">
                <i class="bi bi-box-seam fs-1 d-block mb-3"></i>
                Belum ada data stok.
            </div>
            @endforelse
        </div>

        @if(method_exists($stockItems,'links'))
        <div class="pagination-wrapper">{{ $stockItems->withQueryString()->links() }}</div>
        @endif
    </div>
</div>

@endsection
