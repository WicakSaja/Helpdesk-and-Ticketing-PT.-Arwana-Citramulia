@extends('layouts.helpdesk')
@section('title', 'Laporan Berkala')

@section('css')
    @vite(['resources/css/report.css'])
@endsection

@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Rekapitulasi Laporan</h1>
            <p style="color:#64748b; margin-top:5px; font-size:14px;">Arsip detail tiket dan riwayat pengerjaan</p>
        </div>
        <button class="btn-export" onclick="downloadExcel(event)">
            <i class="fa-solid fa-file-excel"></i> Download Excel
        </button>
    </div>

    {{-- Controls Container (Responsive) --}}
    <div class="controls-wrapper">
        {{-- Kiri: Filters --}}
        <div class="filters-left">
            <div id="filterYearGroup" class="filter-group">
                <label for="selectYear" class="filter-label">Tahun</label>
                <select class="filter-select" id="selectYear">
                    {{-- Diisi JS --}}
                </select>
            </div>
            <div id="filterMonthGroup" class="filter-group">
                <label for="selectMonth" class="filter-label">Bulan</label>
                <select class="filter-select" id="selectMonth">
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
            </div>
            <div id="filterWeekGroup" class="filter-group" style="display: none;">
                <label for="selectWeek" class="filter-label">Minggu</label>
                <select class="filter-select" id="selectWeek">
                    {{-- Diisi JS --}}
                </select>
            </div>
        </div>

        {{-- Kanan: Tabs --}}
        <div class="tabs">
            <button class="tab-btn active" id="tabWeekly">Mingguan</button>
            <button class="tab-btn" id="tabMonthly">Bulanan</button>
            <button class="tab-btn" id="tabYearly">Tahunan</button>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="table-card">
        <div class="table-header-info">
            <h3 id="labelPeriode" style="margin: 0; font-size: 16px; color: #1e293b; font-weight:700;">Memuat...</h3>
            <span style="font-size:12px; color:#94a3b8; font-style:italic;">*Data realtime server</span>
        </div>

        {{-- Responsive Wrapper --}}
        <div class="table-responsive">
            <table class="report-table">
                <thead id="tableHead">
                    {{-- Header Diisi JS --}}
                </thead>
                <tbody id="tableBody">
                    {{-- Body Diisi JS --}}
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="pagination-container" id="paginationContainer" style="display: none;">
            <div class="pagination-info">
                <span>Menampilkan <strong id="paginationStart">1</strong> hingga <strong id="paginationEnd">10</strong> dari
                    <strong id="paginationTotal">0</strong> data</span>
            </div>
            <div class="pagination-buttons" id="paginationButtons">
                {{-- Diisi JS --}}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/report.js') }}?v={{ time() }}"></script>
@endsection
