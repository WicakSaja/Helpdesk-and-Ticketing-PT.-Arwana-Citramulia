@extends('layouts.technician') {{-- Menggunakan Layout Technician --}}
@section('title', 'Detail Tiket (Teknisi)')

@section('css')
    {{-- Menggunakan style yang sama agar konsisten --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css">
    <style>
        /* --- Copy Style dari Helpdesk agar tampilan seragam --- */
        .card {
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            background: #fff;
            margin-bottom: 24px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f0f0f0;
            padding: 20px 24px;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1a202c;
            margin: 0;
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 30px;
            margin-top: 25px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 6px;
            top: 5px;
            bottom: 0;
            width: 2px;
            background: #e5e7eb;
            border-radius: 2px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 24px;
        }

        .timeline-marker {
            position: absolute;
            left: -29px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #206bc4;
            z-index: 2;
        }

        .timeline-content {
            background: #f9fafb;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .timeline-time {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 600;
            display: block;
            margin-bottom: 4px;
        }

        .timeline-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 6px;
            font-size: 0.95rem;
        }

        .timeline-desc {
            font-size: 0.9rem;
            color: #4b5563;
        }

        /* Info Sidebar */
        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 0.95rem;
            color: #111827;
            font-weight: 500;
        }

        .info-group {
            margin-bottom: 24px;
        }
    </style>
@endsection

@section('content')
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="mb-1">
                    <ol class="breadcrumb" aria-label="breadcrumbs">
                        {{-- Sesuaikan Route Dashboard Teknisi --}}
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Tugas Saya</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </div>
                <h2 class="page-title">
                    Tiket #<span id="ticket-id-display">{{ $ticket_id }}</span>
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                {{-- Tombol Aksi Khusus Teknisi (Contoh: Update Progress) --}}
                <button class="btn btn-primary d-none d-sm-inline-block"
                    onclick="alert('Fitur Update Status (Modal) dipasang disini')">
                    <i class="fe fe-edit me-2"></i> Update Status
                </button>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="fe fe-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Layout Grid sama dengan Helpdesk --}}
    <div class="row row-cards">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Permasalahan</h3>
                    <div id="ticket-status-badge">
                        <div class="spinner-border spinner-border-sm text-secondary"></div>
                    </div>
                </div>
                <div class="card-body">
                    <h2 class="mb-3" id="ticket-subject">Memuat...</h2>
                    <div class="text-muted mb-4 p-3 bg-light rounded border" id="ticket-description">
                        Memuat deskripsi...
                    </div>
                    <div class="hr-text text-muted small mt-5 mb-3">Riwayat Pengerjaan</div>
                    <div id="ticket-timeline"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Info Tiket</h3>
                </div>
                <div class="card-body">
                    <div class="info-group">
                        <div class="info-label">Pelapor</div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="avatar avatar-sm rounded bg-azure-lt fw-bold" id="req-initial">?</span>
                            <div class="info-value" id="ticket-requester">Memuat...</div>
                        </div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Kategori</div>
                        <div class="info-value" id="ticket-category">-</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Prioritas</div>
                        <div class="info-value" id="ticket-priority">-</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Tanggal Masuk</div>
                        <div class="info-value" id="ticket-created-at">-</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Script JS sama persis dengan Helpdesk, karena fungsinya hanya fetch data --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const TICKET_ID = '{{ $ticket_id }}';
        const API_URL_BASE = "http://127.0.0.1:8000/api/tickets";

        document.addEventListener('DOMContentLoaded', function() {
            loadTicketDetail();
        });

        async function loadTicketDetail() {
            // ... (Copy logika JS fetch, renderTicket, renderTimeline dari file Helpdesk di sini) ...
            // Agar file tidak terlalu panjang, logika JS-nya SAMA PERSIS dengan yang kamu kirim.
            // Pastikan Token Auth Technician valid.

            // Note: Gunakan kode JS yang ada di file Helpdesk tadi.
            // Copy mulai dari baris `const token = ...` sampai akhir script.

            /* SAYA TULIS ULANG BAGIAN INTI SAJA UNTUK REFERENSI: */
            const token = sessionStorage.getItem('auth_token') || localStorage.getItem('auth_token');
            if (!token) window.location.href = "{{ route('login') }}";

            try {
                const res = await fetch(`${API_URL_BASE}/${TICKET_ID}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) throw new Error("Gagal memuat data");
                const json = await res.json();
                renderTicket(json.data || json.ticket || json);
            } catch (e) {
                Swal.fire('Error', e.message, 'error');
            }
        }

        // Fungsi Render (Sama dengan Helpdesk)
        function renderTicket(t) {
            document.getElementById('ticket-id-display').innerText = t.id;
            document.getElementById('ticket-subject').innerText = t.title || t.subject;
            document.getElementById('ticket-description').innerHTML = (t.description || '').replace(/\n/g, '<br>');
            document.getElementById('ticket-requester').innerText = t.requester?.name || 'User';
            document.getElementById('req-initial').innerText = (t.requester?.name || 'U').charAt(0);
            document.getElementById('ticket-category').innerText = t.category?.name || '-';
            document.getElementById('ticket-priority').innerText = t.priority?.name || 'Normal';
            document.getElementById('ticket-created-at').innerText = new Date(t.created_at).toLocaleDateString('id-ID');

            // Badge Status
            const status = t.status?.name || t.status || 'OPEN';
            document.getElementById('ticket-status-badge').innerHTML =
                `<span class="badge bg-primary text-white">${status}</span>`;

            renderTimeline(t.logs || t.histories || []);
        }

        function renderTimeline(logs) {
            // (Copy fungsi renderTimeline dari helpdesk tadi)
            const container = document.getElementById('ticket-timeline');
            if (!logs.length) {
                container.innerHTML = '<p class="text-muted">Belum ada aktivitas.</p>';
                return;
            }

            let html = '<div class="timeline">';
            logs.forEach(log => {
                html += `
                <div class="timeline-item">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <span class="timeline-time">${new Date(log.created_at).toLocaleString('id-ID')}</span>
                        <div class="timeline-title">${log.action || log.status}</div>
                        <div class="timeline-desc">${log.description || log.note || ''}</div>
                    </div>
                </div>`;
            });
            html += '</div>';
            container.innerHTML = html;
        }
    </script>
@endsection
