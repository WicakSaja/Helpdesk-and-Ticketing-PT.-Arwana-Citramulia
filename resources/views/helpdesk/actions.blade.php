@extends('layouts.helpdesk')
@section('title', 'Validasi Tiket Resolved')

@section('css')
    {{-- Library CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    {{-- Kita gunakan FontAwesome juga biar ikon refreshnya sama kayak Incoming --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* --- 1. GLOBAL SETTINGS --- */
        body { background-color: #f4f6f9; font-family: 'Inter', sans-serif; }

        /* --- 2. HEADER FIX (PAKSA KIRI KANAN) --- */
        .header-wrapper {
            display: flex;
            justify-content: space-between; /* Judul Kiri, Tombol Kanan */
            align-items: center;
            margin-bottom: 25px;
            width: 100%;
        }

        .header-title-box {
            text-align: left !important; /* Paksa rata kiri */
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin: 0 0 5px 0;
            line-height: 1.2;
        }

        .page-subtitle {
            color: #666;
            font-size: 14px;
            margin: 0;
        }

        /* --- 3. TOMBOL REFRESH (STYLE INCOMING) --- */
        .btn-refresh {
            width: 42px;
            height: 42px;
            border-radius: 10px; /* Sedikit lebih rounded */
            border: 1px solid #ddd;
            background: white;
            color: #555;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .btn-refresh i {
            font-size: 18px; /* Ukuran ikon pas */
        }

        .btn-refresh:hover {
            background: #f8f9fa;
            color: #333;
            border-color: #bbb;
            transform: translateY(-2px); /* Efek naik dikit */
        }

        /* --- 4. TABLE STYLING --- */
        .table-card {
            background: white;
            padding: 0;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
        }

        .custom-table th {
            text-align: left;
            color: #64748b;
            padding: 16px 24px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #f8fafc;
        }

        .custom-table td {
            padding: 16px 24px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            color: #334155;
            vertical-align: middle;
        }

        .custom-table tr:hover td {
            background-color: #f8fafc;
        }

        /* Avatar */
        .avatar-initial {
            width: 38px; height: 38px; border-radius: 10px;
            background: #e0f2fe; color: #0284c7;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; flex-shrink: 0;
        }

        /* Badge Status */
        .badge-status {
            padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px; display: inline-block;
        }
        .bg-green-soft { background: #dcfce7; color: #16a34a; }

        /* Tombol Aksi */
        .btn-action {
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #475569;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-action:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #1e293b;
        }

        /* --- 5. MODAL --- */
        .modal-box {
            border-radius: 16px; overflow: hidden; border: none;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .modal-header { background: #f8fafc; padding: 20px 25px; border-bottom: 1px solid #f1f5f9; }
        .modal-title { font-weight: 700; color: #334155; margin: 0; font-size: 1.1rem; }
        .modal-body { padding: 25px; }
        
        .ticket-summary-box {
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px; margin-bottom: 20px;
        }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; }
        .summary-label { color: #64748b; }
        .summary-val { color: #1e293b; font-weight: 600; text-align: right; }

        .form-control:focus { border-color: #206bc4; box-shadow: 0 0 0 2px rgba(32,107,196,0.1); }

        /* --- RESPONSIVE --- */
        @media (max-width: 576px) {
            .header-wrapper {
                flex-direction: row; /* Tetap sebaris di HP */
                align-items: center;
                margin-bottom: 20px;
            }
            
            .header-title-box {
                flex: 1; /* Ambil space */
            }

            .page-title { font-size: 18px; margin-bottom: 2px; }
            .page-subtitle { font-size: 12px; }
            
            .btn-refresh {
                width: 38px; height: 38px; /* Sedikit lebih kecil di HP */
            }
            .btn-refresh i { font-size: 16px; }

            .custom-table th, .custom-table td { padding: 12px 15px; }
        }
    </style>
@endsection

@section('content')
    {{-- HEADER FIX (Menggunakan Flexbox Spesifik) --}}
    <div class="header-wrapper">
        <div class="header-title-box">
            <h1 class="page-title">Validasi Tiket Resolved</h1>
            <p class="page-subtitle">Verifikasi pekerjaan teknisi sebelum ditutup.</p>
        </div>
        
        {{-- Tombol Refresh (Pakai FontAwesome biar muncul) --}}
        <div>
            <button class="btn-refresh" id="refreshBtn" title="Refresh Data">
                <i class="fa-solid fa-arrows-rotate"></i>
            </button>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th width="30%">Info Tiket</th>
                        <th width="20%">Requester</th>
                        <th width="20%">Teknisi</th>
                        <th width="15%">Waktu</th>
                        <th width="10%">Status</th>
                        <th width="5%" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody id="ticketsBody">
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="spinner-border text-primary text-sm mb-2" role="status"></div>
                            <div class="text-muted small">Memuat antrian validasi...</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL ACTION --}}
    <div class="modal modal-blur fade" id="actionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-box">
                <div class="modal-header">
                    <h5 class="modal-title">Validasi Penyelesaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="modalTicketId">

                    {{-- Summary Tiket --}}
                    <div class="ticket-summary-box">
                        <div class="summary-row">
                            <span class="summary-label">No. Tiket</span>
                            <span class="summary-val text-primary" id="modalTicketNo">-</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Subjek</span>
                            <span class="summary-val text-truncate" style="max-width: 200px;" id="modalSubject">-</span>
                        </div>
                        <div class="summary-row mb-0">
                            <span class="summary-label">Teknisi</span>
                            <span class="summary-val" id="modalTechName">-</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted mb-2">Catatan Validasi</label>
                        <textarea class="form-control" id="modalNote" rows="3" 
                            placeholder="Berikan alasan jika menolak, atau catatan tambahan jika setuju..."></textarea>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <button type="button" class="btn btn-outline-danger w-100 fw-bold" id="btnReject">
                                <i class="fe fe-x me-1"></i> Tolak
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-success w-100 fw-bold" id="btnClose">
                                <i class="fe fe-check me-1"></i> Setuju
                            </button>
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
        const BASE_API_URL = "{{ url('/api/tickets') }}";
        let actionModalInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadResolvedTickets();

            // Event Listeners
            document.getElementById('refreshBtn')?.addEventListener('click', function() {
                // Efek muter pas diklik
                const icon = this.querySelector('i');
                icon.classList.add('fa-spin');
                this.disabled = true;
                
                loadResolvedTickets().finally(() => {
                    icon.classList.remove('fa-spin');
                    this.disabled = false;
                });
            });

            document.getElementById('btnClose')?.addEventListener('click', () => processTicket('close'));
            document.getElementById('btnReject')?.addEventListener('click', () => processTicket('reject'));
        });

        // --- FETCH HELPER ---
        async function fetchWithAuth(url, options = {}) {
            const token = sessionStorage.getItem('auth_token') || localStorage.getItem('auth_token');
            if (!token) { window.location.href = "{{ route('login') }}"; return null; }
            
            const headers = {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                ...options.headers
            };
            return fetch(url, { ...options, headers });
        }

        // --- LOAD DATA ---
        async function loadResolvedTickets() {
            const tbody = document.getElementById('ticketsBody');
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm me-2"></div> Memuat data...</td></tr>`;

            try {
                const response = await fetchWithAuth(BASE_API_URL);
                if (!response || !response.ok) throw new Error("Gagal mengambil data");

                const result = await response.json();
                const allTickets = result.data || (Array.isArray(result) ? result : []);

                // Filter status RESOLVED
                const resolvedTickets = allTickets.filter(t => 
                    (t.status?.name || t.status || '').toUpperCase() === 'RESOLVED'
                );

                renderTable(resolvedTickets);

            } catch (error) {
                console.error(error);
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-danger fw-bold">Gagal memuat data.</td></tr>`;
            }
        }

        // --- RENDER TABLE ---
        function renderTable(tickets) {
            const tbody = document.getElementById('ticketsBody');
            
            if (tickets.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted mb-2"><i class="fe fe-check-circle" style="font-size: 32px; opacity: 0.3;"></i></div>
                            <div class="text-muted small">Tidak ada tiket yang perlu divalidasi.</div>
                        </td>
                    </tr>`;
                return;
            }

            let html = '';
            tickets.forEach(t => {
                const reqName = t.requester?.name || t.requester || 'User';
                const techName = t.technician?.name || t.assignment?.technician?.name || '-';
                const dateStr = t.created_at ? new Date(t.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', hour: '2-digit', minute:'2-digit'}) : '-';
                const initial = reqName.charAt(0).toUpperCase();
                
                const ticketDataSafe = encodeURIComponent(JSON.stringify(t));

                html += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-initial">${initial}</div>
                            <div style="min-width: 0;">
                                <div class="fw-bold text-dark text-truncate" style="max-width: 200px;">${t.subject}</div>
                                <div class="small text-muted font-monospace">#${t.ticket_number}</div>
                            </div>
                        </div>
                    </td>
                    <td><div class="fw-bold text-dark" style="font-size: 0.9rem;">${reqName}</div></td>
                    <td><div class="small text-muted"><i class="fe fe-tool me-1"></i> ${techName}</div></td>
                    <td><div class="small text-muted">${dateStr}</div></td>
                    <td><span class="badge-status bg-green-soft">RESOLVED</span></td>
                    <td class="text-end">
                        <button class="btn-action" onclick="openActionModal('${ticketDataSafe}')">
                            <i class="fe fe-check-square"></i> Validasi
                        </button>
                    </td>
                </tr>`;
            });
            tbody.innerHTML = html;
        }

        // --- MODAL & PROCESS ---
        function openActionModal(ticketString) {
            const t = JSON.parse(decodeURIComponent(ticketString));

            document.getElementById('modalTicketId').value = t.id;
            document.getElementById('modalTicketNo').innerText = `#${t.ticket_number}`;
            document.getElementById('modalSubject').innerText = t.subject;
            document.getElementById('modalTechName').innerText = t.technician?.name || t.assignment?.technician?.name || '-';
            document.getElementById('modalNote').value = '';

            const modalEl = document.getElementById('actionModal');
            if(actionModalInstance) actionModalInstance.dispose();
            actionModalInstance = new bootstrap.Modal(modalEl);
            actionModalInstance.show();
        }

        async function processTicket(action) {
            const id = document.getElementById('modalTicketId').value;
            const note = document.getElementById('modalNote').value.trim();

            if (action === 'reject' && !note) {
                Swal.fire('Catatan Wajib', 'Mohon tulis alasan penolakan agar teknisi tahu apa yang harus diperbaiki.', 'warning');
                return;
            }

            // UI Loading
            const btn = document.getElementById(action === 'close' ? 'btnClose' : 'btnReject');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            btn.disabled = true;

            try {
                const url = `${BASE_API_URL}/${id}/${action}`;
                const res = await fetchWithAuth(url, {
                    method: 'POST',
                    body: JSON.stringify({ note: note })
                });

                if (res.ok) {
                    actionModalInstance.hide();
                    Swal.fire({
                        icon: 'success',
                        title: action === 'close' ? 'Tiket Ditutup' : 'Tiket Ditolak',
                        text: action === 'close' ? 'Tiket berhasil divalidasi dan ditutup.' : 'Tiket dikembalikan ke status Open.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    loadResolvedTickets();
                } else {
                    const data = await res.json();
                    throw new Error(data.message || 'Gagal memproses tiket.');
                }
            } catch (err) {
                Swal.fire('Gagal', err.message, 'error');
            } finally {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        }
    </script>
@endsection