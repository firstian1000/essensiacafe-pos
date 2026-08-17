@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/table.css') }}?v=12">
@endpush

@section('title','Meja')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="table-page">

    <!-- Header -->
    <div class="page-header">

        <div>

            <h1>Daftar Meja</h1>

            <div class="breadcrumb-custom">

                <a href="{{ route('dashboard') }}">
                    Dashboard
                </a>

                <span>></span>

                <span>Meja</span>

            </div>

        </div>

        <div class="header-actions">

            <a href="{{ route('tables.print.all') }}"
               class="btn-print">

                <i class="bi bi-qr-code"></i>

                Cetak Semua QR

            </a>

            <a href="{{ route('tables.create') }}"
               class="btn-add">

                <i class="bi bi-plus-lg"></i>

                Tambah Meja

            </a>

        </div>

    </div>

    <!-- Statistik -->

    <div class="stats-grid">

        <div class="stat-card">

            <div class="stat-icon blue">

                <i class="bi bi-grid-3x3-gap"></i>

            </div>

            <div>

                <small>Total Meja</small>

                <h2>{{ $tables->count() }}</h2>

                <p>Semua meja terdaftar</p>

            </div>

        </div>

        <div class="stat-card">

            <div class="stat-icon green">

                <i class="bi bi-check-circle"></i>

            </div>

            <div>

                <small>Tersedia</small>

                <h2>{{ $tables->where('status','available')->count() }}</h2>

                <p>Siap digunakan</p>

            </div>

        </div>

        <div class="stat-card">

            <div class="stat-icon orange">

                <i class="bi bi-people"></i>

            </div>

            <div>

                <small>Terisi</small>

                <h2>{{ $tables->where('status','occupied')->count() }}</h2>

                <p>Sedang digunakan</p>

            </div>

        </div>

        <div class="stat-card">

            <div class="stat-icon purple">

                    <i class="bi bi-calendar-check"></i>

            </div>

            <div>

                <small>Dipesan</small>

                <h2>{{ $tables->where('status','reserved')->count() }}</h2>

                <p>Sudah dipesan</p>

            </div>

        </div>

    </div>

    <!-- Toolbar -->

    <div class="toolbar">

        <form
            action="{{ route('tables.index') }}"
            method="GET"
            class="toolbar-form">

            <div class="search-box">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nomor meja...">

            </div>

        </form>

        <div class="toolbar-right">

            <select
                class="filter-select"
                onchange="window.location=this.value">

                <option value="{{ route('tables.index') }}">
                    Semua Status
                </option>

                <option
                    value="{{ route('tables.index',['status'=>'available']) }}"
                    {{ request('status')=='available' ? 'selected' : '' }}>

                    Tersedia

                </option>

                <option
                    value="{{ route('tables.index',['status'=>'occupied']) }}"
                    {{ request('status')=='occupied' ? 'selected' : '' }}>

                    Terisi

                </option>

                <option
                    value="{{ route('tables.index',['status'=>'reserved']) }}"
                    {{ request('status')=='reserved' ? 'selected' : '' }}>

                    Dipesan

                </option>

            </select>

        </div>

    </div>

    <!-- Grid Card -->

    <div class="table-grid">

        @forelse($tables as $table)

                <div class="table-card">

            <div class="table-card-header">

                <h4>

                    {{ $table->table_number }}

                </h4>

                @if($table->status=='available')

                    <span class="badge-status available">

                        Tersedia

                    </span>

                @elseif($table->status=='occupied')

                    <span class="badge-status occupied">

                        Terisi

                    </span>

                @elseif($table->status=='reserved')

                    <span class="badge-status reserved">

                        Dipesan

                    </span>

                @else

                    <span class="badge-status available">

                        Tersedia

                    </span>

                @endif

            </div>

            <!-- QR Code -->

            <div class="qr-wrapper">

                <img
                    src="{{ $table->qr_image_url }}"
                    alt="QR Code {{ $table->table_number }}">

            </div>

            <!-- Status -->

            <div class="table-status">

                @if($table->status=='available')

                    <span class="status-dot available"></span>

                    <strong>Available</strong>

                    <small>Siap digunakan</small>

                @elseif($table->status=='occupied')

                    <span class="status-dot occupied"></span>

                    <strong>Sedang Digunakan</strong>

                    <small>Meja sedang dipakai</small>

                @elseif($table->status=='reserved')

                    <span class="status-dot reserved"></span>

                    <strong>Dipesan</strong>

                    <small>Meja sudah dipesan</small>

                @else

                    <span class="status-dot available"></span>

                    <strong>Available</strong>

                    <small>Siap digunakan</small>

                @endif

            </div>

            <form
                action="{{ route('tables.status', $table->id) }}"
                method="POST"
                class="table-status-form">

                @csrf
                @method('PUT')

                <label for="table-status-{{ $table->id }}">Pengaturan meja</label>

                <select
                    id="table-status-{{ $table->id }}"
                    name="status"
                    class="table-status-select"
                    onchange="this.form.submit()">

                    <option value="available" {{ $table->status === 'available' ? 'selected' : '' }}>
                        Tersedia
                    </option>

                    <option value="occupied" {{ $table->status === 'occupied' ? 'selected' : '' }}>
                        Terisi
                    </option>

                    <option value="reserved" {{ $table->status === 'reserved' ? 'selected' : '' }}>
                        Dipesan
                    </option>

                </select>

            </form>

            <!-- Button -->

            <div class="table-actions">

                <a
                    href="{{ route('tables.download',$table->id) }}"
                    class="btn-download">

                    <i class="bi bi-download"></i>

                    Download

                </a>

                <a
                    href="{{ route('customer.menu',$table->qr_token) }}"
                    target="_blank"
                    class="btn-menu">

                    <i class="bi bi-eye"></i>

                    Lihat Menu

                </a>

            </div>

            <!-- Footer -->

            <div class="table-footer">

                <a
                    href="{{ route('tables.edit',$table->id) }}"
                    class="btn-edit">

                    <i class="bi bi-pencil"></i>

                </a>

                <form
                    action="{{ route('tables.destroy',$table->id) }}"
                    method="POST"
                    onsubmit="return confirm('Yakin ingin menghapus meja ini?')">

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn-delete">

                        <i class="bi bi-trash"></i>

                    </button>

                </form>

            </div>

        </div>

        @empty

        <div class="empty-data">

            <i class="bi bi-grid-3x3-gap"></i>

            <h4>Belum ada data meja</h4>

            <p>Silakan tambahkan meja terlebih dahulu.</p>

        </div>

        @endforelse

            </div>
    <!-- End Table Grid -->

    @if(method_exists($tables,'links'))

    <div class="pagination-wrapper">

        {{ $tables->links() }}

    </div>

    @endif

</div>

@endsection
