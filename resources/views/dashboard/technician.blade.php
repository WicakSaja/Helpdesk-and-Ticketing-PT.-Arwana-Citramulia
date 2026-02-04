@extends('layouts.technician')
@section('title', 'Dashboard Teknisi')

@section('css')
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

@endsection

@section('scripts')
    <script>
        // === Dashboard Logic (Technician) ===

        async function loadDashboard() {
            try {
                const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
                const response = await fetch('{{ url('/api/dashboard') }}', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

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
                        const categoryClass = ticket.category.toLowerCase().includes('hardware') ? 'cat-mech' :
                            'cat-it';
                        const categoryBadgeClass = ticket.category.toLowerCase().includes('hardware') ?
                            'badge-mech' : 'badge-it';
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
                                <button class="btn-detail" onclick="window.location.href = '{{ url('/technician/tickets') }}/' + ${ticket.id}">
                                    Lihat Detail <i class="fa-solid fa-arrow-right"></i>
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

        // Load dashboard on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboard();
        });
    </script>
@endsection
