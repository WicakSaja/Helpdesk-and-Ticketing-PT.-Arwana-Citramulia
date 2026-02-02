@extends('layouts.requester')
@section('title', 'Riwayat Tiket Saya')

@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/ticket-style.css'])

    <!-- ticket styles are loaded via ticket-style.css -->
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Riwayat Tiket</h1>
            <p class="page-subtitle">Pantau status laporan kendala Anda disini.</p>
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
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="ticketTableBody">
                <tr>
                    <td colspan="5" class="loading-cell">
                        <i class="fa-solid fa-spinner fa-spin loading-icon"></i>
                        <p>Memuat riwayat tiket Anda...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="myModal" class="modal-overlay" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="modal-box" role="document" tabindex="-1">
            <div class="modal-header">
                <h3 class="modal-title">Detail Tiket</h3>
                <button type="button" class="btn-close" onclick="closeModal()" aria-label="Tutup">&times;</button>
            </div>

            <!-- Loading spinner while fetching details -->
            <div id="detailLoading" style="display:none; text-align:center; padding:30px;">
                <i class="fa-solid fa-spinner fa-spin" style="font-size:28px; color:#d62828;"></i>
                <p style="color:#666; margin-top:10px;">Memuat detail tiket...</p>
            </div>

            <div id="detailContent">
                <div class="detail-row"><span class="detail-label">ID Tiket</span><span class="detail-value"
                        id="dId">-</span></div>
                <div class="detail-row"><span class="detail-label">Pemohon</span><span class="detail-value"
                        id="dRequester">-</span></div>
                <div class="detail-row"><span class="detail-label">Subjek</span><span class="detail-value"
                        id="dSub">-</span></div>
                <div class="detail-row"><span class="detail-label">Kategori</span><span class="detail-value"
                        id="dCat">-</span></div>
                <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value status-highlight"
                        id="dStat">-</span></div>
                <div class="detail-row"><span class="detail-label">Update Terakhir</span><span class="detail-value"
                        id="dTime">-</span></div>
                <div style="margin-top: 20px;">
                    <span class="detail-label">Deskripsi:</span>
                    <div class="detail-desc" id="dDesc">...</div>
                </div>
                <div style="margin-top: 25px; text-align: right;">
                    <button type="button" onclick="closeModal()" class="btn-default">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination controls inserted below table -->
    <div id="ticketPagination" class="pagination-wrapper"
        style="margin-top:18px; display:flex; justify-content:flex-end; gap:8px;"></div>
@endsection

@section('scripts')
    <script src="{{ asset('js/auth-token-manager.js') }}"></script>
    <script src="{{ asset('js/tickets-index.js') }}?v={{ time() }}"></script>
@endsection
