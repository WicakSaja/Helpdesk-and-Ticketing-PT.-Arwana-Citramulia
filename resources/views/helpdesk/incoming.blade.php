@extends('layouts.helpdesk')
@section('title', 'Tiket Masuk')

@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/helpdesk-incoming.css'])
@endsection

@section('content')
    <div class="page-header">
        <div class="header-left">
            <h1 class="page-title">Tiket Masuk</h1>
            <p class="page-subtitle">Antrian tiket baru yang perlu ditugaskan.</p>
        </div>

        <div class="header-right">
            <div class="search-container">
                <input type="text" id="searchInput" class="search-input" placeholder="Cari tiket, nomor, atau requester..."
                    autocomplete="off">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
            </div>

            <div class="alert-badge">
                <i class="fa-solid fa-bell"></i>
                <span id="pendingCountNum">0</span>
                <span class="badge-text">&nbsp;Tiket Perlu Tindakan</span>
            </div>
            <button id="refreshTicketsBtn" class="btn-refresh" title="Refresh Data">
                <i class="fa-solid fa-arrows-rotate"></i>
            </button>
        </div>
    </div>

    <div class="table-card">
        {{-- WRAPPER RESPONSIVE --}}
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Tiket Info</th>
                        <th>Divisi</th>
                        <th>Kategori</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="ticketsBody">
                    {{-- Loading State --}}
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #999;">
                            <i class="fa-solid fa-spinner fa-spin fa-2x mb-2"></i><br>
                            Memuat antrian tiket...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination & Info --}}
        <div class="footer-actions">
            <div id="ticketsPagination" class="pagination-container"></div>
        </div>
    </div>

    {{-- MODAL ASSIGN --}}
    <div class="modal-overlay" id="assignModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Assign Teknisi</h3>
                <span class="close-modal" id="closeAssignModal">&times;</span>
            </div>

            <div class="ticket-summary">
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

            <div class="form-group">
                <label class="form-label">Pilih Teknisi:</label>
                <input type="hidden" id="modalTicketIdInput" value="">

                <div style="position: relative;">
                    <select class="form-select" id="technicianSelect">
                        <option value="">-- Memuat teknisi... --</option>
                    </select>
                    <i class="fa-solid fa-chevron-down select-icon"></i>
                </div>

                <div id="technicianLoading" class="loading-text">
                    <i class="fa-solid fa-circle-notch fa-spin"></i> Memuat daftar teknisi...
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan (Opsional):</label>
                <textarea id="assignNotes" class="form-textarea" placeholder="Contoh: Tolong segera dicek ke lokasi..."></textarea>
            </div>

            <div class="modal-footer">
                <button class="btn-cancel" id="assignCancelBtn">Batal</button>
                <button class="btn-save" id="assignSaveBtn">
                    <i class="fa-solid fa-check"></i> Assign Sekarang
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // API Configuration
        window.API_URL = '{{ url('/') }}';
        window.DASHBOARD_API = '{{ url('/api/dashboard') }}';
        window.TICKET_API_BASE = '{{ url('/api/tickets') }}';
        window.TECHNICIANS_API = '{{ url('/api/users/by-role/technician') }}';

        // Modal Close Helper (Tambahan agar tombol X jalan)
        document.getElementById('closeAssignModal')?.addEventListener('click', function() {
            document.getElementById('assignModal').style.display = 'none';
        });
    </script>

    <script src="{{ asset('js/helpdesk-incoming.js') }}?v={{ time() }}"></script>
@endsection
