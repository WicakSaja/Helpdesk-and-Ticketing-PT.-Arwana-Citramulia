@extends('layouts.superadmin') {{-- Layout Superadmin --}}
@section('title', 'Admin - Detail Tiket')

{{-- Isinya hampir Identik dengan HELPDESK, karena Superadmin butuh view lengkap --}}
@section('css')
    {{-- ... Copy semua CSS dari file Helpdesk yang kamu kirim ... --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css">
    <style>
        /* Gunakan style CSS yang sama persis dengan Helpdesk */
        .card {
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        /* ... dst ... */
        .timeline-marker {
            border-color: #d63939;
            /* Beda warna dikit buat admin (Merah) */
        }
    </style>
@endsection

@section('content')
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Admin Panel</a></li>
                    <li class="breadcrumb-item"><a href="#">Semua Tiket</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
                <h2 class="page-title">
                    <span class="badge bg-red text-white me-2">ADMIN</span>
                    Tiket #<span id="ticket-id-display">{{ $ticket_id }}</span>
                </h2>
            </div>
            <div class="col-auto ms-auto">
                <button class="btn btn-danger" onclick="deleteTicket()">
                    <i class="fe fe-trash me-2"></i> Hapus Tiket
                </button>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
    </div>

    {{-- Copy Paste isi Content body dari Helpdesk --}}
    <div class="row row-cards">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Masalah</h3>
                    <div id="ticket-status-badge"></div>
                </div>
                <div class="card-body">
                    <h2 class="mb-3" id="ticket-subject">Loading...</h2>
                    <div class="text-muted mb-4 p-3 bg-light rounded border" id="ticket-description">Loading...</div>
                    <div class="hr-text text-muted small mt-5 mb-3">Audit Log (History)</div>
                    <div id="ticket-timeline"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            {{-- Sidebar Info (Sama dengan Helpdesk) --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Meta Data</h3>
                </div>
                <div class="card-body">
                    <div class="info-group">
                        <div class="info-label">Requester</div>
                        <div class="info-value" id="ticket-requester">Memuat...</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Teknisi</div>
                        <div class="info-value text-primary" id="ticket-agent">Memuat...</div>
                    </div>
                    {{-- Tambahan Info System untuk Admin --}}
                    <div class="info-group">
                        <div class="info-label">ID Database</div>
                        <div class="info-value code text-muted">{{ $ticket_id }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // KONFIGURASI SAMA
        const TICKET_ID = '{{ $ticket_id }}';
        const API_URL_BASE = "http://127.0.0.1:8000/api/tickets";

        // ... Copy Logic Fetch & Render dari Helpdesk ...

        document.addEventListener('DOMContentLoaded', function() {
            loadTicketDetail();
        });

        async function loadTicketDetail() {
            // (Paste kode fetch Helpdesk disini)
            // ...
        }

        function renderTicket(t) {
            // (Paste kode renderTicket Helpdesk disini)
        }

        function renderTimeline(logs) {
            // (Paste kode renderTimeline Helpdesk disini)
        }

        // Tambahan Fungsi Hapus untuk Admin
        function deleteTicket() {
            Swal.fire({
                title: 'Hapus Tiket?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Logic delete fetch API
                    Swal.fire('Terhapus!', 'Tiket telah dihapus.', 'success');
                }
            })
        }
    </script>
@endsection
