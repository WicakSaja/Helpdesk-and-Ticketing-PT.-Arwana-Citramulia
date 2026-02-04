@extends('layouts.helpdesk')
@section('title', 'Validasi Tiket Resolved')

@section('css')
    {{-- Library CSS Utama --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css">
    {{-- SweetAlert2 untuk notifikasi cantik --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* Modern Table & Card Styling */
        .card {
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 12px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f0f0f0;
            padding: 20px 24px;
            border-radius: 12px 12px 0 0;
        }

        .table thead th {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #6c757d;
            background-color: #f8f9fa;
            border-bottom: 2px solid #edf2f9;
            padding: 12px 16px;
        }

        .table tbody td {
            padding: 16px;
            vertical-align: middle;
            color: #344767;
            border-bottom: 1px solid #f0f0f0;
        }

        .table-hover tbody tr:hover {
            background-color: #fafbfc;
        }

        /* Avatar & Text Styling */
        .avatar-initial {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #206bc4 0%, #4299e1 100%);
            color: #fff;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 2px 5px rgba(32, 107, 196, 0.3);
        }

        .text-main {
            font-weight: 600;
            color: #1f2937;
            display: block;
        }

        .text-sub {
            font-size: 0.8rem;
            color: #6b7280;
            display: block;
            margin-top: 2px;
        }

        /* Badge Styling */
        .badge-custom {
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .bg-resolved-soft {
            background-color: #e6fffa;
            color: #23a094;
            border: 1px solid #b2f5ea;
        }

        /* Button Action */
        .btn-action {
            background-color: #fff;
            border: 1px solid #d2d6dc;
            color: #4b5563;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-action:hover {
            background-color: #f3f4f6;
            border-color: #9ca3af;
            color: #111827;
        }

        /* Modal Styling Customization */
        .modal-custom-header {
            background: #f9fafb;
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            border-radius: 12px 12px 0 0;
        }

        .modal-custom-body {
            padding: 24px;
        }

        .info-group {
            margin-bottom: 16px;
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 0.95rem;
            color: #111827;
            font-weight: 500;
        }

        .note-textarea {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 12px;
            font-size: 0.9rem;
            resize: none;
            transition: border-color 0.2s;
        }

        .note-textarea:focus {
            outline: none;
            border-color: #206bc4;
            ring: 2px solid rgba(32, 107, 196, 0.2);
        }
    </style>
@endsection

@section('content')
    <div class="page-header d-print-none mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title" style="font-weight: 700; color: #1a202c;">
                    Validasi Tiket Resolved
                </h2>
                <div class="text-muted mt-1">Daftar tiket yang telah diselesaikan teknisi dan menunggu persetujuan Helpdesk.
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                    <button class="btn btn-outline-primary" id="refreshBtn">
                        <i class="fe fe-refresh-cw me-2"></i> Refresh Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        {{-- Table Container --}}
        <div class="table-responsive">
            <table class="table table-hover table-vcenter card-table">
                <thead>
                    <tr>
                        <th style="width: 25%">Tiket Info</th>
                        <th>Requester</th>
                        <th>Teknisi (Solver)</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody id="ticketsBody">
                    {{-- Data dimuat via JS --}}
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="spinner-border text-primary me-2" role="status"></div>
                            <span class="text-muted">Memuat data tiket resolved...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination Placeholder --}}
        <div class="card-footer d-flex align-items-center justify-content-between" id="paginationContainer">
            {{-- JS will render pagination here --}}
        </div>
    </div>

    {{-- MODAL ACTION (REJECT / CLOSE) --}}
    <div class="modal modal-blur fade" id="actionModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-custom-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold">Keputusan Validasi Tiket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-custom-body">
                    {{-- Hidden ID --}}
                    <input type="hidden" id="modalTicketId">

                    {{-- Ticket Summary --}}
                    <div class="row mb-4">
                        <div class="col-12 mb-3">
                            <div class="info-label">Subjek Tiket</div>
                            <div class="info-value" id="modalSubject">-</div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">No. Tiket</div>
                            <div class="info-value" id="modalTicketNo">-</div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">Diselesaikan Oleh</div>
                            <div class="info-value" id="modalTechName">-</div>
                        </div>
                    </div>

                    {{-- Input Alasan (Wajib jika Reject) --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Catatan Validasi / Alasan Reject</label>
                        <textarea class="note-textarea" id="modalNote" rows="3"
                            placeholder="Tulis catatan untuk teknisi atau user... (Wajib diisi jika Reject)"></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-danger w-100" id="btnReject">
                            <i class="fe fe-x-circle me-2"></i> Tolak (Reject)
                        </button>
                        <button type="button" class="btn btn-success w-100" id="btnClose">
                            <i class="fe fe-check-circle me-2"></i> Tutup (Close)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- 1. LOAD LIBRARY DENGAN CDN YANG STABIL --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // === KONFIGURASI ===
        // Gunakan URL dinamis dari Laravel agar tidak error saat beda port/domain
        const BASE_API_URL = "{{ url('/api/tickets') }}";

        // Variabel Global
        let actionModalInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Script Validasi Loaded - Versi Baru'); // Cek console untuk ini
            loadResolvedTickets();

            // Event Listeners
            const btnRefresh = document.getElementById('refreshBtn');
            if (btnRefresh) btnRefresh.addEventListener('click', () => loadResolvedTickets());

            const btnClose = document.getElementById('btnClose');
            if (btnClose) btnClose.addEventListener('click', () => processTicket('close'));

            const btnReject = document.getElementById('btnReject');
            if (btnReject) btnReject.addEventListener('click', () => processTicket('reject'));
        });

        // === HELPER: Fetch dengan Auth (Mirip all-tickets.js) ===
        async function fetchWithAuth(url, options = {}) {
            const token = sessionStorage.getItem('auth_token') || localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = "{{ route('login') }}";
                return null;
            }

            const headers = {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                ...options.headers
            };

            return fetch(url, {
                ...options,
                headers
            });
        }

        // === FUNGSI LOAD DATA ===
        async function loadResolvedTickets() {
            const tbody = document.getElementById('ticketsBody');

            // Loading State
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="spinner-border text-primary text-sm me-2"></div> Memuat data...
                    </td>
                </tr>`;

            try {
                console.log("Fetching ke:", BASE_API_URL); // Debugging URL

                const response = await fetchWithAuth(BASE_API_URL);
                if (!response) return; // Redirected

                if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);

                const result = await response.json();
                const allTickets = result.data || (Array.isArray(result) ? result : []);

                // FILTER: Hanya ambil yang statusnya RESOLVED
                const resolvedTickets = allTickets.filter(t => {
                    const statusName = t.status?.name || t.status || '';
                    return statusName.toUpperCase() === 'RESOLVED';
                });

                renderTable(resolvedTickets);

            } catch (error) {
                console.error("Error Load Data:", error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-5 text-danger">
                            <i class="fe fe-alert-triangle me-2"></i> Gagal mengambil data.<br>
                            <small>${error.message}</small>
                        </td>
                    </tr>`;
            }
        }

        // === RENDER TABEL ===
        function renderTable(tickets) {
            const tbody = document.getElementById('ticketsBody');

            if (tickets.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <div class="mb-2">✓</div>
                            Tidak ada tiket resolved saat ini.
                        </td>
                    </tr>`;
                return;
            }

            let html = '';
            tickets.forEach(t => {
                const requesterName = t.requester?.name || t.requester || 'User';
                const technicianName = t.technician?.name || t.assignment?.technician?.name || 'Unassigned';
                let dateStr = '-';
                if (t.created_at) {
                    dateStr = new Date(t.created_at).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
                const initial = requesterName.charAt(0).toUpperCase();

                // Encode data untuk tombol
                const ticketDataSafe = encodeURIComponent(JSON.stringify(t));

                html += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-initial me-3" style="width:36px;height:36px;background:#eee;border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:bold;color:#555;">${initial}</div>
                            <div>
                                <span class="d-block fw-bold text-dark">${t.subject}</span>
                                <span class="d-block text-muted small">#${t.ticket_number} • ${t.category?.name || 'Umum'}</span>
                            </div>
                        </div>
                    </td>
                    <td><span class="small fw-bold">${requesterName}</span></td>
                    <td><span class="text-muted small">${technicianName}</span></td>
                    <td class="text-muted small">${dateStr}</td>
                    <td><span class="badge bg-success-subtle text-success border border-success-subtle">RESOLVED</span></td>
                    <td class="text-end">
                        <button class="btn btn-white btn-sm border" onclick="openActionModal('${ticketDataSafe}')">
                            Tindak Lanjuti
                        </button>
                    </td>
                </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        // === MODAL LOGIC (FIXED) ===
        function openActionModal(ticketString) {
            // Pastikan Bootstrap sudah siap
            if (typeof bootstrap === 'undefined') {
                alert("Library Bootstrap belum dimuat. Coba refresh halaman.");
                return;
            }

            const t = JSON.parse(decodeURIComponent(ticketString));

            // Set Value
            document.getElementById('modalTicketId').value = t.id;
            document.getElementById('modalSubject').innerText = t.subject;
            document.getElementById('modalTicketNo').innerText = `#${t.ticket_number}`;
            document.getElementById('modalTechName').innerText = t.technician?.name || t.assignment?.technician?.name ||
                '-';
            document.getElementById('modalNote').value = '';

            // Buka Modal dengan aman
            const modalEl = document.getElementById('actionModal');
            if (actionModalInstance) {
                actionModalInstance.dispose(); // Bersihkan instance lama jika ada
            }
            actionModalInstance = new bootstrap.Modal(modalEl);
            actionModalInstance.show();
        }

        async function processTicket(action) {
            const id = document.getElementById('modalTicketId').value;
            const note = document.getElementById('modalNote').value.trim();

            if (action === 'reject' && !note) {
                Swal.fire('Wajib Diisi', 'Alasan reject harus diisi!', 'warning');
                return;
            }

            const result = await Swal.fire({
                title: 'Konfirmasi',
                text: action === 'close' ? 'Yakin tutup tiket ini?' : 'Yakin tolak tiket ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Proses'
            });

            if (!result.isConfirmed) return;

            // Loading UI
            const btnId = action === 'close' ? 'btnClose' : 'btnReject';
            const btnEl = document.getElementById(btnId);
            const oriHtml = btnEl.innerHTML;
            btnEl.innerHTML = 'Wait...';
            btnEl.disabled = true;

            try {
                // Endpoint API Action
                const url = `${BASE_API_URL}/${id}/${action}`;

                const res = await fetchWithAuth(url, {
                    method: 'POST',
                    body: JSON.stringify({
                        note: note
                    })
                });

                if (res.ok) {
                    if (actionModalInstance) actionModalInstance.hide();
                    Swal.fire('Sukses', 'Data berhasil diproses', 'success');
                    loadResolvedTickets();
                } else {
                    const data = await res.json();
                    throw new Error(data.message || 'Gagal memproses');
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Error', err.message, 'error');
            } finally {
                btnEl.innerHTML = oriHtml;
                btnEl.disabled = false;
            }
        }
    </script>
@endsection
