@extends('layouts.requester')
@section('title', 'Riwayat Tiket Saya')

@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/tickets.css') }}">

    <style>
        /* fallback small inline overrides (kept minimal) */
        /* Copy style CSS dari kode Mas Fadhli yang terakhir dikirim, atau pakai yang di bawah ini */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .page-title {
            font-size: 26px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .btn-create {
            background: #d62828;
            color: white;
            padding: 12px 25px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
            box-shadow: 0 4px 10px rgba(214, 40, 40, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
            /* keep button to right */
        }

        /* Responsive: avoid stacking by making button smaller on mobile */
        @media (max-width: 640px) {
            .page-header {
                flex-wrap: nowrap;
                gap: 12px;
            }

            .btn-create {
                padding: 10px 14px;
                font-size: 13px;
            }
        }

        .btn-create:hover {
            background: #b01f1f;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(214, 40, 40, 0.3);
            color: white;
        }

        .table-container {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
        }

        .ticket-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .ticket-table th {
            text-align: left;
            color: #888;
            padding: 15px;
            border-bottom: 2px solid #f0f0f0;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ticket-table td {
            padding: 20px 15px;
            border-bottom: 1px solid #f9f9f9;
            font-size: 14px;
            color: #333;
            vertical-align: middle;
        }

        .ticket-table tr:hover td {
            background: #fcfcfc;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .st-open {
            background: #e3f2fd;
            color: #1976d2;
        }

        .st-progress {
            background: #fff3e0;
            color: #e65100;
        }

        .st-resolved {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .st-closed {
            background: #eceff1;
            color: #455a64;
        }

        .st-assigned {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .btn-detail {
            background: white;
            border: 1px solid #eee;
            color: #555;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            transition: 0.3s;
            cursor: pointer;
            display: inline-block;
        }

        .btn-detail:hover {
            background: #fdfdfd;
            color: #d62828;
            border-color: #d62828;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(2px);
        }

        .modal-box {
            background: white;
            width: 500px;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.3s ease;
            position: relative;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #999;
            cursor: pointer;
        }

        .detail-row {
            display: flex;
            margin-bottom: 15px;
        }

        .detail-label {
            width: 140px;
            color: #777;
            font-size: 13px;
            font-weight: 500;
        }

        .detail-value {
            flex: 1;
            color: #333;
            font-size: 14px;
            font-weight: 600;
        }

        .detail-desc {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 10px;
            font-size: 14px;
            color: #555;
            line-height: 1.6;
            margin-top: 10px;
            border: 1px solid #eee;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Riwayat Tiket</h1>
            <p style="color: #777; font-size: 14px; margin-top: 5px;">Pantau status laporan kendala Anda disini.</p>
        </div>
        <a href="{{ route('tickets.create') }}" class="btn-create"><i class="fa-solid fa-plus"></i> Buat Tiket Baru</a>
    </div>

    <div class="table-container">
        <table class="ticket-table">
            <thead>
                <tr>
                    <th>Subjek</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Update Terakhir</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody id="ticketTableBody">
                <tr>
                    <td colspan="5" style="text-align:center; padding:40px; color:#999;">
                        <i class="fa-solid fa-spinner fa-spin" style="font-size:24px;"></i>
                        <p style="margin-top:10px;">Memuat riwayat tiket Anda...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="myModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 style="font-size: 18px; font-weight: 700; color: #333;">Detail Tiket</h3>
                <button type="button" class="btn-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="detail-row"><span class="detail-label">ID Tiket</span><span class="detail-value"
                    id="dId">-</span></div>
            <div class="detail-row"><span class="detail-label">Subjek</span><span class="detail-value"
                    id="dSub">-</span></div>
            <div class="detail-row"><span class="detail-label">Kategori</span><span class="detail-value"
                    id="dCat">-</span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value" id="dStat"
                    style="color:#d62828;">-</span></div>
            <div class="detail-row"><span class="detail-label">Update Terakhir</span><span class="detail-value"
                    id="dTime">-</span></div>
            <div style="margin-top: 20px;">
                <span class="detail-label">Deskripsi:</span>
                <div class="detail-desc" id="dDesc">...</div>
            </div>
            <div style="margin-top: 25px; text-align: right;">
                <button type="button" onclick="closeModal()"
                    style="background:#eee; border:none; padding:10px 25px; border-radius:8px; font-weight:600; cursor:pointer; color:#555;">Tutup</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/auth-token-manager.js') }}"></script>
    <script src="{{ asset('js/tickets-index.js') }}?v={{ time() }}"></script>
@endsection
