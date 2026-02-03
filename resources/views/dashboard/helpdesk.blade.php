@extends('layouts.helpdesk')

@section('title', 'Dashboard - Helpdesk Arwana')

@section('css')
    @vite(['resources/css/dashboard-helpdesk.css'])
@endsection

@section('content')
    <div class="header-welcome">
        <div class="user-info">
            <h2>Halo, Tim Helpdesk! 👋</h2>
            <p>Pantau aktivitas tiket dan distribusi tugas teknisi hari ini.</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card card-red">
            <div class="stat-info">
                <p>Belum Di-Assign</p>
                <h3>5</h3>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
        </div>
        <div class="stat-card card-blue">
            <div class="stat-info">
                <p>Tiket Bulan Ini</p>
                <h3>145</h3>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-globe"></i></div>
        </div>
        <div class="stat-card card-purple">
            <div class="stat-info">
                <p>Teknisi Ready</p>
                <h3>4</h3>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-headset"></i></div>
        </div>
        <div class="stat-card card-orange">
            <div class="stat-info">
                <p>Open Tickets</p>
                <h3>12</h3>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-list-ul"></i></div>
        </div>
    </div>

    <div class="table-container">
        <div class="section-title">Tiket Baru (Butuh Penanganan)</div>
        <table class="urgent-table">
            <thead>
                <tr>
                    <th width="15%">ID TIKET</th>
                    <th width="30%">SUBJEK MASALAH</th>
                    <th width="20%">DEPARTEMEN</th>
                    <th width="20%">WAKTU DIBUAT</th>
                    <th width="15%">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><b>#TKT-009</b></td>
                    <td>
                        <div style="font-weight:600;">Server Ruko Down (Urgent)</div>
                    </td>
                    <td><span class="badge-dept dept-finance">Finance</span></td>
                    <td>10 Menit lalu</td>
                    <td>
                        <button class="btn-assign" onclick="openModal('TKT-009', 'Server Ruko Down')">
                            Pilih Teknisi <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td><b>#TKT-008</b></td>
                    <td>
                        <div style="font-weight:600;">Minta Akses SAP</div>
                    </td>
                    <td><span class="badge-dept dept-marketing">Marketing</span></td>
                    <td>30 Menit lalu</td>
                    <td>
                        <button class="btn-assign" onclick="openModal('TKT-008', 'Minta Akses SAP')">
                            Pilih Teknisi <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="dashboardContent">
        <div class="loading">
            <p>Loading dashboard data...</p>
        </div>
    </div>

    <div class="modal-overlay" id="assignModal">
        <div class="modal-box">
            <h3 style="margin-bottom:20px; font-size:18px; font-weight:700;">Assign Teknisi</h3>

            <div
                style="margin-bottom:20px; background:#fff5f5; padding:15px; border-radius:10px; border: 1px solid #ffcdd2;">
                <div style="font-size: 12px; color: #d62828; font-weight: 600;" id="modalTicketId">#ID</div>
                <div style="font-weight: 700; font-size: 15px; color: #333; margin-top: 3px;" id="modalTicketSubject">
                    Subject</div>
            </div>

            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px; color: #555;">Pilih Teknisi:</label>
                <select class="form-select" id="technicianSelect">
                    <option value="">-- Pilih Teknisi --</option>
                </select>
            </div>

            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal()">Batal</button>
                <button class="btn-save" onclick="simpanAssignment()">Simpan & Kirim</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const modal = document.getElementById('assignModal');

        function openModal(id, subject) {
            document.getElementById('modalTicketId').innerText = '#' + id;
            document.getElementById('modalTicketSubject').innerText = subject;
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        function simpanAssignment() {
            closeModal();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Tiket telah ditugaskan.',
                timer: 2000,
                showConfirmButton: false,
                confirmButtonColor: '#d62828'
            });
        }
    </script>
@endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const modal = document.getElementById('assignModal');
    let activeTechnicians = [];
    
    // Fetch active technicians
    async function loadActiveTechnicians() {
        try {
            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
            
            const response = await fetch('{{ url("/api/technicians/active") }}', {
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
            activeTechnicians = result.data || [];
            
            // Populate dropdown
            populateTechnicianDropdown();

        } catch (error) {
            console.error('Error loading technicians:', error);
            activeTechnicians = [];
        }
    }

    function populateTechnicianDropdown() {
        const select = document.getElementById('technicianSelect');
        
        // Clear existing options except the default one
        while (select.options.length > 1) {
            select.remove(1);
        }

        // Add technicians
        if (activeTechnicians.length > 0) {
            activeTechnicians.forEach(tech => {
                const option = document.createElement('option');
                option.value = tech.id;
                option.textContent = `👨‍🔧 ${tech.name} (${tech.department?.name || 'N/A'})`;
                select.appendChild(option);
            });
        }
    }
    
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
                    <div class="stat-card card-red">
                        <div class="stat-info"><p>Belum Di-Assign</p><h3>${data.summary.unassigned}</h3></div>
                        <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    </div>
                    <div class="stat-card card-blue">
                        <div class="stat-info"><p>Tiket Minggu Ini</p><h3>${data.summary.this_week}</h3></div>
                        <div class="stat-icon"><i class="fa-solid fa-globe"></i></div>
                    </div>
                    <div class="stat-card card-purple">
                        <div class="stat-info"><p>Teknisi Ready</p><h3>${data.summary.technicians}</h3></div>
                        <div class="stat-icon"><i class="fa-solid fa-headset"></i></div>
                    </div>
                    <div class="stat-card card-orange">
                        <div class="stat-info"><p>Assign Hari Ini</p><h3>${data.summary.assigned_today}</h3></div>
                        <div class="stat-icon"><i class="fa-solid fa-list-ul"></i></div>
                    </div>
                </div>

                <div class="table-container">
                    <div class="section-title">🎯 Tiket Baru (Butuh Penanganan)</div>
                    <table class="urgent-table">
                        <thead>
                            <tr>
                                <th width="15%">ID TIKET</th>
                                <th width="30%">SUBJEK MASALAH</th>
                                <th width="20%">KATEGORI</th>
                                <th width="20%">WAKTU DIBUAT</th>
                                <th width="15%">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            if (data.unassigned_tickets && data.unassigned_tickets.length > 0) {
                data.unassigned_tickets.forEach(ticket => {
                    const deptClass = getCategoryClass(ticket.category);
                    html += `
                        <tr>
                            <td><b>${ticket.ticket_number}</b></td>
                            <td><div style="font-weight:600;">${ticket.subject}</div></td>
                            <td><span class="badge-dept ${deptClass}">${ticket.category}</span></td>
                            <td>${ticket.created_at}</td>
                            <td>
                                <button class="btn-assign" onclick="openModal('${ticket.ticket_number}', '${ticket.subject}', ${ticket.id})">
                                    Pilih Teknisi <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                html += `
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px;">
                            <div class="empty-state">
                                <i class="fa-solid fa-check-circle"></i>
                                <p>Semua tiket sudah di-assign!</p>
                            </div>
                        </td>
                    </tr>
                `;
            }

            html += `
                        </tbody>
                    </table>
                </div>
            `;

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

    function getCategoryClass(category) {
        const categoryMap = {
            'Hardware': 'dept-hardware',
            'Account & Access': 'dept-account',
            'Other': 'dept-other',
        };
        
        const defaultClass = 'dept-other';
        
        if (category.toLowerCase().includes('hardware')) return 'dept-hardware';
        if (category.toLowerCase().includes('account')) return 'dept-account';
        return defaultClass;
    }

    function openModal(ticketNumber, subject, ticketId) {
        document.getElementById('modalTicketId').innerText = '#' + ticketNumber;
        document.getElementById('modalTicketSubject').innerText = subject;
        modal.dataset.ticketId = ticketId;
        modal.style.display = 'flex';
    }

    function closeModal() { 
        modal.style.display = 'none'; 
    }

    function simpanAssignment() {
        const ticketId = modal.dataset.ticketId;
        const technicianId = document.getElementById('technicianSelect').value;
        
        if (!technicianId) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Teknisi!',
                text: 'Silakan pilih teknisi sebelum menyimpan.',
                confirmButtonColor: '#d62828'
            });
            return;
        }

        // Find selected technician data
        const selectedTech = activeTechnicians.find(t => t.id == technicianId);
        const technicianName = selectedTech?.name || 'Teknisi';

        closeModal();
        Swal.fire({ 
            icon: 'success', 
            title: 'Berhasil!', 
            text: `Tiket telah ditugaskan ke ${technicianName}.`, 
            timer: 2000, 
            showConfirmButton: false, 
            confirmButtonColor: '#d62828',
            didClose: () => {
                loadDashboard();
            }
        });
    }

    // Load dashboard and technicians on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadActiveTechnicians();
        loadDashboard();
    });
</script>
@endsection
