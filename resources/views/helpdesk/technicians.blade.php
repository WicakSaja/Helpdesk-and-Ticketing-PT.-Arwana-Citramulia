@extends('layouts.helpdesk')
@section('title', 'Kelola Teknisi')

@section('css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #206bc4;
            --success: #2fb344;
            --danger: #d63939;
            --warning: #f59f00;
            --dark: #1e293b;
            --muted: #64748b;
            --bg-surface: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
        }

        /* --- HEADER --- */
        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark);
            margin: 0;
        }

        .page-subtitle {
            color: var(--muted);
            font-size: 0.9rem;
            margin-top: 4px;
        }

        /* --- GRID SYSTEM (RESPONSIF & COMPACT) --- */
        .tech-grid {
            display: grid;
            /* Desktop: 4 kolom, Tablet: 3 kolom */
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding-bottom: 40px;
        }

        /* --- CARD STYLE --- */
        .tech-card {
            background: var(--bg-surface);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: all 0.2s ease;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .tech-card:hover {
            border-color: var(--primary);
            box-shadow: 0 10px 20px rgba(32, 107, 196, 0.1);
            transform: translateY(-3px);
        }

        /* Status Badge (Pojok Kanan Atas) */
        .status-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background: #e6fffa;
            color: #0ca678;
            border: 1px solid #20c997;
        }

        .status-inactive {
            background: #fff5f5;
            color: #d63939;
            border: 1px solid #ffcdd2;
        }

        /* Avatar */
        .tech-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 12px;
            border: 3px solid #f8fafc;
            box-shadow: 0 0 0 1px #e2e8f0;
        }

        /* Nama & Role */
        .tech-name {
            font-size: 1rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 2px;
            /* Truncate text panjang */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .tech-role {
            font-size: 0.75rem;
            color: var(--muted);
            background: #f1f5f9;
            padding: 2px 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-weight: 500;
        }

        /* Stats (Hanya Proses & Selesai) */
        .stats-container {
            display: flex;
            width: 100%;
            border-top: 1px dashed #e2e8f0;
            padding-top: 12px;
            margin-top: auto;
        }

        .stat-box {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        /* Garis pemisah tengah */
        .stat-box:first-child {
            border-right: 1px solid #f1f5f9;
        }

        .stat-num {
            font-size: 1.1rem;
            font-weight: 800;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 600;
        }

        /* --- MODAL STYLES (Tetap Rapih) --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
            z-index: 1050;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-box {
            background: #fff;
            width: 100%;
            max-width: 450px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.3s ease;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header-custom {
            background: #f8fafc;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .modal-body-custom {
            padding: 20px;
            overflow-y: auto;
        }

        .task-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .btn-close {
            width: 100%;
            padding: 15px;
            background: white;
            border: none;
            border-top: 1px solid #eee;
            color: #555;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-close:hover {
            background: #f9f9f9;
            color: #000;
        }

        /* --- LOADING --- */
        .loading-container {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            color: var(--muted);
        }

        /* --- MEDIA QUERY KHUSUS HP (TAMPILAN 2 KOLOM) --- */
        @media (max-width: 576px) {
            .page-header {
                margin-bottom: 20px;
            }

            /* KUNCI: Paksa 2 Kolom di HP */
            .tech-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
                /* Jarak antar kartu lebih rapat */
            }

            .tech-card {
                padding: 15px 10px;
                /* Padding dalam diperkecil */
            }

            .tech-avatar {
                width: 56px;
                /* Avatar diperkecil */
                height: 56px;
                font-size: 1.2rem;
                margin-bottom: 8px;
            }

            .tech-name {
                font-size: 0.9rem;
            }

            /* Nama font diperkecil */
            .tech-role {
                font-size: 0.65rem;
                margin-bottom: 10px;
                padding: 2px 6px;
            }

            .status-badge {
                font-size: 0.55rem;
                padding: 2px 6px;
                top: 8px;
                right: 8px;
            }

            .stat-num {
                font-size: 1rem;
            }

            .stat-label {
                font-size: 0.6rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Tim Teknisi</h1>
        <p class="page-subtitle">Monitoring status personel & beban kerja.</p>
    </div>

    <div class="tech-grid" id="technicianGrid">
        <div class="loading-container">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="mt-2 small">Memuat data...</div>
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    <div class="modal-overlay" id="detailModal">
        <div class="modal-box">
            <div class="modal-header-custom">
                <div class="tech-avatar mx-auto" id="dAvatar" style="width: 80px; height: 80px; margin-bottom: 10px;">AB
                </div>
                <h3 id="dName" style="margin: 0; color: #1e293b;">Nama</h3>
                <p id="dDept" style="margin: 5px 0 0; color: #64748b; font-size: 0.9rem;">Departemen</p>
            </div>
            <div class="modal-body-custom">
                <h6
                    style="text-transform: uppercase; font-size: 0.75rem; color: #94a3b8; font-weight: 700; margin-bottom: 15px;">
                    Tiket Dikerjakan</h6>
                <div id="ticketsList"></div>
            </div>
            <button class="btn-close" onclick="closeDetailModal()">Tutup Detail</button>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let allTechnicians = [];
        const baseUrl = (typeof API_URL !== 'undefined') ? API_URL : "{{ url('/') }}";

        // Cek helper di layout
        if (typeof getAuthHeaders === 'undefined') {
            window.getAuthHeaders = () => {
                const token = sessionStorage.getItem('auth_token');
                return {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                };
            };
        }

        if (typeof fetchWithAuth === 'undefined') {
            window.fetchWithAuth = async (url, options = {}) => {
                try {
                    const response = await fetch(url, {
                        ...options,
                        headers: {
                            ...getAuthHeaders(),
                            ...options.headers
                        }
                    });
                    if (response.status === 401) {
                        window.location.href = '/login';
                        return null;
                    }
                    return response;
                } catch (error) {
                    console.error(error);
                    return null;
                }
            };
        }

        document.addEventListener('DOMContentLoaded', fetchTechnicians);

        async function fetchTechnicians() {
            try {
                const response = await fetchWithAuth(`${baseUrl}/api/users/by-role/technician`);

                if (!response || !response.ok) throw new Error('Gagal mengambil data');

                const data = await response.json();
                allTechnicians = data.data || [];
                renderTechnicians(allTechnicians);
            } catch (error) {
                document.getElementById('technicianGrid').innerHTML = `
                <div class="loading-container">
                    <p class="text-danger fw-bold">Gagal memuat data.</p>
                    <button class="btn btn-sm btn-outline-primary" onclick="location.reload()">Refresh</button>
                </div>`;
            }
        }

        function renderTechnicians(technicians) {
            const grid = document.getElementById('technicianGrid');

            if (technicians.length === 0) {
                grid.innerHTML = `<div class="loading-container"><p>Tidak ada teknisi.</p></div>`;
                return;
            }

            grid.innerHTML = technicians.map(tech => {
                const isActive = !!tech.is_active;
                const statusLabel = isActive ? 'AKTIF' : 'OFF'; // Label Jelas
                const statusClass = isActive ? 'status-active' : 'status-inactive';

                const initials = getInitials(tech.name);
                const hue = (tech.id * 137.508) % 360;
                const bgAvatar = `hsl(${hue}, 70%, 90%)`;
                const textAvatar = `hsl(${hue}, 80%, 30%)`;

                // Statistik
                const inProgress = tech.ticket_statistics?.in_progress || 0;
                const completed = tech.ticket_statistics?.completed || 0;

                return `
                <div class="tech-card" onclick="openDetailModal(${tech.id})">
                    <div class="status-badge ${statusClass}">${statusLabel}</div>

                    <div class="tech-avatar" style="background-color: ${bgAvatar}; color: ${textAvatar};">
                        ${initials}
                    </div>
                    
                    <div class="tech-name">${tech.name}</div>
                    <div class="tech-role">${tech.department?.name || 'Teknisi'}</div>
                    
                    <div class="stats-container">
                        <div class="stat-box">
                            <span class="stat-num" style="color: #f59f00;">${inProgress}</span>
                            <span class="stat-label">Proses</span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-num" style="color: #2fb344;">${completed}</span>
                            <span class="stat-label">Selesai</span>
                        </div>
                    </div>
                </div>
            `;
            }).join('');
        }

        function openDetailModal(id) {
            const tech = allTechnicians.find(t => t.id === id);
            if (!tech) return;

            document.getElementById('dName').innerText = tech.name;
            document.getElementById('dDept').innerText = tech.department?.name || 'Umum';

            const hue = (tech.id * 137.508) % 360;
            const dAvatar = document.getElementById('dAvatar');
            dAvatar.innerText = getInitials(tech.name);
            dAvatar.style.backgroundColor = `hsl(${hue}, 70%, 90%)`;
            dAvatar.style.color = `hsl(${hue}, 80%, 30%)`;

            const listContainer = document.getElementById('ticketsList');
            listContainer.innerHTML = `<div class="text-center text-muted py-3 small">Memuat tiket...</div>`;

            loadAssignedTickets(tech, listContainer);

            document.getElementById('detailModal').style.display = 'flex';
        }

        async function loadAssignedTickets(tech, listContainer) {
            if (!listContainer) return;

            if (!tech.assigned_tickets) {
                const response = await fetchWithAuth(`${baseUrl}/api/users/${tech.id}/assigned-tickets`);
                if (!response || !response.ok) {
                    listContainer.innerHTML =
                    `<div class="text-center text-muted py-3 small">Gagal memuat tiket.</div>`;
                    return;
                }

                const data = await response.json();
                tech.assigned_tickets = data.data?.assigned_tickets || [];
            }

            const tickets = tech.assigned_tickets || [];

            if (tickets.length === 0) {
                listContainer.innerHTML = `<div class="text-center text-muted py-3 small">Tidak ada tiket aktif.</div>`;
                return;
            }

            listContainer.innerHTML = tickets.map(assignment => {
                const t = assignment.ticket;
                if (!t) return '';

                let color = '#64748b';
                const s = (t.status?.name || 'open').toLowerCase();
                if (s.includes('progress')) color = '#f59f00';
                else if (s.includes('resolved')) color = '#2fb344';

                return `
                <div class="task-item">
                    <div style="overflow: hidden;">
                        <div style="font-weight: 700; font-size: 0.85rem; color: #333;">${t.ticket_number}</div>
                        <div style="font-size: 0.8rem; color: #666; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${t.subject}</div>
                    </div>
                    <div style="font-size: 0.7rem; font-weight: 700; color: ${color}; text-transform: uppercase;">
                        ${t.status?.name || 'Unknown'}
                    </div>
                </div>
            `;
            }).join('');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').style.display = 'none';
        }

        function getInitials(name) {
            if (!name) return 'UN';
            return name.match(/\b(\w)/g)?.slice(0, 2).join('').toUpperCase() || name.substring(0, 2).toUpperCase();
        }

        window.onclick = function(e) {
            if (e.target === document.getElementById('detailModal')) closeDetailModal();
        }
    </script>
@endsection
