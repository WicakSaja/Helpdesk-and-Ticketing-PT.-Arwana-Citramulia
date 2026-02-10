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
            <div class="stats-badge pending-badge">
                <i class="fa-solid fa-clipboard-list"></i>
                <span id="pendingCountNum">0</span>
                <span class="badge-text">Tiket Perlu Dikerjakan</span>
            </div>
            <div class="filter-dropdown">
                <select id="statusFilter" onchange="loadHistory()">
                    <option value="">Semua Status</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>ID Tiket</th>
                        <th>Judul Masalah</th>
                        <th>Requester / Divisi</th>
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
