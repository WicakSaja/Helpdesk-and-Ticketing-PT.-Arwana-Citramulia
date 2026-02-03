@extends('layouts.helpdesk')
@section('title', 'Tiket Masuk')

@section('css')
    @vite(['resources/css/helpdesk-incoming.css'])
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Tiket Masuk</h1>
            <p class="page-subtitle">Antrian tiket baru yang perlu ditugaskan.</p>
        </div>

        <div style="display:flex;align-items:center;gap:10px;">
            <div class="alert-badge">
                <i class="fa-solid fa-bell"></i>
                <span>0 Tiket Perlu Tindakan</span>
            </div>
            <button id="refreshTicketsBtn" class="btn-icon" title="Refresh"
                style="background:#fff; border:1px solid #e9e9e9; color:#333;">
                <i class="fa-solid fa-arrows-rotate"></i>
            </button>
        </div>
    </div>

    <div class="table-card">
        <table class="custom-table">
            <thead>
                <tr>
                    <th width="35%">Tiket Info</th>
                    <th width="20%">Lokasi</th>
                    <th width="15%">Kategori</th>
                    <th width="15%">Waktu</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody id="ticketsBody">
            </tbody>
        </table>
        <div id="ticketsPagination" style="margin-top:16px; display:flex; justify-content:flex-end; gap:8px;"></div>
    </div>

    <div class="modal-overlay" id="assignModal">
        <div class="modal-box">
            <h3 style="margin-bottom:20px; font-size: 18px; font-weight: 700;">Assign Teknisi</h3>

            <div
                style="margin-bottom:24px; background:#fff5f5; padding:18px; border-radius:10px; border: 1px solid #ffcdd2;">
                <div class="modal-info">
                    <div class="modal-ticket-number" id="modalTicketId">#NO</div>
                    <div class="modal-ticket-subject" id="modalTicketSubject">Subject</div>
                    <div id="modalAssignedTo" class="modal-assigned-to">User: -</div>
                    <div class="modal-desc">
                        <div class="modal-desc-title">Deskripsi</div>
                        <div id="modalTicketDesc" class="modal-desc-text">-</div>
                    </div>


                </div>
            </div>

            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px; color: #555;">Pilih
                    Teknisi:</label>
                <input type="hidden" id="modalTicketIdInput" value="">
                <select class="form-select" id="technicianSelect">
                    <option value="">-- Memuat teknisi... --</option>
                </select>
                <div id="technicianLoading" style="display:none; font-size:12px; color:#666; margin-top:6px;">Memuat daftar
                    teknisi...</div>
            </div>

            <div class="modal-footer">
                <button class="btn-cancel" id="assignCancelBtn">Batal</button>
                <button class="btn-save" id="assignSaveBtn">Simpan & Kirim</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Base API endpoints (use same URLs as dashboard for consistency)
        window.API_URL = '{{ url('') }}';
        window.DASHBOARD_API = '{{ url('/api/dashboard') }}';
        window.TICKET_API_BASE = '{{ url('/api/tickets') }}';
        window.TECHNICIANS_API = '{{ url('/api/users/by-role/technician') }}';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/helpdesk-incoming.js') }}?v={{ time() }}"></script>
@endsection
