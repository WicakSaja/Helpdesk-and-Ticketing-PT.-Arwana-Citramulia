@extends('layouts.requester')
@section('title', 'Validasi Tiket Resolved')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @vite(['resources/css/action-tickets.css'])
@endsection

@section('content')
    {{-- HEADER --}}
    <div class="header-wrapper">
        <div class="header-title-box">
            <h1 class="page-title">Validasi Tiket Resolved</h1>
            <p class="page-subtitle">Verifikasi pekerjaan teknisi sebelum ditutup.</p>
        </div>

        <div>
            <button class="btn-refresh" id="refreshBtn" title="Refresh Data">
                <i class="fa-solid fa-arrows-rotate"></i>
            </button>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="table-container">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th width="30%">Info Tiket</th>
                        <th width="20%">Requester</th>
                        <th width="20%">Teknisi</th>
                        <th width="15%">Waktu</th>
                        <th width="10%">Status</th>
                        <th width="5%">Aksi</th>
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

    {{-- Pagination --}}
    <div class="pagination-container" id="actionPagination" style="display: none;"></div>

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
                                <i class="fa-solid fa-xmark me-1"></i> Reject
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-success w-100 fw-bold" id="btnClose">
                                <i class="fa-solid fa-check me-1"></i> Close
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
        const LIST_API_URL = "{{ url('/api/my-tickets') }}";
        const ACTION_API_URL = "{{ url('/api/tickets') }}";
        const ACTION_PAGE_SIZE = 10;
        let actionModalInstance = null;
        let _allResolved = [];
        let _actionCurrentPage = 1;

        document.addEventListener('DOMContentLoaded', function() {
            loadResolvedTickets();

            document.getElementById('refreshBtn')?.addEventListener('click', function() {
                const icon = this.querySelector('i');
                icon.classList.add('fa-spin');
                this.disabled = true;

                loadResolvedTickets().finally(() => {
                    icon.classList.remove('fa-spin');
                    this.disabled = false;
                });
            });

            document.getElementById('btnClose')?.addEventListener('click', () => processTicket('close'));
            document.getElementById('btnReject')?.addEventListener('click', () => processTicket('unresolve'));
        });

        async function fetchWithAuth(url, options = {}) {
            const token = sessionStorage.getItem('auth_token');
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

        async function loadResolvedTickets() {
            const tbody = document.getElementById('ticketsBody');
            tbody.innerHTML =
                `<tr><td colspan="6" class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm me-2"></div> Memuat data...</td></tr>`;

            try {
                const response = await fetchWithAuth(LIST_API_URL);
                if (!response || !response.ok) throw new Error("Gagal mengambil data");

                const result = await response.json();
                const allTickets = result.data || (Array.isArray(result) ? result : []);

                _allResolved = allTickets.filter(t =>
                    (t.status?.name || t.status || '').toUpperCase() === 'RESOLVED'
                );
                _actionCurrentPage = 1;

                renderPage(_actionCurrentPage);
                renderActionPagination();

            } catch (error) {
                console.error(error);
                tbody.innerHTML =
                    `<tr><td colspan="6" class="text-center py-5 text-danger fw-bold">Gagal memuat data.</td></tr>`;
                const pag = document.getElementById('actionPagination');
                if (pag) pag.style.display = 'none';
            }
        }

        function renderPage(page) {
            _actionCurrentPage = page;
            const start = (page - 1) * ACTION_PAGE_SIZE;
            const pageItems = _allResolved.slice(start, start + ACTION_PAGE_SIZE);
            renderTable(pageItems);
        }

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
                const techName = t.technician?.name || t.assignment?.technician?.name || null;
                const dateStr = t.created_at ? new Date(t.created_at).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    hour: '2-digit',
                    minute: '2-digit'
                }) : '-';

                const ticketDataSafe = encodeURIComponent(JSON.stringify(t));

                const techHtml = techName ?
                    `<div class="tech-badge"><i class="fa-solid fa-screwdriver-wrench"></i> ${techName}</div>` :
                    `<span class="no-tech"><i class="fa-regular fa-clock"></i> Menunggu...</span>`;

                html += `
                <tr>
                    <td>
                        <div style="min-width: 0;">
                            <div class="fw-bold text-dark text-truncate" style="max-width: 250px;">${t.subject}</div>
                            <div class="small text-muted font-monospace">#${t.ticket_number}</div>
                        </div>
                    </td>
                    <td><div class="fw-bold text-dark" style="font-size: 0.9rem;">${reqName}</div></td>
                    <td>${techHtml}</td>
                    <td><div class="small text-muted">${dateStr}</div></td>
                    <td><span class="badge-status bg-green-soft">RESOLVED</span></td>
                    <td>
                        <button class="btn-action" onclick="openActionModal('${ticketDataSafe}')">
                            <i class="fe fe-check-square"></i> Validasi
                        </button>
                    </td>
                </tr>`;
            });
            tbody.innerHTML = html;
        }

        function renderActionPagination() {
            const container = document.getElementById('actionPagination');
            if (!container) return;
            const total = _allResolved.length;
            const totalPages = Math.ceil(total / ACTION_PAGE_SIZE);
            if (totalPages <= 1) {
                container.innerHTML = '';
                container.style.display = 'none';
                return;
            }

            const startIndex = (_actionCurrentPage - 1) * ACTION_PAGE_SIZE;
            const endIndex = Math.min(startIndex + ACTION_PAGE_SIZE, total);

            let html = `<div class="pagination-info">
                <span>Menampilkan <strong>${startIndex + 1}</strong> hingga <strong>${endIndex}</strong> dari <strong>${total}</strong> data</span>
            </div>`;

            html += `<div class="pagination-buttons">`;

            html += `<button type="button" class="pagination-btn" data-page="prev" ${_actionCurrentPage === 1 ? 'disabled' : ''}>
                <i class="fa-solid fa-chevron-left"></i>
            </button>`;

            const maxButtons = 5;
            let startPage = Math.max(1, _actionCurrentPage - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);
            if (endPage - startPage + 1 < maxButtons) {
                startPage = Math.max(1, endPage - maxButtons + 1);
            }

            if (startPage > 1) {
                html += `<button type="button" class="pagination-btn" data-page="1">1</button>`;
                if (startPage > 2) {
                    html += `<span class="pagination-btn" style="cursor:default; border:none; padding:0;">...</span>`;
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                html +=
                    `<button type="button" class="pagination-btn ${i === _actionCurrentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += `<span class="pagination-btn" style="cursor:default; border:none; padding:0;">...</span>`;
                }
                html += `<button type="button" class="pagination-btn" data-page="${totalPages}">${totalPages}</button>`;
            }

            html += `<button type="button" class="pagination-btn" data-page="next" ${_actionCurrentPage === totalPages ? 'disabled' : ''}>
                <i class="fa-solid fa-chevron-right"></i>
            </button>`;

            html += `</div>`;

            container.innerHTML = html;
            container.style.display = 'flex';

            container.querySelectorAll('.pagination-btn[data-page]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const p = this.getAttribute('data-page');
                    let newPage = _actionCurrentPage;
                    if (p === 'prev') newPage = Math.max(1, newPage - 1);
                    else if (p === 'next') newPage = Math.min(totalPages, newPage + 1);
                    else newPage = Number(p);

                    if (newPage !== _actionCurrentPage) {
                        renderPage(newPage);
                        renderActionPagination();
                    }
                });
            });
        }

        function openActionModal(ticketString) {
            const t = JSON.parse(decodeURIComponent(ticketString));

            document.getElementById('modalTicketId').value = t.id;
            document.getElementById('modalTicketNo').innerText = `#${t.ticket_number}`;
            document.getElementById('modalSubject').innerText = t.subject;
            document.getElementById('modalTechName').innerText = t.technician?.name || t.assignment?.technician?.name ||
                '-';
            document.getElementById('modalNote').value = '';

            const modalEl = document.getElementById('actionModal');
            if (actionModalInstance) actionModalInstance.dispose();
            actionModalInstance = new bootstrap.Modal(modalEl);
            actionModalInstance.show();
        }

        async function processTicket(action) {
            const id = document.getElementById('modalTicketId').value;
            const note = document.getElementById('modalNote').value.trim();

            if (action === 'unresolve' && !note) {
                Swal.fire('Catatan Wajib', 'Mohon tulis alasan penolakan agar teknisi tahu apa yang harus diperbaiki.',
                    'warning');
                return;
            }

            const btn = document.getElementById(action === 'close' ? 'btnClose' : 'btnReject');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            btn.disabled = true;

            try {
                const url = `${ACTION_API_URL}/${id}/${action}`;
                const res = await fetchWithAuth(url, {
                    method: 'POST',
                    body: JSON.stringify({
                        note: note
                    })
                });

                if (res.ok) {
                    actionModalInstance.hide();
                    Swal.fire({
                        icon: 'success',
                        title: action === 'close' ? 'Tiket Ditutup' : 'Tiket Ditolak',
                        text: action === 'close' ? 'Tiket berhasil divalidasi dan ditutup.' :
                            'Tiket dikembalikan ke status Open.',
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
