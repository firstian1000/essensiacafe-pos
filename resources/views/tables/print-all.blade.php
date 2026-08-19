@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/print-qr.css') }}">
@endpush

@section('title','Cetak Semua QR')

@section('content')

<div class="print-page">

    <div class="page-header">

        <div>

            <h1>Preview Cetak QR</h1>
            <p class="print-note">

                <i class="bi bi-info-circle-fill"></i>

                    Gunakan kertas <strong>A4</strong> untuk mencetak barcode
            </p>
        </div>

        <div class="header-actions">

            <a
                href="{{ route('tables.index') }}"
                class="btn-back">

                <i class="bi bi-arrow-left"></i>

                Kembali

            </a>

            <button
                onclick="window.print()"
                class="btn-print">

                <i class="bi bi-printer"></i>

                Cetak

            </button>

        </div>

    </div>

    <div class="print-card">

        <div class="qr-grid">

        @foreach($tables as $table)

<div class="qr-item">

    <div class="company-name">

        Esensia Koffie

    </div>

    <div class="table-name">

        {{ strtoupper($table->table_number) }}

    </div>

    <div class="qr-box">

        <img
            src="{{ $table->qr_image_url }}"
            alt="Meja {{ $table->table_number }}">

    </div>

    <div class="scan-text">

        Scan QR Code untuk melakukan pemesanan

    </div>

    <div class="website-text">

        {{ url('/') }}

    </div>

</div>

@endforeach

        </div>

    </div>

</div>

@endsection

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const printButton = document.querySelector('.btn-print');

    if(printButton){

        printButton.addEventListener('click', function(){

            window.print();

        });

    }

});

</script>

@endpush
