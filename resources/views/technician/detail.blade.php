@extends('layouts.technician')
@section('title', 'Detail Tiket (Teknisi)')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @vite(['resources/css/ticket-detail.css'])
@endsection

@section('content')
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">

            {{-- JUDUL TIKET (KIRI) --}}
            <div class="col">
                <h2 class="page-title d-flex align-items-center gap-2">
                    <span class="text-truncate">Detail Tiket</span>
                    <span class="text-muted fw-normal" id="ticket-id-display"
                        style="font-size: 0.8em; opacity: 0.7;">#Loading</span>
                </h2>
                <div class="text-muted small mt-1">Informasi lengkap permasalahan dan riwayat penanganan.</div>
            </div>

            {{-- TOMBOL KEMBALI (KANAN) --}}
            <div class="col-auto ms-auto d-print-none">
                <a href="javascript:history.back()" class="btn-back-custom">
                    <i class="fe fe-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        {{-- KOLOM UTAMA (KIRI) --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Masalah</h3>
                    <div id="ticket-status-badge">
                        <div class="spinner-border spinner-border-sm text-secondary"></div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Subjek --}}
                    <h2 class="mb-3" id="ticket-subject" style="color:#1e293b; font-size: 1.5rem; line-height:1.4;">
                        Memuat Data...
                    </h2>

                    {{-- Deskripsi --}}
                    <div class="p-4 bg-light rounded-3 mb-5" style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
                        <div id="ticket-description" style="color: #334155; line-height: 1.7; font-size: 0.95rem;">
                            <div class="d-flex align-items-center gap-2 text-muted">
                                <div class="spinner-border spinner-border-sm"></div>
                                <span>Memuat deskripsi...</span>
                            </div>
                        </div>
                    </div>

                    <div class="hr-text text-muted small mt-5 mb-4 fw-bold text-uppercase"
                        style="letter-spacing:1px; color: #94a3b8;">
                        Riwayat Aktivitas
                    </div>

                    {{-- Timeline --}}
                    <div id="ticket-timeline">
                        <div class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm mb-2"></div>
                            <div>Memuat riwayat...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM SIDEBAR (KANAN) --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h3 class="card-title text-secondary"
                        style="font-size: 0.85rem; text-transform:uppercase; letter-spacing: 0.5px;">Informasi Tiket</h3>
                </div>
                <div class="card-body">
                    {{-- Requester --}}
                    <div class="info-group">
                        <div class="info-label">Requester (Pengaju)</div>
                        <div class="d-flex align-items-center gap-3 mt-2">
                            <span class="avatar avatar-md rounded bg-blue-lt fw-bold" id="req-initial"
                                style="font-size: 1.2rem;">U</span>
                            <div>
                                <div class="info-value text-dark" id="ticket-requester">Memuat...</div>
                                <div class="small text-muted" id="ticket-dept">-</div>
                                <div class="small text-muted" id="ticket-requester-phone">-</div>
                                <div class="small text-muted" id="ticket-requester-email">-</div>
                            </div>
                        </div>
                    </div>

                    {{-- Kategori --}}
                    <div class="info-group">
                        <div class="info-label">Kategori</div>
                        <div class="info-value" id="ticket-category">-</div>
                    </div>

                    {{-- Tanggal --}}
                    <div class="info-group">
                        <div class="info-label">Dibuat Pada</div>
                        <div class="info-value" id="ticket-created-at">-</div>
                    </div>

                    <div class="hr my-4"></div>

                    {{-- Teknisi --}}
                    <div class="info-group">
                        <div class="info-label">Teknisi (Solver)</div>
                        <div class="mt-2" id="ticket-agent">
                            <span class="text-muted fst-italic small">Belum ditugaskan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const TICKET_ID = '{{ $ticket_id }}';
        const API_URL_BASE = "{{ url('/api/tickets') }}";
        const LOGIN_URL = "{{ route('login') }}";
    </script>
    <script src="{{ asset('js/ticket-detail.js') }}?v={{ time() }}"></script>
@endsection
