@extends('layouts.technician')
@section('title', 'Dashboard Teknisi')

@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/dashboard-technician.css'])
@endsection

@section('content')
    <div class="header-welcome">
        <div class="user-info">
            <h2>Semangat Pagi, Teknisi! 🛠️</h2>
            <p>Prioritas: Menjaga Operasional Mesin & IT.</p>
        </div>
    </div>

    <div id="dashboardContent">
        <div class="loading">
            <p>Loading dashboard data...</p>
        </div>
    </div>

    <div id="modalUpdate" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Update Pengerjaan</h3>
                <button class="btn-close" onclick="closeModal('modalUpdate')">&times;</button>
            </div>

            <form id="updateForm">
                <div
                    style="background: #e8f5e9; padding: 10px 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c8e6c9;">
                    <strong style="color: #2e7d32; font-size: 13px;">Tiket: <span id="uSubject">...</span></strong>
                </div>

                <div class="form-group">
                    <label class="form-label">Status Pengerjaan</label>
                    <select class="form-select" id="uStatus">
                        <option value="On Progress">Sedang Dikerjakan (On Progress)</option>
                        <option value="Waiting Sparepart">Menunggu Sparepart</option>
                        <option value="Pending Vendor">Pending Vendor</option>
                        <option value="Resolved">Selesai (Resolved)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Tindakan Perbaikan / Catatan Teknisi</label>
                    <textarea class="form-textarea" placeholder="Contoh: Sudah dilakukan penggantian bearing, mesin normal kembali."
                        required></textarea>
                </div>

                <div style="text-align: right; margin-top: 10px;">
                    <button type="button" onclick="closeModal('modalUpdate')"
                        style="background:white; border:1px solid #ddd; padding:10px 20px; border-radius:8px; cursor:pointer; margin-right: 10px;">Batal</button>
                    <button type="submit"
                        style="background:#2e7d32; color:white; border:none; padding:10px 25px; border-radius:8px; cursor:pointer; font-weight:600;">Simpan
                        Laporan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // 1. Logic Modal Update
        function openUpdate(id, subject) {
            document.getElementById('uSubject').innerText = id + " - " + subject;
            document.getElementById('modalUpdate').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // 2. Simulasi Simpan Data
        document.getElementById('updateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            closeModal('modalUpdate');

            let timerInterval;
            Swal.fire({
                title: 'Menyimpan...',
                html: 'Mohon tunggu sebentar.',
                timer: 1000,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                },
                willClose: () => {
                    clearInterval(timerInterval);
                }
            }).then((result) => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Status tiket berhasil diperbarui.',
                    confirmButtonColor: '#2e7d32',
                });
<script>
    // Fetch dashboard data from API
    async function loadDashboard() {
        try {
            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
            
            const response = await fetch('{{ url("/api/dashboard") }}', {
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
            const data = result.data;

            // Build HTML
            let html = `
                <div class="stats-grid">
                    <div class="stat-card card-blue">
                        <div class="stat-info"><p>Total Ditangani</p><h3>${data.summary.total}</h3></div>
                        <div class="stat-icon"><i class="fa-solid fa-star"></i></div>
                    </div>
                    <div class="stat-card card-orange">
                        <div class="stat-info"><p>Tugas Assigned</p><h3>${data.summary.assigned}</h3></div>
                        <div class="stat-icon"><i class="fa-solid fa-tasks"></i></div>
                    </div>
                    <div class="stat-card card-red">
                        <div class="stat-info"><p>Sedang Dikerjakan</p><h3>${data.summary.in_progress}</h3></div>
                        <div class="stat-icon"><i class="fa-solid fa-hourglass-end"></i></div>
                    </div>
                    <div class="stat-card card-green">
                        <div class="stat-info"><p>Selesai Hari Ini</p><h3>${data.summary.solved_today}</h3></div>
                        <div class="stat-icon"><i class="fa-solid fa-check-circle"></i></div>
                    </div>
                </div>

                <h3 class="section-title">Daftar Tugas Anda</h3>
            `;

            if (data.my_tickets && data.my_tickets.length > 0) {
                data.my_tickets.forEach(ticket => {
                    const categoryClass = ticket.category.toLowerCase().includes('hardware') ? 'cat-mech' : 'cat-it';
                    const categoryBadgeClass = ticket.category.toLowerCase().includes('hardware') ? 'badge-mech' : 'badge-it';
                    const statusBadgeClass = getStatusBadgeClass(ticket.status);

                    html += `
                        <div class="task-card ${categoryClass}">
                            <div class="task-content">
                                <h4>${ticket.subject} <span class="badge-cat ${categoryBadgeClass}">${ticket.category}</span></h4>
                                <div class="task-meta">
                                    <span><i class="fa-solid fa-ticket"></i> ${ticket.ticket_number}</span>
                                    <span><i class="fa-solid fa-user"></i> ${ticket.requester}</span>
                                    <span><i class="fa-regular fa-clock"></i> ${ticket.created_at}</span>
                                    <span><span class="badge-status ${statusBadgeClass}">${ticket.status}</span></span>
                                </div>
                            </div>
                            <button class="btn-update" onclick="openUpdate('${ticket.ticket_number}', '${ticket.subject}', ${ticket.id})">
                                Update Status <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </div>
                    `;
                });
            } else {
                html += `
                    <div class="empty-state">
                        <i class="fa-solid fa-inbox"></i>
                        <p>Tidak ada tugas yang ditugaskan kepada Anda saat ini</p>
                    </div>
                `;
            }

            document.getElementById('dashboardContent').innerHTML = html;

        } catch (error) {
            console.error('Error:', error);
            document.getElementById('dashboardContent').innerHTML = `
                <div class="error">
                    <strong>Error!</strong> Gagal memuat dashboard data. ${error.message}
                </div>
            `;
        }
    }

    function getStatusBadgeClass(status) {
        const statusMap = {
            'Open': 'open',
            'Assigned': 'assigned',
            'In Progress': 'in-progress',
            'Resolved': 'resolved',
            'Closed': 'closed'
        };
        return statusMap[status] || 'open';
    }

    // Logic Modal Update
    function openUpdate(ticketNumber, subject, ticketId) {
        document.getElementById('uSubject').innerText = ticketNumber + " - " + subject;
        document.getElementById('modalUpdate').dataset.ticketId = ticketId;
        document.getElementById('modalUpdate').style.display = 'flex';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Simulasi Simpan Data
    document.getElementById('updateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        closeModal('modalUpdate');
        
        let timerInterval;
        Swal.fire({
            title: 'Menyimpan...',
            html: 'Mohon tunggu sebentar.',
            timer: 1000,
            timerProgressBar: true,
            didOpen: () => { Swal.showLoading(); },
            willClose: () => { clearInterval(timerInterval); }
        }).then((result) => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Status tiket berhasil diperbarui.',
                confirmButtonColor: '#2e7d32',
            }).then(() => {
                // Reload dashboard
                loadDashboard();
            });
        });

        // Close on click outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = 'none';
            }
        }
    </script>
@endsection
    }

    // Load dashboard on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadDashboard();
    });
</script>
@endsection
