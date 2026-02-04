@extends('layouts.helpdesk')
@section('title', 'Detail Tiket')

@section('css')
    {{-- Library CSS (Tabler & Icons) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css">

    <style>
        /* --- Modern Card Styling --- */
        .card {
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 12px;
            background: #fff;
            margin-bottom: 24px;
            transition: transform 0.2s;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f0f0f0;
            padding: 20px 24px;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1a202c;
            margin: 0;
        }

        /* --- Timeline Styling (Riwayat) --- */
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
            margin-bottom: 4px;
            display: block;
            font-weight: 600;
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

        /* --- Info Sidebar --- */
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

        /* --- Badge Custom --- */
        .badge-status {
            font-size: 0.8rem;
            padding: 6px 10px;
            border-radius: 6px;
        }
    </style>
@endsection

@section('content')
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="mb-1">
                    <ol class="breadcrumb" aria-label="breadcrumbs">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.helpdesk') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('helpdesk.all') }}">Tiket</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </div>
                <h2 class="page-title">
                    Detail Tiket #<span id="ticket-id-display">{{ $ticket_id }}</span>
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                {{-- Tombol Kembali --}}
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="fe fe-arrow-left me-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        {{-- KOLOM KIRI: DETAIL & HISTORY --}}
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
                    <h2 class="mb-3" id="ticket-subject" style="color:#1f2937;">Memuat Subjek...</h2>

                    {{-- Deskripsi --}}
                    <div class="text-muted mb-4 p-3 bg-light rounded border" id="ticket-description"
                        style="line-height: 1.6; min-height: 80px;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                            <span>Memuat deskripsi tiket...</span>
                        </div>
                    </div>

                    <div class="hr-text text-muted small mt-5 mb-3">Riwayat Aktivitas</div>

                    {{-- Timeline Container --}}
                    <div id="ticket-timeline">
                        <div class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm mb-2"></div>
                            <div>Memuat riwayat...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: SIDEBAR INFO --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Meta Data</h3>
                </div>
                <div class="card-body">
                    <div class="info-group">
                        <div class="info-label">Requester (Pengaju)</div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="avatar avatar-sm rounded bg-blue-lt fw-bold" id="req-initial">U</span>
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
                        <div class="info-label">Dibuat Pada</div>
                        <div class="info-value" id="ticket-created-at">-</div>
                    </div>

                    <div class="hr my-3"></div>

                    <div class="info-group">
                        <div class="info-label">Teknisi (Solver)</div>
                        <div class="info-value text-primary mt-1" id="ticket-agent">
                            <span class="text-muted fst-italic">Belum ditugaskan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Library Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // === KONFIGURASI ===
        const TICKET_ID = '{{ $ticket_id }}';
        // URL API dasar (Tanpa /helpdesk, sesuai perbaikan kita sebelumnya)
        const API_URL_BASE = "http://127.0.0.1:8000/api/tickets";

        document.addEventListener('DOMContentLoaded', function() {
            loadTicketDetail();
        });

        // === 1. FETCH DATA ===
        async function loadTicketDetail() {
            const token = sessionStorage.getItem('auth_token') || localStorage.getItem('auth_token');

            if (!token) {
                alert("Sesi habis, silakan login kembali.");
                window.location.href = "{{ route('login') }}";
                return;
            }

            try {
                const url = `${API_URL_BASE}/${TICKET_ID}`;
                console.log("Fetching Detail:", url);

                const res = await fetch(url, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!res.ok) {
                    if (res.status === 404) throw new Error("Tiket tidak ditemukan.");
                    if (res.status === 401) {
                        window.location.href = "{{ route('login') }}";
                        return;
                    }
                    throw new Error("Gagal mengambil data dari server.");
                }

                const json = await res.json();
                // Handle struktur response: {data: {...}} atau langsung {...}
                const ticket = json.data || json.ticket || json;

                renderTicket(ticket);

            } catch (error) {
                console.error(error);
                document.getElementById('ticket-description').innerHTML =
                    `<div class="alert alert-danger">Error: ${error.message}</div>`;

                Swal.fire('Gagal', error.message, 'error');
            }
        }

        // === 2. RENDER UI ===
        function renderTicket(t) {
            // -- Header & Deskripsi --
            document.getElementById('ticket-id-display').innerText = t.ticket_number || t.id;
            document.getElementById('ticket-subject').innerText = t.subject || t.title || '(Tanpa Subjek)';

            // Deskripsi (Convert newline ke <br>)
            const desc = t.description || 'Tidak ada deskripsi.';
            document.getElementById('ticket-description').innerHTML = desc.replace(/\n/g, '<br>');

            // -- Sidebar Info --
            const reqName = t.requester?.name || t.requester_name || 'User';
            document.getElementById('ticket-requester').innerText = reqName;
            document.getElementById('req-initial').innerText = reqName.charAt(0).toUpperCase();

            document.getElementById('ticket-category').innerText = t.category?.name || '-';

            // Styling Prioritas
            const priority = t.priority?.name || t.priority || 'Normal';
            const pEl = document.getElementById('ticket-priority');
            pEl.innerText = priority;
            if (priority.toUpperCase() === 'HIGH') pEl.className = 'info-value text-danger fw-bold';
            else pEl.className = 'info-value';

            // Tanggal
            if (t.created_at) {
                const date = new Date(t.created_at);
                document.getElementById('ticket-created-at').innerText = date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            // Teknisi
            const techName = t.technician?.name || t.assignment?.technician?.name || null;
            if (techName) {
                document.getElementById('ticket-agent').innerHTML =
                    `<div class="d-flex align-items-center gap-2">
                        <i class="fe fe-tool text-muted"></i> 
                        <span class="fw-bold text-dark">${techName}</span>
                     </div>`;
            }

            // -- Badge Status --
            const status = t.status?.name || t.status || 'OPEN';
            let badgeClass = 'bg-secondary text-white';
            const s = status.toUpperCase();

            if (s === 'OPEN') badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
            if (s === 'IN_PROGRESS' || s === 'ASSIGNED') badgeClass =
                'bg-primary-subtle text-primary border border-primary-subtle';
            if (s === 'RESOLVED') badgeClass = 'bg-success-subtle text-success border border-success-subtle';
            if (s === 'CLOSED') badgeClass = 'bg-dark-subtle text-dark border border-dark-subtle';

            document.getElementById('ticket-status-badge').innerHTML =
                `<span class="badge ${badgeClass} px-3 py-2 fs-5">${status}</span>`;

            // -- Render Timeline --
            const logs = t.logs || t.histories || t.activity_logs || [];
            renderTimeline(logs);
        }

        // === 3. RENDER TIMELINE ===
        function renderTimeline(logs) {
            const container = document.getElementById('ticket-timeline');

            if (!logs || logs.length === 0) {
                container.innerHTML =
                    `<div class="empty text-center py-4">
                        <div class="empty-img"><i class="fe fe-activity text-muted fs-2"></i></div>
                        <p class="empty-title text-muted mt-2">Belum ada riwayat aktivitas.</p>
                    </div>`;
                return;
            }

            // Sort: Terbaru di atas
            logs.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

            let html = '<div class="timeline">';

            logs.forEach(log => {
                const date = new Date(log.created_at);
                const dateStr = date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const action = log.action || log.status || 'Update';
                const note = log.description || log.note || log.message || '';

                // Warna Marker
                let markerColor = '#206bc4'; // Default Blue
                if (action.toUpperCase().includes('RESOLVED')) markerColor = '#2fb344'; // Green
                if (action.toUpperCase().includes('REJECT')) markerColor = '#d63939'; // Red
                if (action.toUpperCase().includes('CLOSED')) markerColor = '#1f2937'; // Dark

                html += `
                    <div class="timeline-item">
                        <div class="timeline-marker" style="border-color: ${markerColor}"></div>
                        <div class="timeline-content">
                            <span class="timeline-time">${dateStr}</span>
                            <div class="timeline-title" style="color:${markerColor}">${action}</div>
                            ${note ? `<div class="timeline-desc">${note}</div>` : ''}
                        </div>
                    </div>`;
            });

            html += '</div>';
            container.innerHTML = html;
        }
    </script>
@endsection
