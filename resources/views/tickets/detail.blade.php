@extends('layouts.requester') {{-- Menggunakan Layout Requester --}}
@section('title', 'Detail Tiket Saya')

@section('css')
    {{-- Style CSS (Sama) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css">
    <style>
        /* Style card & timeline sama */
        .card {
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #f0f0f0;
            padding: 15px 20px;
        }

        /* Timeline lebih sederhana untuk user */
        .timeline {
            margin-top: 20px;
            padding-left: 20px;
            border-left: 2px solid #e5e7eb;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
            padding-left: 15px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -21px;
            top: 5px;
            width: 10px;
            height: 10px;
            background: #206bc4;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .timeline-time {
            font-size: 0.75rem;
            color: #999;
        }

        .timeline-title {
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <div class="container-xl">
        <div class="page-header d-print-none mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <ol class="breadcrumb" aria-label="breadcrumbs">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Tiket Saya</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                    <h2 class="page-title">Tiket #<span id="ticket-id-display">{{ $ticket_id }}</span></h2>
                </div>
                <div class="col-auto ms-auto">
                    <a href="javascript:history.back()" class="btn btn-ghost-secondary">Kembali</a>
                </div>
            </div>
        </div>

        <div class="row row-cards">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h2 id="ticket-subject">...</h2>
                            <div id="ticket-status-badge"></div>
                        </div>
                        <div class="p-3 bg-light border rounded" id="ticket-description">...</div>

                        <h3 class="mt-4">Riwayat Progress</h3>
                        <div id="ticket-timeline"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Informasi</h3>
                        <div class="mb-2"><strong>Kategori:</strong> <span id="ticket-category">-</span></div>
                        <div class="mb-2"><strong>Prioritas:</strong> <span id="ticket-priority">-</span></div>
                        <div class="mb-2"><strong>Dibuat:</strong> <span id="ticket-created-at">-</span></div>
                        <hr>
                        <div class="mb-2"><strong>Teknisi:</strong> <span id="ticket-agent" class="text-muted">Belum
                                ada</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Gunakan script JS Fetch yang sama persis seperti di Helpdesk --}}
    {{-- Salin block <script> dari file Helpdesk di sini --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ... PASTE LOGIC JS DARI HELPDESK ...
        // Logic fetch data sama karena endpoint API-nya sama.
        // Tampilan akan otomatis mengisi elemen dengan ID yang sesuai.

        const TICKET_ID = '{{ $ticket_id }}';
        const API_URL_BASE = "http://127.0.0.1:8000/api/tickets";

        document.addEventListener('DOMContentLoaded', function() {
            loadTicketDetail();
        });

        // Fungsi fetch loadTicketDetail() ... copy dari helpdesk
        async function loadTicketDetail() {
            // Placeholder logic
            const token = localStorage.getItem('auth_token'); // Biasanya user simpan di local
            // Fetch logic here...
            // Render logic here... (renderTicket, renderTimeline)
            // Pastikan nama ID element HTML (ticket-subject, dll) sama dengan di HTML Requester di atas.

            // UNTUK SEMENTARA SAYA PANGGIL ALERT AGAR ANDA INGAT MENGCOPY LOGICNYA
            console.log("Load script logic from helpdesk file");
        }

        // ... Copy fungsi renderTicket & renderTimeline ...
    </script>
@endsection
