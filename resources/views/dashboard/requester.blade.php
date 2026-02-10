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
            <div class="avatar-circle"></div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="stats-grid">
        <div class="stat-card card-blue">
            <div class="stat-info">
                <p>Total Tiket</p>
                <h3 id="stat-total">-</h3>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-ticket"></i></div>
        </div>

        <div class="stat-card card-orange">
            <div class="stat-info">
                <p>Diproses</p>
                <h3 id="stat-process">-</h3>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-spinner"></i></div>
        </div>

        <div class="stat-card card-green">
            <div class="stat-info">
                <p>Selesai</p>
                <h3 id="stat-solved">-</h3>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-check-double"></i></div>
        </div>
    </div>

    <h3 class="section-title">Tiket Terbaru Anda</h3>
    <div id="dashboardContent">
        <div class="loading">
            <i class="fa-solid fa-circle-notch fa-spin"></i> Loading tiket Anda...
        </div>
    </div>

    <script>
        const authUser = JSON.parse(sessionStorage.getItem('auth_user') || '{}');
        document.getElementById('user-name').textContent = authUser.name || 'Guest';

        if (authUser.name) {
            const firstLetter = (authUser.name).charAt(0).toUpperCase();
            document.querySelector('.avatar-circle').textContent = firstLetter;
        }

        // Helper Classes
        function getStatusClass(status) {
            const s = (status || '').toLowerCase();
            if (s.includes('progress')) return 'in-progress';
            return s;
        }

        function getCategoryStyle(cat) {
            const c = (cat || '').toLowerCase();
            if (c.includes('hardware') || c.includes('mesin')) return {
                border: 'cat-mech',
                badge: 'badge-mech'
            };
            if (c.includes('it') || c.includes('network') || c.includes('software')) return {
                border: 'cat-it',
                badge: 'badge-it'
            };
            return {
                border: 'cat-other',
                badge: 'badge-other'
            };
        }

        async function loadDashboard() {
            try {
                const token = sessionStorage.getItem('auth_token');
                const response = await fetch('/api/dashboard', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Gagal koneksi server');

                const result = await response.json();
                const data = result?.data || {};
                const summary = data.summary || {};
                const tickets = Array.isArray(data.my_tickets) ? data.my_tickets : [];

                document.getElementById('stat-total').textContent = summary.total ?? 0;
                document.getElementById('stat-process').textContent = summary.process ?? 0;
                document.getElementById('stat-solved').textContent = summary.solved ?? 0;

                let html = '';

                if (tickets.length === 0) {
                    html = `
                        <div class="empty-state">
                            <i class="fa-solid fa-box-open" style="font-size: 32px; color: #e5e7eb; margin-bottom: 10px;"></i>
                            <p>Belum ada tiket.</p>
                            <a href="{{ route('tickets.create') }}" class="btn-detail" style="background:var(--primary); color:white; border:none; display:inline-flex; width:auto;">
                                + Buat Tiket Baru
                            </a>
                        </div>`;
                } else {
                    tickets.forEach(ticket => {
                        const catName = ticket.category?.name || 'Umum';
                        const statusName = ticket.status?.name || 'Open';

                        const styles = getCategoryStyle(catName);
                        const statusClass = getStatusClass(statusName);

                        const date = ticket.created_at ? new Date(ticket.created_at).toLocaleDateString(
                            'id-ID', {
                                day: 'numeric',
                                month: 'short',
                                hour: '2-digit',
                                minute: '2-digit'
                            }) : '-';

                        // FIX LINK DETAIL: Menggunakan route URL yang benar
                        const detailUrl = "{{ url('tickets') }}/" + ticket.id;

                        html += `
                            <div class="task-card ${styles.border}">
                                <div class="task-content">
                                    <h4>
                                        ${ticket.subject} 
                                        <span class="badge-cat ${styles.badge}">${catName}</span>
                                    </h4>
                                    <div class="task-meta">
                                        <span><i class="fa-solid fa-hashtag"></i> ${ticket.ticket_number}</span>
                                        <span><i class="fa-regular fa-clock"></i> ${date}</span>
                                        <span class="status-text ${statusClass}">${statusName}</span>
                                    </div>
                                </div>
                                <a href="${detailUrl}" class="btn-detail">
                                    Detail <i class="fa-solid fa-chevron-right" style="font-size:10px;"></i>
                                </a>
                            </div>
                        `;
                    });
                }

                document.getElementById('dashboardContent').innerHTML = html;

            } catch (error) {
                console.error(error);
                document.getElementById('dashboardContent').innerHTML =
                    `<div class="loading text-danger">Gagal memuat data.</div>`;
            }
        }

        document.addEventListener('DOMContentLoaded', loadDashboard);
    </script>
@endsection
