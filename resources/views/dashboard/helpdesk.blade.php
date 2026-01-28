@extends('layouts.helpdesk')

@section('title', 'Dashboard - Helpdesk Arwana')

@section('css')
<style>
    /* Header Flex: Judul Kiri, Notif Kanan */
    .header-welcome { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 60px; padding-right: 140px; }
    .user-info h2 { font-size: 26px; color: #333; margin-bottom: 8px; font-weight: 700; }
    .user-info p { color: #777; font-size: 15px; }

    /* Stats Grid */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; margin-bottom: 50px; }
    .stat-card { background: white; padding: 30px 25px; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03); border-bottom: 4px solid transparent; transition: transform 0.3s; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
    .stat-info h3 { font-size: 32px; color: #333; font-weight: 700; margin-bottom: 5px; line-height: 1; }
    .stat-info p { color: #888; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
    .stat-icon { width: 55px; height: 55px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; }

    /* Warna Kartu */
    .card-red { border-color: #d32f2f; } .card-red .stat-icon { background: #ffebee; color: #d32f2f; }
    .card-blue { border-color: #1976d2; } .card-blue .stat-icon { background: #e3f2fd; color: #1976d2; }
    .card-purple { border-color: #7b1fa2; } .card-purple .stat-icon { background: #f3e5f5; color: #7b1fa2; }
    .card-orange { border-color: #f57c00; } .card-orange .stat-icon { background: #fff3e0; color: #f57c00; }

    /* Tabel Dashboard */
    .table-container { background: white; padding: 35px; border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); }
    .section-title { font-size: 20px; font-weight: 700; color: #333; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
    .urgent-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .urgent-table th { text-align: left; color: #999; font-size: 12px; font-weight: 700; padding: 15px; border-bottom: 1px solid #eee; text-transform: uppercase; letter-spacing: 0.8px; }
    .urgent-table td { padding: 20px 15px; border-bottom: 1px solid #f9f9f9; font-size: 14px; color: #333; vertical-align: middle; }
    .urgent-table tr:hover td { background-color: #fcfcfc; }
    .badge-dept { padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 600; display: inline-block; text-transform: uppercase; }
    .dept-finance { background: #fff3e0; color: #e65100; }
    .dept-marketing { background: #e3f2fd; color: #1565c0; }
    .dept-hrd { background: #f3e5f5; color: #7b1fa2; }
    .btn-assign { background: #1976d2; color: white; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; transition: 0.3s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-assign:hover { background: #1565c0; box-shadow: 0 4px 10px rgba(25, 118, 210, 0.2); }

    /* Modal Styles */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 999; }
    .modal-box { background: white; width: 450px; padding: 30px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); animation: slideDown 0.3s ease; }
    @keyframes slideDown { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px; }
    .form-select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-size: 14px; }
    .btn-save { background: #d62828; color: white; padding: 12px 25px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; }
    .btn-cancel { background: #f5f5f5; color: #555; padding: 12px 25px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; }
</style>
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
            <div class="stat-info"><p>Belum Di-Assign</p><h3>5</h3></div>
            <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
        </div>
        <div class="stat-card card-blue">
            <div class="stat-info"><p>Tiket Bulan Ini</p><h3>145</h3></div>
            <div class="stat-icon"><i class="fa-solid fa-globe"></i></div>
        </div>
        <div class="stat-card card-purple">
            <div class="stat-info"><p>Teknisi Ready</p><h3>4</h3></div>
            <div class="stat-icon"><i class="fa-solid fa-headset"></i></div>
        </div>
        <div class="stat-card card-orange">
            <div class="stat-info"><p>Open Tickets</p><h3>12</h3></div>
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
                    <td><div style="font-weight:600;">Server Ruko Down (Urgent)</div></td>
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
                    <td><div style="font-weight:600;">Minta Akses SAP</div></td>
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

    <div class="modal-overlay" id="assignModal">
        <div class="modal-box">
            <h3 style="margin-bottom:20px; font-size:18px; font-weight:700;">Assign Teknisi</h3>
            
            <div style="margin-bottom:20px; background:#fff5f5; padding:15px; border-radius:10px; border: 1px solid #ffcdd2;">
                <div style="font-size: 12px; color: #d62828; font-weight: 600;" id="modalTicketId">#ID</div>
                <div style="font-weight: 700; font-size: 15px; color: #333; margin-top: 3px;" id="modalTicketSubject">Subject</div>
            </div>

            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px; color: #555;">Pilih Teknisi:</label>
                <select class="form-select">
                    <option value="">-- Pilih Personil Tersedia --</option>
                    <option>👨‍🔧 Budi Santoso (Mekanik) - Ready</option>
                    <option>👨‍💻 Andi Pratama (IT) - Ready</option>
                    <option>⚡ Citra (Elektrikal) - Sibuk</option>
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
    function closeModal() { modal.style.display = 'none'; }
    function simpanAssignment() {
        closeModal();
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Tiket telah ditugaskan.', timer: 2000, showConfirmButton: false, confirmButtonColor: '#d62828' });
    }
</script>
@endsection