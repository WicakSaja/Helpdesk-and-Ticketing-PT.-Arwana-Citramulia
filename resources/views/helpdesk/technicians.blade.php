@extends('layouts.helpdesk')
@section('title', 'Kelola Teknisi')

@section('css')
<style>
    /* 1. HEADER (Jarak ke bawah ditambah biar tidak mepet card) */
    .page-header { 
        margin-bottom: 50px; /* Diperbesar dari 35px */
        padding-right: 140px; 
    }
    .page-title { font-size: 26px; font-weight: 700; color: #333; margin-bottom: 5px; }
    .page-subtitle { color: #777; font-size: 15px; }
    
    /* Grid 3 Kolom */
    .tech-grid { 
        display: grid; 
        grid-template-columns: repeat(3, 1fr); 
        gap: 30px; 
    }
    
    /* 2. CARD STYLE (Padding dikurangi agar lebih compact tapi tetap rapi) */
    .tech-card { 
        background: white; 
        padding: 25px 20px; /* Dikurangi dari 30px jadi 25px */
        border-radius: 16px; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.03); 
        text-align: center; 
        border-top: 5px solid #eee; 
        transition: 0.3s; 
        cursor: pointer; 
    }
    .tech-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); }

    .border-ready { border-top-color: #2e7d32; }
    .border-busy { border-top-color: #d62828; }

    /* 3. AVATAR (Diperbesar Signifikan) */
    .tech-avatar { 
        width: 90px; height: 90px; /* Diperbesar dari 70px */
        background: #eee; border-radius: 50%; 
        margin: 0 auto 15px; 
        display: flex; align-items: center; justify-content: center; 
        font-size: 32px; font-weight: 700; /* Font juga diperbesar */
    }
    
    .tech-name { font-weight: 700; color: #333; font-size: 18px; margin-bottom: 5px; }
    .tech-spec { 
        font-size: 13px; color: #777; 
        background: #f4f6f9; padding: 5px 15px; 
        border-radius: 20px; display: inline-block; 
        margin-bottom: 15px; 
    }
    
    .tech-status { 
        font-size: 14px; font-weight: 600; 
        display: flex; align-items: center; justify-content: center; gap: 8px; 
    }
    .st-ready { color: #2e7d32; }
    .st-busy { color: #d62828; }
    
    .task-count { 
        margin-top: 20px; padding-top: 15px; 
        border-top: 1px solid #f0f0f0; 
        display: flex; justify-content: space-between; 
        font-size: 13px; color: #555; 
    }

    /* Modal styles */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 999; overflow-y: auto; }
    .modal-box { background: white; width: 550px; padding: 30px; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: slideDown 0.3s ease; margin: 20px 0; }
    @keyframes slideDown { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .btn-close-modal { background: #eee; border: none; padding: 10px 25px; border-radius: 8px; cursor: pointer; margin-top: 20px; font-weight: 600; color: #555; transition: 0.3s; }
    .btn-close-modal:hover { background: #ddd; }
    
    .detail-header { text-align: center; margin-bottom: 20px; }
    .detail-avatar { width: 80px; height: 80px; background: #eee; border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: bold; }
    .detail-tasks { background: #f9f9f9; padding: 15px; border-radius: 10px; max-height: 300px; overflow-y: auto; }
    .detail-item { display: flex; justify-content: space-between; font-size: 13px; padding: 10px; border-bottom: 1px solid #eee; }
    .detail-item:last-child { border-bottom: none; }
    .ticket-link { color: #1976d2; font-weight: 600; text-decoration: none; }
    .ticket-link:hover { text-decoration: underline; }
    
    /* Loading */
    .loading { text-align: center; padding: 40px; color: #777; }
    .loading-spinner { display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #1976d2; border-radius: 50%; animation: spin 1s linear infinite; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    
    /* Empty state */
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
    .empty-state img { width: 150px; opacity: 0.5; margin-bottom: 20px; }
</style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Tim Teknisi Plant 5</h1>
        <p class="page-subtitle">Monitoring status personel teknisi yang siap bertugas.</p>
    </div>

    <div class="tech-grid" id="technicianGrid">
        <div class="loading">
            <div class="loading-spinner"></div>
            <p style="margin-top: 20px;">Memuat data teknisi...</p>
        </div>
    </div>

    <div class="modal-overlay" id="detailModal">
        <div class="modal-box">
            <div class="detail-header">
                <div class="detail-avatar" id="dAvatar">X</div>
                <h3 style="margin-bottom: 5px; font-size: 20px;" id="dName">Nama</h3>
                <p style="margin: 5px 0; color: #777; font-size: 13px;">
                    <span id="dEmail"></span><br>
                    <span id="dPhone"></span>
                </p>
                <span style="background:#f4f6f9; padding:5px 12px; border-radius:20px; font-size:12px; color:#555;" id="dDept">Departemen</span>
            </div>

            <div style="background: #f9f9f9; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                    <div style="text-align: center;">
                        <div style="font-size: 20px; font-weight: 700; color: #f57c00;" id="dInProgress">0</div>
                        <div style="font-size: 12px; color: #777; margin-top: 5px;">Sedang Dikerjakan</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 20px; font-weight: 700; color: #2e7d32;" id="dCompleted">0</div>
                        <div style="font-size: 12px; color: #777; margin-top: 5px;">Selesai</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 20px; font-weight: 700; color: #1976d2;" id="dTotal">0</div>
                        <div style="font-size: 12px; color: #777; margin-top: 5px;">Total Tiket</div>
                    </div>
                </div>
            </div>

            <h4 style="font-size:14px; margin-bottom:10px; color:#555;">Tiket yang Ditugaskan</h4>
            <div class="detail-tasks" id="ticketsList">
                <div style="text-align: center; color: #999; padding: 20px;">Tidak ada tiket</div>
            </div>

            <div style="text-align: center;">
                <button class="btn-close-modal" onclick="closeDetailModal()">Tutup Detail</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let allTechnicians = [];

// Fetch technicians on page load
document.addEventListener('DOMContentLoaded', async function() {
    await fetchTechnicians();
});

async function fetchTechnicians() {
    try {
        const token = TokenManager.getToken();
        if (!token) {
            showError('Token tidak ditemukan. Silakan login kembali.');
            return;
        }

        const response = await fetch(`${API_URL}/api/users/by-role/technician`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        allTechnicians = data.data || [];

        renderTechnicians(allTechnicians);
    } catch (error) {
        console.error('Error fetching technicians:', error);
        showError('Gagal memuat data teknisi. Silakan refresh halaman.');
    }
}

function renderTechnicians(technicians) {
    const grid = document.getElementById('technicianGrid');

    if (technicians.length === 0) {
        grid.innerHTML = `
            <div style="grid-column: 1 / -1;">
                <div class="empty-state">
                    <p>Tidak ada data teknisi tersedia</p>
                </div>
            </div>
        `;
        return;
    }

    grid.innerHTML = technicians.map(tech => {
        const avatarColor = getColorForTechnician(tech.id);
        const isActive = !!tech.is_active;
        const statusText = isActive ? 'Active' : 'Nonactive';
        const statusClass = isActive ? 'st-ready' : 'st-busy';
        const borderClass = isActive ? 'border-ready' : 'border-busy';

        return `
            <div class="tech-card ${borderClass}" onclick="openDetailModal(${tech.id})">
                <div class="tech-avatar" style="background: ${avatarColor.bg}; color: ${avatarColor.text};">
                    ${getInitials(tech.name)}
                </div>
                <div class="tech-name">${tech.name}</div>
                <div class="tech-spec">${tech.department?.name || 'N/A'}</div>
                <div class="tech-status ${statusClass}">
                    <i class="fa-solid fa-circle" style="font-size:10px;"></i> ${statusText}
                </div>
                <div class="task-count">
                    <span>Sedang Dikerjakan: <b>${tech.ticket_statistics.in_progress}</b></span>
                    <span>Selesai: <b>${tech.ticket_statistics.completed}</b></span>
                </div>
            </div>
        `;
    }).join('');
}

function openDetailModal(technicianId) {
    const tech = allTechnicians.find(t => t.id === technicianId);
    if (!tech) return;

    const avatarColor = getColorForTechnician(tech.id);

    document.getElementById('dAvatar').innerText = getInitials(tech.name);
    document.getElementById('dAvatar').style.background = avatarColor.bg;
    document.getElementById('dAvatar').style.color = avatarColor.text;
    document.getElementById('dName').innerText = tech.name;
    document.getElementById('dEmail').innerText = tech.email;
    document.getElementById('dPhone').innerText = tech.phone;
    document.getElementById('dDept').innerText = tech.department?.name || 'N/A';
    document.getElementById('dInProgress').innerText = tech.ticket_statistics.in_progress;
    document.getElementById('dCompleted').innerText = tech.ticket_statistics.completed;
    document.getElementById('dTotal').innerText = tech.ticket_statistics.total;

    // Render tickets
    const ticketsHtml = renderTickets(tech.assigned_tickets);
    document.getElementById('ticketsList').innerHTML = ticketsHtml;

    document.getElementById('detailModal').style.display = 'flex';
}

function renderTickets(tickets) {
    if (!tickets || tickets.length === 0) {
        return `<div style="text-align: center; color: #999; padding: 20px;">Tidak ada tiket yang ditugaskan</div>`;
    }

    return tickets.map(assignment => {
        const ticket = assignment.ticket;
        const status = ticket.status?.name || 'Unknown';
        const statusColor = status === 'Closed' ? '#2e7d32' : status === 'IN_PROGRESS' ? '#f57c00' : '#d62828';
        
        return `
            <div class="detail-item">
                <div>
                    <div style="font-weight: 600; color: #333;">${ticket.ticket_number}</div>
                    <div style="color: #777; margin-top: 3px;">${ticket.subject}</div>
                </div>
                <div style="color: ${statusColor}; font-weight: 600; text-align: right;">
                    ${status}
                </div>
            </div>
        `;
    }).join('');
}

function closeDetailModal() {
    document.getElementById('detailModal').style.display = 'none';
}

function getInitials(name) {
    return name.match(/\b(\w)/g)?.join('').substring(0, 2).toUpperCase() || 'U';
}

function getColorForTechnician(id) {
    const colors = [
        { bg: '#e8f5e9', text: '#2e7d32' }, // Green
        { bg: '#ffebee', text: '#d62828' }, // Red
        { bg: '#e3f2fd', text: '#1976d2' }, // Blue
        { bg: '#fff3e0', text: '#e65100' }, // Orange
        { bg: '#f3e5f5', text: '#7b1fa2' }, // Purple
        { bg: '#e0f2f1', text: '#00897b' }, // Teal
    ];
    return colors[(id - 1) % colors.length];
}

function showError(message) {
    const grid = document.getElementById('technicianGrid');
    grid.innerHTML = `
        <div style="grid-column: 1 / -1;">
            <div class="empty-state">
                <p style="color: #d62828; margin-bottom: 20px;">⚠️ ${message}</p>
                <button onclick="location.reload()" style="background: #1976d2; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                    Coba Lagi
                </button>
            </div>
        </div>
    `;
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('detailModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>
@endsection