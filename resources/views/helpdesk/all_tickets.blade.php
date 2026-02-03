@extends('layouts.helpdesk')
@section('title', 'Semua Data Tiket')

@section('css')
    @vite(['resources/css/helpdesk-all-tickets.css'])
    <style>
        .btn-reject {
            padding: 8px 14px;
            background-color: #d62828;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-reject:hover:not(:disabled) {
            background-color: #b71c1c;
        }

        .btn-reject:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-close {
            padding: 8px 14px;
            background-color: #388e3c;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-close:hover:not(:disabled) {
            background-color: #2e7d32;
        }

        .btn-close:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .timeline-item {
            position: relative;
            padding-left: 30px;
            margin-bottom: 20px;
        }

        .timeline-dot {
            position: absolute;
            left: 0;
            top: 3px;
            width: 12px;
            height: 12px;
            background-color: #999;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 0 0 2px #999;
        }

        .detail-group {
            margin-bottom: 20px;
        }

        .detail-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 13px;
            color: #555;
        }
    </style>
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
            <tbody id="ticketTableBody">
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
    <script src="{{ asset('js/helpdesk-all-tickets.js') }}?v={{ time() }}"></script>
@endsection
