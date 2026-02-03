@extends('layouts.requester')
@section('title', 'Dashboard')

@section('css')
    @vite(['resources/css/dashboard-requester.css'])
@endsection

@section('content')
    <div class="header-welcome">
        <div class="user-info">
            <h2>Halo, <span id="user-name">Loading...</span>! 👋</h2>
            <p>Selamat datang di Arwana Helpdesk System</p>
        </div>
        <div class="user-avatar">
            <div class="avatar-circle">
            </div>
        </div>
    </div>

    <div class="stats-grid">

        <div class="stat-card card-blue">
            <div class="stat-info">
                <p>Total Tiket Saya</p>
                <h3 id="stat-total">-</h3>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-ticket"></i>
            </div>
        </div>

        <div class="stat-card card-orange">
            <div class="stat-info">
                <p>Sedang Diproses</p>
                <h3 id="stat-process">-</h3>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-spinner"></i>
            </div>
        </div>

        <div class="stat-card card-green">
            <div class="stat-info">
                <p>Tiket Selesai</p>
                <h3 id="stat-solved">-</h3>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-check-double"></i>
            </div>
        </div>

    </div>

    <h3 class="section-title">Tiket Terbaru Anda</h3>
    <div id="dashboardContent">
        <div class="loading">
            <p>Loading tiket Anda...</p>
        </div>
    </div>

    <script>
        
        const authUser = JSON.parse(sessionStorage.getItem('auth_user') || '{}');
        document.getElementById('user-name').textContent = authUser.name || 'Guest';
        const firstLetter = (authUser.name).charAt(0).toUpperCase();
        document.querySelector('.avatar-circle').textContent = firstLetter;

        function getStatusBadgeClass(status) {
            const statusMap = {
                'open': 'open',
                'assigned': 'assigned',
                'in progress': 'in-progress',
                'resolved': 'resolved',
                'closed': 'closed'
            };
            return statusMap[status?.toLowerCase()] || 'open';
        }

        function getCategoryClass(category) {
            if (!category) return 'cat-other';
            const categoryLower = category.toLowerCase();
            if (categoryLower.includes('hardware')) return 'cat-mech';
            if (categoryLower.includes('it') || categoryLower.includes('account') || categoryLower.includes('network') || categoryLower.includes('software')) return 'cat-it';
            return 'cat-other';
        }

        function getCategoryBadgeClass(category) {
            if (!category) return 'badge-other';
            const categoryLower = category.toLowerCase();
            if (categoryLower.includes('hardware')) return 'badge-mech';
            if (categoryLower.includes('it') || categoryLower.includes('account') || categoryLower.includes('network') || categoryLower.includes('software')) return 'badge-it';
            return 'badge-other';
        }

        async function loadDashboard() {
            try {
                const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
                const response = await fetch('/api/dashboard', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Gagal mengambil data dashboard');
                }

                const result = await response.json();
                const data = result?.data || {};
                const summary = data.summary || {};
                const tickets = Array.isArray(data.my_tickets) ? data.my_tickets : [];

                // Update stat cards
                document.getElementById('stat-total').textContent = summary.total ?? 0;
                document.getElementById('stat-process').textContent = summary.process ?? 0;
                document.getElementById('stat-solved').textContent = summary.solved ?? 0;

                // Build dashboard content
                let html = '';

                if (tickets.length === 0) {
                    html = `
                        <div class="empty-state">
                            <i class="fa-solid fa-inbox" style="font-size: 48px; color: #ddd; margin-bottom: 15px;"></i>
                            <p>Belum ada tiket hari ini</p>
                            <a href="{{ route('tickets.create') }}" style="color: #d62828; text-decoration: none; font-weight: 600; margin-top: 10px; display: inline-block;">Buat Tiket Sekarang →</a>
                        </div>
                    `;
                } else {
                    tickets.forEach(ticket => {
                        const categoryClass = getCategoryClass(ticket.category?.name);
                        const categoryBadgeClass = getCategoryBadgeClass(ticket.category?.name);
                        const statusBadgeClass = getStatusBadgeClass(ticket.status?.name);
                        const requesterName = ticket.requester?.name || 'Unknown';
                        const categoryName = ticket.category?.name || 'Other';
                        const statusName = ticket.status?.name || 'Unknown';
                        const createdAt = ticket.created_at ? new Date(ticket.created_at).toLocaleString('id-ID', { 
                            year: 'numeric', 
                            month: '2-digit', 
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : '-';

                        html += `
                            <div class="task-card ${categoryClass}">
                                <div class="task-content">
                                    <h4>${ticket.subject} <span class="badge-cat ${categoryBadgeClass}">${categoryName}</span></h4>
                                    <div class="task-meta">
                                        <span><i class="fa-solid fa-ticket"></i> ${ticket.ticket_number}</span>
                                        <span><i class="fa-solid fa-user"></i> ${requesterName}</span>
                                        <span><i class="fa-regular fa-clock"></i> ${createdAt}</span>
                                        <span><span class="badge-status ${statusBadgeClass}">${statusName}</span></span>
                                    </div>
                                </div>
                                <a href="{{ url('tickets') }}/${ticket.id}" class="btn-view" style="text-decoration: none;">
                                    Lihat Detail <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        `;
                    });
                }

                document.getElementById('dashboardContent').innerHTML = html;

            } catch (error) {
                console.error('Error:', error);
                document.getElementById('dashboardContent').innerHTML = `
                    <div class="error" style="background: #ffebee; padding: 20px; border-radius: 8px; color: #d62828;">
                        <strong>Error!</strong> Gagal memuat dashboard. ${error.message}
                    </div>
                `;
            }
        }

        // Load dashboard on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboard();
        });
    </script>
@endsection
