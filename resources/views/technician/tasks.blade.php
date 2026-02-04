@extends('layouts.technician')
@section('title', 'Tugas Saya')

@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        /* Task Card */
        .task-card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
            margin-bottom: 20px;
            border-left: 5px solid #ccc;
            transition: 0.3s;
            position: relative;
        }

        .task-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .task-id {
            font-weight: 700;
            font-size: 14px;
        }

        .task-time {
            font-size: 12px;
            color: #888;
        }

        .task-body h3 {
            font-size: 18px;
            margin-bottom: 5px;
            color: #333;
            font-weight: 700;
        }

        .task-body p {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .task-meta {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .meta-tag {
            background: #f4f6f9;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
            color: #555;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        /* Tombol Aksi */
        .action-group {
            display: flex;
            gap: 10px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        .btn-action {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: 0.3s;
        }

        .btn-detail {
            background: white;
            border: 1px solid #ddd;
            color: #555;
        }

        .btn-detail:hover {
            background: #f8f9fa;
            border-color: #ccc;
            color: #333;
        }

        .btn-update {
            background: #2e7d32;
            color: white;
        }

        .btn-update:hover {
            background: #1b5e20;
        }

        .btn-confirm {
            background: #1976d2;
            color: white;
        }

        .btn-confirm:hover {
            background: #0d47a1;
        }

        .btn-reject {
            background: #d32f2f;
            color: white;
        }

        .btn-reject:hover {
            background: #b71c1c;
        }

        /* Warna Kategori */
        .bd-mech {
            border-left-color: #d62828;
        }

        .txt-mech {
            color: #d62828;
        }

        .bd-it {
            border-left-color: #1976d2;
        }

        .txt-it {
            color: #1976d2;
        }

        /* --- MODAL STYLE --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            backdrop-filter: blur(4px);
        }

        .modal-box {
            background: white;
            width: 600px;
            max-width: 90%;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: #333;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #999;
            cursor: pointer;
        }

        .btn-close:hover {
            color: #d62828;
        }

        /* Form di Modal */
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 8px;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            margin-bottom: 15px;
        }

        .form-textarea {
            height: 100px;
            resize: vertical;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: #2e7d32;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title" id="taskTitle">Daftar Tugas</h1>
    </div>

    <div id="taskList">
        <div class="task-card">
            <div class="task-body">
                <h3>Loading...</h3>
                <p>Sedang memuat daftar tugas.</p>
            </div>
        </div>
    </div>

    <div id="modalUpdate" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Selesaikan Tiket</h3>
                <button class="btn-close" onclick="closeModal('modalUpdate')">&times;</button>
            </div>

            <form id="updateForm">
                <div
                    style="background: #e8f5e9; padding: 10px 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c8e6c9;">
                    <strong style="color: #2e7d32; font-size: 13px;">Tiket: <span id="uSubject">...</span></strong>
                </div>

                <input type="hidden" id="resolveTicketId">

                <div class="form-group">
                    <label class="form-label">Tanggal & Waktu Selesai</label>
                    <input type="datetime-local" class="form-input" id="resolvedAt" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Solusi / Tindakan Perbaikan</label>
                    <textarea class="form-textarea" id="solutionText" 
                        placeholder="Contoh: Sudah dicek kabel power ternyata tidak terpasang dengan baik. Sudah dipasang kembali dengan benar dan komputer sudah bisa menyala normal."
                        required></textarea>
                </div>

                <div style="text-align: right; margin-top: 10px;">
                    <button type="button" onclick="closeModal('modalUpdate')"
                        style="background:white; border:1px solid #ddd; padding:10px 20px; border-radius:8px; cursor:pointer; margin-right: 10px;">Batal</button>
                    <button type="submit"
                        style="background:#2e7d32; color:white; border:none; padding:10px 25px; border-radius:8px; cursor:pointer; font-weight:600;">
                        <i class="fa-solid fa-check-circle"></i> Selesaikan Tiket
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        async function loadTechnicianTasks() {
            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');

            try {
                const response = await fetch('{{ url("/api/technician/tickets") }}?page=1&per_page=15', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                const tickets = result.data || [];
                document.getElementById('taskTitle').innerText = `Daftar Tugas (${tickets.length})`;

                renderTasks(tickets);
            } catch (error) {
                document.getElementById('taskList').innerHTML = `
                    <div class="task-card">
                        <div class="task-body">
                            <h3>Gagal memuat data</h3>
                            <p>${error.message}</p>
                        </div>
                    </div>
                `;
            }
        }

        function renderTasks(tickets) {
            if (!tickets.length) {
                document.getElementById('taskList').innerHTML = `
                    <div class="task-card">
                        <div class="task-body">
                            <h3>Tidak ada tugas</h3>
                            <p>Belum ada ticket yang di-assign ke Anda.</p>
                        </div>
                    </div>
                `;
                return;
            }

            const html = tickets.map(ticket => {
                const categoryName = ticket.category?.name || 'Unknown';
                const statusName = ticket.status?.name || 'unknown';
                const requesterName = ticket.requester?.name || 'Unknown';
                const requesterDept = ticket.requester?.department?.name || 'Unknown';
                const createdAt = formatDate(ticket.created_at);
                const cardClass = getCategoryClass(categoryName);
                const textClass = getCategoryTextClass(categoryName);

                // Determine action buttons based on status
                let actionButtons = '';
                const statusLower = statusName.toLowerCase();
                
                if (statusLower === 'assigned') {
                    // Status ASSIGNED: Detail + Confirm + Reject
                    actionButtons = `
                        <a class="btn-action btn-detail" href="/tickets/${ticket.id}">
                            <i class="fa-regular fa-eye"></i> Detail
                        </a>
                        <button class="btn-action btn-confirm"
                            onclick="confirmTicket(${ticket.id}, '${escapeHtml(ticket.ticket_number)}')">
                            <i class="fa-solid fa-check"></i> Konfirmasi
                        </button>
                        <button class="btn-action btn-reject" 
                            onclick="rejectTicket(${ticket.id}, '${escapeHtml(ticket.ticket_number)}')">
                            <i class="fa-solid fa-times"></i> Tolak
                        </button>
                    `;
                } else if (statusLower === 'in progress') {
                    // Status IN PROGRESS: Detail + Resolve
                    actionButtons = `
                        <a class="btn-action btn-detail" href="/tickets/${ticket.id}">
                            <i class="fa-regular fa-eye"></i> Detail
                        </a>
                        <button class="btn-action btn-update" 
                            onclick="openResolve(${ticket.id}, '${escapeHtml(ticket.ticket_number)}', '${escapeHtml(ticket.subject)}')">
                            <i class="fa-solid fa-check-circle"></i> Selesaikan Tiket
                        </button>
                    `;
                } else {
                    // Status lainnya (resolved, closed, dll): hanya detail
                    actionButtons = `
                        <a class="btn-action btn-detail" href="/tickets/${ticket.id}">
                            <i class="fa-regular fa-eye"></i> Detail
                        </a>
                    `;
                }

                return `
                    <div class="task-card ${cardClass}">
                        <div class="task-header">
                            <span class="task-id ${textClass}">#${ticket.ticket_number}</span>
                            <span class="task-time"><i class="fa-regular fa-clock"></i> ${createdAt}</span>
                        </div>
                        <div class="task-body">
                            <h3>${ticket.subject}</h3>
                            <p>${ticket.description || '-'}</p>
                            <div class="task-meta">
                                <div class="meta-tag"><i class="fa-solid fa-building"></i> ${requesterDept}</div>
                                <div class="meta-tag"><i class="fa-solid fa-tag"></i> ${categoryName}</div>
                                <div class="meta-tag"><i class="fa-solid fa-circle-info"></i> ${statusName}</div>
                            </div>
                        </div>
                        <div class="action-group">
                            ${actionButtons}
                        </div>
                    </div>
                `;
            }).join('');

            document.getElementById('taskList').innerHTML = html;
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function getCategoryClass(category) {
            const name = (category || '').toLowerCase();
            if (name.includes('hardware') || name.includes('mechanical')) return 'bd-mech';
            if (name.includes('it') || name.includes('software')) return 'bd-it';
            return '';
        }

        function getCategoryTextClass(category) {
            const name = (category || '').toLowerCase();
            if (name.includes('hardware') || name.includes('mechanical')) return 'txt-mech';
            if (name.includes('it') || name.includes('software')) return 'txt-it';
            return '';
        }

        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function openResolve(ticketId, ticketNumber, subject) {
            document.getElementById('resolveTicketId').value = ticketId;
            document.getElementById('uSubject').innerText = '#' + ticketNumber + " - " + subject;
            
            // Set default datetime to now
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('resolvedAt').value = `${year}-${month}-${day}T${hours}:${minutes}`;
            document.getElementById('solutionText').value = '';
            
            document.getElementById('modalUpdate').style.display = 'flex';
        }

        async function confirmTicket(ticketId, ticketNumber) {
            const result = await Swal.fire({
                title: 'Konfirmasi Tiket',
                text: `Anda akan mengkonfirmasi tiket #${ticketNumber}. Lanjutkan?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1976d2',
                cancelButtonColor: '#999',
                confirmButtonText: 'Ya, Konfirmasi',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;

            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
            
            try {
                const response = await fetch(`{{ url('/api/tickets') }}/${ticketId}/confirm`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Tiket berhasil dikonfirmasi. Status berubah menjadi "In Progress".',
                        confirmButtonColor: '#2e7d32'
                    });
                    loadTechnicianTasks();
                } else {
                    throw new Error(result.message || 'Gagal konfirmasi tiket');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message,
                    confirmButtonColor: '#d62828'
                });
            }
        }

        async function rejectTicket(ticketId, ticketNumber) {
            const result = await Swal.fire({
                title: 'Tolak Tiket',
                text: `Anda akan menolak tiket #${ticketNumber}. Tiket akan kembali ke status "Open".`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d32f2f',
                cancelButtonColor: '#999',
                confirmButtonText: 'Ya, Tolak',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;

            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
            
            try {
                const response = await fetch(`{{ url('/api/tickets') }}/${ticketId}/reject`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Tiket berhasil ditolak. Status kembali ke "Open".',
                        confirmButtonColor: '#2e7d32'
                    });
                    loadTechnicianTasks();
                } else {
                    throw new Error(result.message || 'Gagal menolak tiket');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message,
                    confirmButtonColor: '#d62828'
                });
            }
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        document.getElementById('updateForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const ticketId = document.getElementById('resolveTicketId').value;
            const solution = document.getElementById('solutionText').value.trim();
            const resolvedAt = document.getElementById('resolvedAt').value;

            if (!solution) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Solusi Required',
                    text: 'Mohon isi solusi/tindakan perbaikan.',
                    confirmButtonColor: '#d62828'
                });
                return;
            }

            // Convert datetime-local to MySQL format
            const dateObj = new Date(resolvedAt);
            const mysqlDateTime = dateObj.getFullYear() + '-' + 
                String(dateObj.getMonth() + 1).padStart(2, '0') + '-' +
                String(dateObj.getDate()).padStart(2, '0') + ' ' +
                String(dateObj.getHours()).padStart(2, '0') + ':' +
                String(dateObj.getMinutes()).padStart(2, '0') + ':00';

            closeModal('modalUpdate');

            Swal.fire({
                title: 'Menyimpan...',
                text: 'Sedang menyelesaikan tiket',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');

            try {
                const response = await fetch(`{{ url('/api/tickets') }}/${ticketId}/solve`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        solution: solution,
                        resolved_at: mysqlDateTime
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Tiket berhasil diselesaikan. Status berubah menjadi "Resolved".',
                        confirmButtonColor: '#2e7d32'
                    });
                    loadTechnicianTasks();
                } else {
                    throw new Error(result.message || 'Gagal menyelesaikan tiket');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message,
                    confirmButtonColor: '#d62828'
                });
            }
        });

        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadTechnicianTasks();
        });
    </script>
@endsection
