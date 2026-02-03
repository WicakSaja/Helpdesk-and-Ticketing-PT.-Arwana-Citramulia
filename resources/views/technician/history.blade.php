@extends('layouts.technician')
@section('title', 'Riwayat Selesai')

@section('css')
<style>
    .page-header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
    .table-container { background: white; padding: 30px; border-radius: 16px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); overflow-x: auto; }
    .history-table { width: 100%; border-collapse: collapse; }
    .history-table th { text-align: left; color: #888; font-size: 12px; padding: 15px; border-bottom: 1px solid #eee; text-transform: uppercase; }
    .history-table td { padding: 20px 15px; border-bottom: 1px solid #f9f9f9; font-size: 14px; color: #333; }
    .badge-resolved { background: #e3f2fd; color: #1565c0; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
    .badge-closed { background: #e8f5e9; color: #2e7d32; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
    .loading-spinner { text-align: center; padding: 40px; color: #888; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
    .empty-state svg { width: 64px; height: 64px; margin-bottom: 16px; opacity: 0.3; }
    .pagination { display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 20px; padding: 20px 0; }
    .pagination button { padding: 8px 16px; border: 1px solid #ddd; background: white; border-radius: 6px; cursor: pointer; font-size: 14px; }
    .pagination button:hover:not(:disabled) { background: #f5f5f5; }
    .pagination button:disabled { opacity: 0.5; cursor: not-allowed; }
    .pagination .page-info { font-size: 14px; color: #666; }
    .filter-controls { display: flex; gap: 10px; align-items: center; }
    .filter-controls select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
</style>
@endsection

@section('content')
    <div class="page-header">
        <h1 style="font-size:24px; font-weight:700; color:#333;">Riwayat Pekerjaan</h1>
        <div class="filter-controls">
            <select id="statusFilter" onchange="loadHistory()">
                <option value="">Semua Status</option>
                <option value="resolved">Resolved</option>
                <option value="closed">Closed</option>
            </select>
        </div>
    </div>

    <div class="table-container">
        <table class="history-table">
            <thead>
                <tr>
                    <th>ID Tiket</th>
                    <th>Tanggal Selesai</th>
                    <th>Judul Masalah</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="historyTableBody">
                <tr>
                    <td colspan="6" class="loading-spinner">
                        Memuat data...
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="pagination" id="paginationControls" style="display: none;">
            <button id="prevPage" onclick="changePage(-1)">← Sebelumnya</button>
            <span class="page-info" id="pageInfo"></span>
            <button id="nextPage" onclick="changePage(1)">Selanjutnya →</button>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let totalPages = 1;

        async function loadHistory() {
            const tbody = document.getElementById('historyTableBody');
            const paginationControls = document.getElementById('paginationControls');
            const statusFilter = document.getElementById('statusFilter').value;
            
            tbody.innerHTML = '<tr><td colspan="6" class="loading-spinner">Memuat data...</td></tr>';
            paginationControls.style.display = 'none';

            try {
                const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
                if (!token) {
                    throw new Error('No authentication token found');
                }

                let url = `/api/technician/completed-tickets?page=${currentPage}&per_page=15&sort_by=updated_at&sort_order=desc`;
                if (statusFilter) {
                    url += `&status=${statusFilter}`;
                }

                const response = await fetch(url, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                renderHistory(result.data);
                
                if (result.pagination) {
                    totalPages = result.pagination.last_page;
                    updatePagination(result.pagination);
                }
            } catch (error) {
                console.error('Error loading history:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="empty-state">
                            <div>❌</div>
                            <div>Gagal memuat data. Silakan refresh halaman.</div>
                        </td>
                    </tr>
                `;
            }
        }

        function renderHistory(tickets) {
            const tbody = document.getElementById('historyTableBody');
            
            if (!tickets || tickets.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="empty-state">
                            <div>📋</div>
                            <div>Belum ada riwayat pekerjaan yang selesai</div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = tickets.map(ticket => `
                <tr>
                    <td><b>${escapeHtml(ticket.ticket_number)}</b></td>
                    <td>${formatDate(ticket.updated_at)}</td>
                    <td>${escapeHtml(ticket.subject)}</td>
                    <td>${escapeHtml(ticket.category?.name || '-')}</td>
                    <td>${escapeHtml(ticket.requester?.department?.name || '-')}</td>
                    <td><span class="badge-${ticket.status.name}">${getStatusLabel(ticket.status.name)}</span></td>
                </tr>
            `).join('');
        }

        function updatePagination(pagination) {
            const paginationControls = document.getElementById('paginationControls');
            const pageInfo = document.getElementById('pageInfo');
            const prevBtn = document.getElementById('prevPage');
            const nextBtn = document.getElementById('nextPage');

            if (pagination.last_page > 1) {
                paginationControls.style.display = 'flex';
                pageInfo.textContent = `Halaman ${pagination.current_page} dari ${pagination.last_page} (Total: ${pagination.total} tiket)`;
                prevBtn.disabled = pagination.current_page === 1;
                nextBtn.disabled = pagination.current_page === pagination.last_page;
            } else {
                paginationControls.style.display = 'none';
            }
        }

        function changePage(direction) {
            const newPage = currentPage + direction;
            if (newPage >= 1 && newPage <= totalPages) {
                currentPage = newPage;
                loadHistory();
            }
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
            return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
        }

        function getStatusLabel(status) {
            const labels = {
                'resolved': 'Selesai',
                'closed': 'Ditutup'
            };
            return labels[status] || status;
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Load data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', loadHistory);
    </script>
@endsection