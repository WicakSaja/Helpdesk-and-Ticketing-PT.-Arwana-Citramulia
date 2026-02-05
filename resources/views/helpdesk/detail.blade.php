@extends('layouts.helpdesk')
@section('title', 'Detail Tiket')

@section('css')
    {{-- Library CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* --- STYLE DASAR --- */
        .card {
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03); /* Shadow lebih halus */
            border-radius: 16px;
            background: #fff;
            margin-bottom: 24px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f0f0f0;
            padding: 20px 24px;
            border-radius: 16px 16px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1a202c;
            margin: 0;
        }

        /* --- TOMBOL KEMBALI MODERN (ARWANA STYLE) --- */
        .btn-back-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            
            background-color: #ffffff;
            color: #64748b;
            border: 1px solid #e2e8f0;
            
            padding: 10px 20px;
            border-radius: 50px; /* Pill Shape */
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .btn-back-custom:hover {
            background-color: #fff5f5; /* Merah muda sangat tipis */
            border-color: #d62828;     /* Merah Arwana */
            color: #d62828;
            transform: translateX(-3px); /* Efek geser kiri dikit */
            box-shadow: 0 4px 10px rgba(214, 40, 40, 0.1);
        }

        /* --- TIMELINE --- */
        .timeline {
            position: relative;
            padding-left: 35px;
            margin-top: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 9px;
            top: 5px;
            bottom: 0;
            width: 2px;
            background: #f1f5f9;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }

        .timeline-marker {
            position: absolute;
            left: -32px;
            top: 4px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #206bc4;
            z-index: 2;
            box-shadow: 0 0 0 4px #fff; 
        }

        .timeline-content {
            background: #ffffff;
            padding: 0 5px;
        }

        .timeline-time {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 4px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .timeline-title {
            font-weight: 700;
            color: #334155;
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .timeline-desc {
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.6;
            background: #f8fafc;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #f1f5f9;
            display: inline-block;
            min-width: 50%;
        }

        /* --- INFO SIDEBAR --- */
        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #94a3b8;
            font-weight: 700;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 1rem;
            color: #1e293b;
            font-weight: 600;
        }

        .info-group { margin-bottom: 28px; }

        /* --- STATUS BADGE VIBRANT --- */
        .badge-status-lg {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .bg-red-solid { background-color: #d62828 !important; }
        .bg-blue-solid { background-color: #0d6efd !important; }
        .bg-green-solid { background-color: #198754 !important; }
        .bg-grey-solid { background-color: #6c757d !important; }

        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            .page-header {
                margin-top: 10px;
                margin-bottom: 20px;
            }
            .page-header .row {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            .page-header .col-auto {
                width: 100%;
            }
            .btn-back-custom {
                width: 100%;
            }
            .timeline-desc {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            
            {{-- JUDUL TIKET (KIRI) --}}
            <div class="col">
                <h2 class="page-title d-flex align-items-center gap-2">
                    <span class="text-truncate">Detail Tiket</span> 
                    <span class="text-muted fw-normal" id="ticket-id-display" style="font-size: 0.8em; opacity: 0.7;">#Loading</span>
                </h2>
                {{-- Subtitle kecil jika perlu --}}
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
                    {{-- Badge Status --}}
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

                    <div class="hr-text text-muted small mt-5 mb-4 fw-bold text-uppercase" style="letter-spacing:1px; color: #94a3b8;">
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
                    <h3 class="card-title text-secondary" style="font-size: 0.85rem; text-transform:uppercase; letter-spacing: 0.5px;">Meta Data</h3>
                </div>
                <div class="card-body">
                    {{-- Requester --}}
                    <div class="info-group">
                        <div class="info-label">Requester (Pengaju)</div>
                        <div class="d-flex align-items-center gap-3 mt-2">
                            <span class="avatar avatar-md rounded bg-blue-lt fw-bold" id="req-initial" style="font-size: 1.2rem;">U</span>
                            <div>
                                <div class="info-value text-dark" id="ticket-requester">Memuat...</div>
                                <div class="small text-muted" id="ticket-dept">-</div>
                            </div>
                        </div>
                    </div>

                    {{-- Kategori (Full Width) --}}
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

        document.addEventListener('DOMContentLoaded', loadTicketDetail);

        async function loadTicketDetail() {
            const token = sessionStorage.getItem('auth_token') || localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = "{{ route('login') }}";
                return;
            }

            try {
                const res = await fetch(`${API_URL_BASE}/${TICKET_ID}`, {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });

                if (!res.ok) throw new Error("Gagal mengambil data tiket.");

                const json = await res.json();
                const ticket = json.data || json.ticket || json;
                renderTicket(ticket);

            } catch (error) {
                console.error(error);
                document.getElementById('ticket-description').innerHTML = `<div class="text-danger">Gagal memuat data: ${error.message}</div>`;
            }
        }

        function renderTicket(t) {
            // Header Info
            document.getElementById('ticket-id-display').innerText = `#${t.ticket_number || t.id}`;
            document.getElementById('ticket-subject').innerText = t.subject || '(Tanpa Subjek)';
            
            // Deskripsi
            const rawDesc = t.description || 'Tidak ada deskripsi.';
            const safeDesc = rawDesc.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, '<br>');
            document.getElementById('ticket-description').innerHTML = safeDesc;

            // Sidebar Info
            const reqName = t.requester?.name || t.requester_name || 'User';
            document.getElementById('ticket-requester').innerText = reqName;
            document.getElementById('req-initial').innerText = reqName.charAt(0).toUpperCase();
            document.getElementById('ticket-dept').innerText = t.requester?.department?.name || t.department?.name || '';

            document.getElementById('ticket-category').innerText = t.category?.name || '-';
            
            // Tanggal
            if (t.created_at) {
                const d = new Date(t.created_at);
                document.getElementById('ticket-created-at').innerText = d.toLocaleDateString('id-ID', {
                    day: 'numeric', month: 'long', year: 'numeric', hour:'2-digit', minute:'2-digit'
                });
            }

            // Teknisi
            const techName = t.technician?.name || t.assignment?.technician?.name;
            if (techName) {
                document.getElementById('ticket-agent').innerHTML = 
                    `<div class="d-flex align-items-center gap-2 p-3 rounded bg-blue-lt border border-blue-subtle">
                        <i class="fe fe-check-circle text-primary fs-3"></i> 
                        <span class="fw-bold text-dark fs-4">${techName}</span>
                     </div>`;
            }

            // --- STATUS BADGE (VIBRANT & HIDUP) ---
            const status = (t.status?.name || t.status || 'OPEN').toUpperCase();
            let badgeHtml = '';

            if(status === 'OPEN') {
                badgeHtml = `<span class="badge-status-lg bg-red-solid">OPEN</span>`;
            } else if(status === 'IN_PROGRESS' || status === 'ASSIGNED') {
                badgeHtml = `<span class="badge-status-lg bg-blue-solid">ON PROGRESS</span>`;
            } else if(status === 'RESOLVED') {
                badgeHtml = `<span class="badge-status-lg bg-green-solid">RESOLVED</span>`;
            } else {
                badgeHtml = `<span class="badge-status-lg bg-grey-solid">${status}</span>`;
            }
            
            document.getElementById('ticket-status-badge').innerHTML = badgeHtml;

            // Timeline
            renderTimeline(t.logs || t.histories || []);
        }

        function renderTimeline(logs) {
            const container = document.getElementById('ticket-timeline');
            if (!logs.length) {
                container.innerHTML = `<div class="text-center py-3 text-muted small">Belum ada aktivitas.</div>`;
                return;
            }

            logs.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

            let html = '<div class="timeline">';
            logs.forEach(log => {
                const date = new Date(log.created_at);
                const dateStr = date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
                const action = (log.action || log.status || 'Update').toUpperCase();
                
                // Warna Marker Timeline
                let color = '#206bc4'; // Default Blue
                if(action.includes('RESOLVED')) color = '#198754'; // Green Solid
                if(action.includes('CLOSED')) color = '#64748b'; // Grey
                if(action.includes('REJECT')) color = '#d62828'; // Red Solid
                if(action.includes('ASSIGN')) color = '#f59f00'; // Orange

                html += `
                <div class="timeline-item">
                    <div class="timeline-marker" style="border-color: ${color}; box-shadow: 0 0 0 3px #fff;"></div>
                    <div class="timeline-content">
                        <span class="timeline-time">${dateStr}</span>
                        <div class="timeline-title" style="color:${color}">${action}</div>
                        <div class="timeline-desc">${log.description || log.note || '-'}</div>
                    </div>
                </div>`;
            });
            html += '</div>';
            container.innerHTML = html;
        }
    </script>
@endsection