@extends('layouts.helpdesk')
@section('title', 'Semua Data Tiket')

@section('css')
    @vite(['resources/css/helpdesk-all-tickets.css'])
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Semua Data Tiket</h1>
        
        <div class="search-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Cari tiket, subjek, atau pengaju...">
        </div>
    </div>

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>No. Tiket</th>
                    <th>Subjek / Pengaju</th>
                    <th>Departemen</th>
                    <th>Teknisi</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <tr>
                    <td colspan="6" class="loading-row">
                        <i class="fa-solid fa-spinner fa-spin" style="font-size: 24px;"></i>
                        <p style="margin-top: 10px;">Menginisialisasi...</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <div id="noDataMessage" style="display: none; text-align: center; padding: 30px; color: #888;">
            <i class="fa-solid fa-folder-open" style="font-size: 24px; margin-bottom: 10px;"></i>
            <p>Tidak ada data tiket ditemukan.</p>
        </div>

        <div class="pagination-wrapper">
            <div id="paginationInfo" style="font-size: 13px; color: #666;"></div>
            <div class="pagination-controls" id="paginationControls"></div>
        </div>
    </div>

    <div id="detailModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Detail Tiket <span id="mId"></span></h3>
                <span class="close-modal" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="modalDetailContent">
                <div style="margin-bottom: 20px;">
                    <h4 id="mSubject" style="font-size: 18px; font-weight: 700; color: #333;"></h4>
                    <p id="mDept" style="color: #666; font-size: 13px;"></p>
                </div>
                
                <div class="detail-group">
                    <label class="detail-label">Riwayat Perjalanan</label>
                    <div class="timeline" id="mTimeline"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Definisi Global API URL (Backup jika belum ada)
        if (typeof API_URL === 'undefined') var API_URL = 'http://127.0.0.1:8000';
    </script>
    <script src="{{ asset('js/helpdesk-all-tickets.js') }}?v={{ time() }}"></script>
@endsection