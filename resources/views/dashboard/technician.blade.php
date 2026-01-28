@extends('layouts.technician')
@section('title', 'Dashboard Teknisi')

@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* CSS Dashboard Lama (Header & Stats) */
        .header-welcome { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .user-info h2 { font-size: 24px; color: #333; margin-bottom: 5px; }
        .user-info p { color: #888; font-size: 14px; }

        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 25px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03); border-bottom: 4px solid transparent; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-info h3 { font-size: 28px; color: #333; font-weight: 700; margin-bottom: 5px; }
        .stat-info p { color: #777; font-size: 13px; font-weight: 500; }
        .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .card-orange { border-color: #f57c00; } .card-orange .stat-icon { background: #fff3e0; color: #f57c00; }
        .card-green { border-color: #388e3c; } .card-green .stat-icon { background: #e8f5e9; color: #388e3c; }
        .card-blue { border-color: #1976d2; } .card-blue .stat-icon { background: #e3f2fd; color: #1976d2; }

        /* Task List Styles */
        .section-title { font-size: 18px; font-weight: 700; color: #333; margin-bottom: 20px; }
        .task-card { background: white; padding: 25px; border-radius: 16px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03); border-left: 6px solid #ccc; transition: 0.3s; }
        .task-card:hover { transform: translateX(5px); }
        .cat-mech { border-left-color: #d62828; } .cat-it { border-left-color: #1976d2; }
        .task-content h4 { font-size: 16px; font-weight: 600; color: #333; margin-bottom: 8px; }
        .task-meta { font-size: 13px; color: #777; display: flex; gap: 15px; align-items: center; }
        .task-meta i { color: #bbb; margin-right: 5px; }
        .badge-cat { padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; margin-left: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-mech { background: #ffebee; color: #d62828; } .badge-it { background: #e3f2fd; color: #1976d2; }

        .btn-update { background: #2e7d32; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; display: flex; align-items: center; gap: 8px; transition: 0.3s; text-decoration: none;}
        .btn-update:hover { background: #1b5e20; }

        /* Modal Styles (Copy dari Tasks) */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: none; align-items: center; justify-content: center; z-index: 2000; backdrop-filter: blur(4px); }
        .modal-box { background: white; width: 600px; max-width: 90%; padding: 30px; border-radius: 12px; box-shadow: 0 25px 50px rgba(0,0,0,0.25); animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .modal-title { font-size: 20px; font-weight: 700; color: #333; }
        .btn-close { background: none; border: none; font-size: 24px; color: #999; cursor: pointer; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #444; margin-bottom: 8px; }
        .form-input, .form-select, .form-textarea { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; margin-bottom: 15px; }
        .form-textarea { height: 100px; resize: vertical; }
        .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: #2e7d32; box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1); }
    </style>
@endsection

@section('content')
    <div class="header-welcome">
        <div class="user-info">
            <h2>Semangat Pagi, Teknisi! 🛠️</h2>
            <p>Prioritas: Menjaga Operasional Mesin & IT.</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card card-orange">
            <div class="stat-info"><p>Tugas Pending</p><h3>2</h3></div>
            <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
        </div>
        <div class="stat-card card-green">
            <div class="stat-info"><p>Selesai Hari Ini</p><h3>3</h3></div>
            <div class="stat-icon"><i class="fa-solid fa-check-circle"></i></div>
        </div>
        <div class="stat-card card-blue">
            <div class="stat-info"><p>Total Ditangani</p><h3>45</h3></div>
            <div class="stat-icon"><i class="fa-solid fa-star"></i></div>
        </div>
    </div>

    <h3 class="section-title">Daftar Tugas Anda</h3>

    <div class="task-card cat-mech">
        <div class="task-content">
            <h4>Ganti Bearing Motor Conveyor Line 2 <span class="badge-cat badge-mech">Mechanical</span></h4>
            <div class="task-meta">
                <span><i class="fa-solid fa-user"></i> Spv. Produksi</span>
                <span><i class="fa-solid fa-triangle-exclamation"></i> Mesin Bunyi Kasar</span>
                <span><i class="fa-regular fa-clock"></i> 30 Menit lalu</span>
            </div>
        </div>
        <button class="btn-update" onclick="openUpdate('#TKT-002', 'Ganti Bearing Motor')">
            Update Status <i class="fa-solid fa-arrow-right"></i>
        </button>
    </div>

    <div class="task-card cat-it">
        <div class="task-content">
            <h4>Install Ulang PC Admin Gudang <span class="badge-cat badge-it">IT Support</span></h4>
            <div class="task-meta">
                <span><i class="fa-solid fa-user"></i> Staff Gudang</span>
                <span><i class="fa-solid fa-laptop-code"></i> Windows Error</span>
                <span><i class="fa-regular fa-clock"></i> 2 Jam lalu</span>
            </div>
        </div>
        <button class="btn-update" onclick="openUpdate('#TKT-005', 'Install Ulang PC')">
            Update Status <i class="fa-solid fa-arrow-right"></i>
        </button>
    </div>

    <div id="modalUpdate" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Update Pengerjaan</h3>
                <button class="btn-close" onclick="closeModal('modalUpdate')">&times;</button>
            </div>
            
            <form id="updateForm">
                <div style="background: #e8f5e9; padding: 10px 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c8e6c9;">
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
                    <textarea class="form-textarea" placeholder="Contoh: Sudah dilakukan penggantian bearing, mesin normal kembali." required></textarea>
                </div>

                <div style="text-align: right; margin-top: 10px;">
                    <button type="button" onclick="closeModal('modalUpdate')" style="background:white; border:1px solid #ddd; padding:10px 20px; border-radius:8px; cursor:pointer; margin-right: 10px;">Batal</button>
                    <button type="submit" style="background:#2e7d32; color:white; border:none; padding:10px 25px; border-radius:8px; cursor:pointer; font-weight:600;">Simpan Laporan</button>
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
            didOpen: () => { Swal.showLoading(); },
            willClose: () => { clearInterval(timerInterval); }
        }).then((result) => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Status tiket berhasil diperbarui.',
                confirmButtonColor: '#2e7d32',
            });
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