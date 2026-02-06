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

    <div id="dashboardContent">
        <div class="loading">
            <p>Loading dashboard data...</p>
        </div>
    </div>


@endsection

@section('scripts')
    <script>
    
    // Fetch dashboard data from API
    async function loadDashboard() {
        try {
            const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
            
            // Tampilkan loading dulu
            document.getElementById('dashboardContent').innerHTML = `
                <div class="loading">
                    <i class="fa-solid fa-circle-notch fa-spin fa-2x"></i>
                    <p style="margin-top:10px;">Mengambil data terbaru...</p>
                </div>
            `;

            const response = await fetch('{{ url("/api/dashboard") }}', {
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
                    <div class="stat-card card-red">
                        <div class="stat-info"><p>Belum Di-Assign</p><h3>${data.summary.unassigned}</h3></div>
                        <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    </div>
                    <div class="stat-card card-orange">
                        <div class="stat-info"><p>Assign Hari Ini</p><h3>${data.summary.assigned_today}</h3></div>
                        <div class="stat-icon"><i class="fa-solid fa-list-ul"></i></div>
                    </div>
                    <div class="stat-card card-blue">
                        <div class="stat-info"><p>Total Minggu Ini</p><h3>${data.summary.this_week}</h3></div>
                        <div class="stat-icon"><i class="fa-solid fa-calendar-week"></i></div>
                    </div>
                    <div class="stat-card card-purple">
                        <div class="stat-info"><p>Teknisi Ready</p><h3>${data.summary.technicians}</h3></div>
                        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                    </div>
                </div>

                <div class="table-container">
                    <div class="section-title">
                        <i class="fa-solid fa-bullseye" style="color:#ef4444;"></i> 
                        Tiket Baru (Butuh Penanganan)
                    </div>
                    <table class="urgent-table">
                        <thead>
                            <tr>
                                <th>ID Tiket</th>
                                <th>Subjek Masalah</th>
                                <th>Kategori</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            if (data.unassigned_tickets && data.unassigned_tickets.length > 0) {
                data.unassigned_tickets.forEach(ticket => {
                    // Logic warna kategori sederhana
                    const catLower = (ticket.category || '').toLowerCase();
                    let badgeClass = 'dept-other';
                    if (catLower.includes('hardware') || catLower.includes('jaringan')) badgeClass = 'dept-hardware';
                    if (catLower.includes('akun') || catLower.includes('akses')) badgeClass = 'dept-account';

                    html += `
                        <tr>
                            <td><span style="font-family:monospace; font-weight:700;">${ticket.ticket_number}</span></td>
                            <td>
                                <div style="font-weight:600; color:#111;">${ticket.subject}</div>
                                <div style="font-size:12px; color:#888;">From: ${ticket.requester_name || 'User'}</div>
                            </td>
                            <td><span class="badge-dept ${badgeClass}">${ticket.category}</span></td>
                            <td>${ticket.created_at_human || ticket.created_at}</td>
                            <td>
                                <button class="btn-assign" onclick="window.location.href = '{{ url('/helpdesk/tickets') }}/' + ${ticket.id}">
                                    Detail <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                html += `
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fa-solid fa-clipboard-check"></i>
                                <p>Tidak ada tiket baru yang menunggu.</p>
                            </div>
                        </td>
                    </tr>
                `;
            }

            html += `   </tbody>
                    </table>
                </div>`;

            document.getElementById('dashboardContent').innerHTML = html;

        } catch (error) {
            console.error('Error:', error);
            document.getElementById('dashboardContent').innerHTML = `
                <div style="text-align:center; padding:40px; color:#ef4444;">
                    <i class="fa-solid fa-triangle-exclamation fa-2x mb-2"></i><br>
                    <strong>Gagal memuat dashboard.</strong><br>
                    <small>${error.message}</small>
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