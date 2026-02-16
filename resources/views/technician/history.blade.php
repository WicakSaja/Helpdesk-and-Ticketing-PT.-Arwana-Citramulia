@extends('layouts.technician')
@section('title', 'Riwayat Selesai')

@section('css')
    @vite(['resources/css/technician-history.css'])
@endsection

@section('content')
    <div class="page-header">
        <div class="header-left">
            <h1 class="page-title">Riwayat Pekerjaan</h1>
            <p class="page-subtitle">Daftar tiket yang telah diselesaikan.</p>
        </div>
        <div class="header-right">
            <!-- Filter Status -->
            <div class="filter-container">
                <button class="filter-btn filter-all active" data-status="all">
                    <i class="fa-solid fa-list"></i> Semua
                </button>
                <button class="filter-btn filter-resolved" data-status="resolved">
                    <i class="fa-solid fa-check-circle"></i> Resolved
                </button>
                <button class="filter-btn filter-closed" data-status="closed">
                    <i class="fa-solid fa-lock"></i> Closed
                </button>
            </div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>No Tiket</th>
                        <th>Judul Masalah</th>
                        <th>Requester</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody">
                    <tr>
                        <td colspan="6" class="loading-spinner">
                            Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="pagination-container" id="historyPagination" style="display: none;"></div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/technician-history.js') }}?v={{ time() }}"></script>
@endsection
