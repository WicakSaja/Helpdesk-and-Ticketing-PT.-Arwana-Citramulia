@extends('layouts.helpdesk')
@section('title', 'Semua Data Tiket')

@section('css')
<style>
    /* Header Container */
    .page-header { 
        display: flex; 
        align-items: center; 
        gap: 30px; 
        margin-bottom: 30px; 
        padding-right: 140px; 
    }
    
    .page-title { font-size: 24px; font-weight: 700; color: #333; white-space: nowrap; }
    
    /* SEARCH BOX */
    .search-box { position: relative; width: 100%; max-width: 400px; }
    .search-input { 
        width: 100%; padding: 12px 15px 12px 45px; 
        border: 1px solid #ddd; border-radius: 8px; 
        outline: none; font-size: 14px; background: white; 
        transition: 0.3s;
    }
    .search-input:focus { border-color: #d62828; box-shadow: 0 0 0 3px rgba(214, 40, 40, 0.1); }
    .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999; font-size: 16px; }

    /* TABLE STYLE */
    .table-container { 
        background: white; padding: 30px; 
        border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); 
        overflow-x: auto; 
    }
    
    .full-table { width: 100%; border-collapse: collapse; white-space: nowrap; }
    
    .full-table th { 
        text-align: left; color: #888; font-size: 13px; 
        padding: 15px; border-bottom: 2px solid #f0f0f0; 
        text-transform: uppercase; font-weight: 700; 
    }
    
    .full-table td { 
        padding: 18px 15px; 
        border-bottom: 1px solid #f9f9f9; 
        font-size: 14px; color: #333; 
    }
    .full-table tr:hover td { background-color: #fcfcfc; }

    /* Badges */
    .badge { padding: 6px 12px; border-radius: 50px; font-size: 11px; font-weight: 600; display: inline-block; }
    .bdg-done { background: #e8f5e9; color: #2e7d32; }
    .bdg-progress { background: #fff3e0; color: #e65100; }
    .bdg-open { background: #e3f2fd; color: #1976d2; }
    
    .btn-view { 
        background: #fff0f0; color: #d62828; border: none; 
        width: 35px; height: 35px; border-radius: 8px; 
        cursor: pointer; display: inline-flex; align-items: center; justify-content: center; 
        transition: 0.3s; font-size: 14px;
    }
    .btn-view:hover { background: #d62828; color: white; }

    /* PAGINATION STYLE */
    .pagination { display: flex; justify-content: flex-end; gap: 8px; margin-top: 25px; }
    .page-btn { 
        width: 35px; height: 35px; 
        display: flex; align-items: center; justify-content: center; 
        border: 1px solid #eee; border-radius: 8px; 
        color: #555; font-size: 13px; cursor: pointer; transition: 0.3s; background: white; 
    }
    .page-btn.active { background: #d62828; color: white; border-color: #d62828; }
    .page-btn:hover:not(.active) { background: #f9f9f9; }

    /* MODAL DETAIL STYLE */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 999; }
    .modal-box { background: white; width: 500px; padding: 30px; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: slideDown 0.3s ease; }
    @keyframes slideDown { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
    .close-btn { background: none; border: none; font-size: 20px; cursor: pointer; color: #999; }
    
    .timeline-item { display: flex; gap: 15px; margin-bottom: 15px; }
    .timeline-dot { width: 10px; height: 10px; background: #d62828; border-radius: 50%; margin-top: 6px; flex-shrink: 0; }
    .timeline-content { font-size: 14px; color: #555; }
    .timeline-date { font-size: 11px; color: #999; display: block; margin-bottom: 3px; font-weight: 600; }
</style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Semua Data</h1>
        
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Cari ID, Subjek, atau Teknisi..." onkeyup="filterTable()">
        </div>
    </div>
    
    <div class="table-container">
        <table class="full-table" id="ticketTable">
            <thead>
                <tr>
                    <th>ID Tiket</th>
                    <th>Tanggal</th>
                    <th>Subjek</th>
                    <th>Dept</th>
                    <th>Teknisi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody"></tbody>
        </table>

        <div id="noDataMessage" style="text-align:center; padding:30px; color:#999; display:none;">
            <i class="fa-solid fa-folder-open" style="font-size:30px; margin-bottom:10px;"></i>
            <p>Data tidak ditemukan.</p>
        </div>

        <div class="pagination" id="paginationControls"></div>
    </div>

    <div class="modal-overlay" id="detailModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 style="font-size: 18px; font-weight: 700;">Detail Riwayat Tiket</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            
            <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <h4 id="mSubject" style="margin-bottom: 5px; color:#333; font-size: 16px;">Subjek Tiket</h4>
                <div style="display:flex; justify-content:space-between; font-size:13px; color:#666;">
                    <span id="mId" style="font-weight: 600;">#TKT-000</span>
                    <span id="mDept" style="background: #eee; padding: 2px 8px; border-radius: 4px;">Departemen</span>
                </div>
            </div>

            <h5 style="margin-bottom:15px; color:#d62828; font-size: 14px;">Kronologi Pengerjaan</h5>
            <div id="mTimeline">
                </div>

            <div style="text-align: right; margin-top: 25px;">
                <button onclick="closeModal()" style="background:#eee; color:#333; border:none; padding:10px 25px; border-radius:8px; cursor:pointer; font-weight:600;">Tutup</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // 1. DATA DUMMY LENGKAP
    const tickets = [
        { id: '#TKT-001', date: '20 Jan 2026', subject: 'Ganti Kabel LAN Ruang Meeting', dept: 'HRD', tech: 'Andi Pratama', status: 'Closed', class: 'bdg-done' },
        { id: '#TKT-002', date: '21 Jan 2026', subject: 'Mesin Glaze Line 4 Error', dept: 'Produksi', tech: 'Budi Santoso', status: 'In Progress', class: 'bdg-progress' },
        { id: '#TKT-003', date: '22 Jan 2026', subject: 'Printer Gudang Macet', dept: 'Logistik', tech: 'Citra', status: 'Closed', class: 'bdg-done' },
        { id: '#TKT-004', date: '23 Jan 2026', subject: 'Wifi Ruko Depan Mati', dept: 'Umum', tech: 'Andi Pratama', status: 'Resolved', class: 'bdg-done' },
        { id: '#TKT-005', date: '24 Jan 2026', subject: 'Sensor Conveyor Patah', dept: 'Produksi', tech: '-', status: 'Open', class: 'bdg-open' },
        { id: '#TKT-006', date: '25 Jan 2026', subject: 'Lupa Password Email', dept: 'Marketing', tech: 'Andi Pratama', status: 'Closed', class: 'bdg-done' },
        { id: '#TKT-007', date: '25 Jan 2026', subject: 'AC Server Panas', dept: 'IT', tech: 'Citra', status: 'In Progress', class: 'bdg-progress' },
        { id: '#TKT-008', date: '26 Jan 2026', subject: 'Minta Akses ERP', dept: 'Finance', tech: 'Andi Pratama', status: 'Open', class: 'bdg-open' },
        { id: '#TKT-009', date: '26 Jan 2026', subject: 'Forklift Mogok', dept: 'Gudang', tech: 'Budi Santoso', status: 'Resolved', class: 'bdg-done' },
        { id: '#TKT-010', date: '27 Jan 2026', subject: 'CCTV Pos Satpam Mati', dept: 'Security', tech: 'Citra', status: 'Closed', class: 'bdg-done' },
        { id: '#TKT-011', date: '27 Jan 2026', subject: 'Update Windows 11', dept: 'HRD', tech: 'Andi Pratama', status: 'Open', class: 'bdg-open' },
        { id: '#TKT-012', date: '28 Jan 2026', subject: 'Mesin Press Bunyi', dept: 'Produksi', tech: 'Budi Santoso', status: 'In Progress', class: 'bdg-progress' },
        { id: '#TKT-013', date: '28 Jan 2026', subject: 'Mouse Rusak', dept: 'Marketing', tech: '-', status: 'Open', class: 'bdg-open' },
        { id: '#TKT-014', date: '29 Jan 2026', subject: 'Lampu Gudang Putus', dept: 'Logistik', tech: 'Citra', status: 'Resolved', class: 'bdg-done' },
        { id: '#TKT-015', date: '29 Jan 2026', subject: 'Permintaan Mousepad', dept: 'Umum', tech: '-', status: 'Open', class: 'bdg-open' }
    ];

    // Variabel untuk menyimpan data yang sedang ditampilkan (bisa data full atau hasil search)
    let filteredTickets = [...tickets]; // Default: semua data
    const rowsPerPage = 5;
    let currentPage = 1;

    // 2. FUNGSI RENDER TABEL (Menerima parameter data array)
    function renderTable(page) {
        const tbody = document.getElementById('tableBody');
        const noData = document.getElementById('noDataMessage');
        tbody.innerHTML = '';

        // Cek jika data kosong
        if (filteredTickets.length === 0) {
            noData.style.display = 'block';
            document.getElementById('paginationControls').innerHTML = ''; // Hapus pagination
            return;
        } else {
            noData.style.display = 'none';
        }

        // Logic Pagination pada Filtered Data
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedItems = filteredTickets.slice(start, end);

        paginatedItems.forEach(t => {
            const row = `<tr>
                <td><b>${t.id}</b></td>
                <td>${t.date}</td>
                <td>${t.subject}</td>
                <td>${t.dept}</td>
                <td>${t.tech}</td>
                <td><span class="badge ${t.class}">${t.status}</span></td>
                <td>
                    <button class="btn-view" onclick="openDetail('${t.id}', '${t.subject}', '${t.dept}')">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </td>
            </tr>`;
            tbody.innerHTML += row;
        });

        renderPagination();
    }

    // 3. FUNGSI SEARCH (FILTER LOGIC)
    function filterTable() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        
        // Filter array tickets berdasarkan input user
        filteredTickets = tickets.filter(t => 
            t.id.toLowerCase().includes(input) || 
            t.subject.toLowerCase().includes(input) || 
            t.tech.toLowerCase().includes(input) ||
            t.dept.toLowerCase().includes(input)
        );

        currentPage = 1; // Reset ke halaman 1 setiap kali search berubah
        renderTable(currentPage);
    }

    // 4. FUNGSI RENDER PAGINATION
    function renderPagination() {
        const controls = document.getElementById('paginationControls');
        controls.innerHTML = '';
        const pageCount = Math.ceil(filteredTickets.length / rowsPerPage);

        // Jangan tampilkan pagination jika halaman cuma 1
        if (pageCount <= 1) return;

        // Tombol Prev
        const prevBtn = document.createElement('div');
        prevBtn.className = 'page-btn';
        prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
        prevBtn.onclick = function() { if(currentPage > 1) { currentPage--; renderTable(currentPage); } };
        controls.appendChild(prevBtn);

        // Angka Halaman
        for (let i = 1; i <= pageCount; i++) {
            const btn = document.createElement('div');
            btn.className = `page-btn ${i === currentPage ? 'active' : ''}`;
            btn.innerText = i;
            btn.onclick = function() {
                currentPage = i;
                renderTable(currentPage);
            };
            controls.appendChild(btn);
        }

        // Tombol Next
        const nextBtn = document.createElement('div');
        nextBtn.className = 'page-btn';
        nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
        nextBtn.onclick = function() { if(currentPage < pageCount) { currentPage++; renderTable(currentPage); } };
        controls.appendChild(nextBtn);
    }

    // 5. LOGIKA MODAL DETAIL
    function openDetail(id, subject, dept) {
        document.getElementById('mId').innerText = id;
        document.getElementById('mSubject').innerText = subject;
        document.getElementById('mDept').innerText = dept;

        let timelineHTML = `
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <span class="timeline-date">Baru Saja</span>
                    Status tiket sedang dicek oleh Admin Helpdesk.
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot" style="background:#eee;"></div>
                <div class="timeline-content">
                    <span class="timeline-date">Kemarin</span>
                    Tiket dibuat oleh User dari Departemen ${dept}.
                </div>
            </div>
        `;
        document.getElementById('mTimeline').innerHTML = timelineHTML;
        document.getElementById('detailModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('detailModal').style.display = 'none';
    }

    // Init Table saat pertama kali load
    renderTable(currentPage);
</script>
@endsection