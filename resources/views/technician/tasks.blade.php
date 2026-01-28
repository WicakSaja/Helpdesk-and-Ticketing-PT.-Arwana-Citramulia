@extends('layouts.technician')
@section('title', 'Tugas Saya')

@section('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    .page-title { font-size: 24px; font-weight: 700; color: #333; margin: 0; }
    
    /* Task Card */
    .task-card { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); margin-bottom: 20px; border-left: 5px solid #ccc; transition: 0.3s; position: relative; }
    .task-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
    
    .task-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
    .task-id { font-weight: 700; font-size: 14px; }
    .task-time { font-size: 12px; color: #888; }
    
    .task-body h3 { font-size: 18px; margin-bottom: 5px; color: #333; font-weight: 700; }
    .task-body p { font-size: 14px; color: #666; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    
    .task-meta { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
    .meta-tag { background: #f4f6f9; padding: 5px 12px; border-radius: 6px; font-size: 12px; color: #555; display: flex; align-items: center; gap: 6px; font-weight: 500; }
    
    /* Tombol Aksi */
    .action-group { display: flex; gap: 10px; border-top: 1px solid #eee; padding-top: 15px; }
    .btn-action { flex: 1; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 13px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.3s; }
    
    .btn-detail { background: white; border: 1px solid #ddd; color: #555; }
    .btn-detail:hover { background: #f8f9fa; border-color: #ccc; color: #333; }
    
    .btn-update { background: #2e7d32; color: white; }
    .btn-update:hover { background: #1b5e20; }

    /* Warna Kategori */
    .bd-mech { border-left-color: #d62828; } .txt-mech { color: #d62828; }
    .bd-it { border-left-color: #1976d2; } .txt-it { color: #1976d2; }

    /* --- MODAL STYLE --- */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: none; align-items: center; justify-content: center; z-index: 2000; backdrop-filter: blur(4px); }
    .modal-box { background: white; width: 600px; max-width: 90%; padding: 30px; border-radius: 12px; box-shadow: 0 25px 50px rgba(0,0,0,0.25); animation: slideIn 0.3s ease; }
    @keyframes slideIn { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
    .modal-title { font-size: 20px; font-weight: 700; color: #333; }
    .btn-close { background: none; border: none; font-size: 24px; color: #999; cursor: pointer; }
    .btn-close:hover { color: #d62828; }

    /* Form di Modal */
    .form-label { display: block; font-size: 13px; font-weight: 600; color: #444; margin-bottom: 8px; }
    .form-input, .form-select, .form-textarea { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; margin-bottom: 15px; }
    .form-textarea { height: 100px; resize: vertical; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: #2e7d32; box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1); }
</style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Daftar Tugas (3)</h1>
    </div>

    <div class="task-card bd-mech">
        <div class="task-header">
            <span class="task-id txt-mech">#TKT-002</span>
            <span class="task-time"><i class="fa-regular fa-clock"></i> 30 Menit lalu</span>
        </div>
        <div class="task-body">
            <h3>Ganti Bearing Motor Conveyor Line 2</h3>
            <p>Supervisor melaporkan bunyi kasar pada motor utama conveyor finish line. Indikasi bearing pecah.</p>
            <div class="task-meta">
                <div class="meta-tag"><i class="fa-solid fa-building"></i> Produksi</div>
                <div class="meta-tag"><i class="fa-solid fa-wrench"></i> Mechanical</div>
            </div>
        </div>
        <div class="action-group">
            <button class="btn-action btn-detail" onclick="openDetail('#TKT-002', 'Ganti Bearing Motor', 'Mesin bunyi kasar di Line 2.', 'Produksi', 'Spv. Andi')">
                <i class="fa-regular fa-eye"></i> Detail
            </button>
            <button class="btn-action btn-update" onclick="openUpdate('#TKT-002', 'Ganti Bearing Motor')">
                <i class="fa-solid fa-pen-to-square"></i> Update Status
            </button>
        </div>
    </div>

    <div class="task-card bd-it">
        <div class="task-header">
            <span class="task-id txt-it">#TKT-005</span>
            <span class="task-time">2 Jam lalu</span>
        </div>
        <div class="task-body">
            <h3>Install Ulang PC Admin Gudang</h3>
            <p>PC terkena virus dan lambat. User meminta install ulang Windows 10 dan Office lengkap.</p>
            <div class="task-meta">
                <div class="meta-tag"><i class="fa-solid fa-warehouse"></i> Gudang</div>
                <div class="meta-tag"><i class="fa-solid fa-desktop"></i> IT Support</div>
            </div>
        </div>
        <div class="action-group">
            <button class="btn-action btn-detail" onclick="openDetail('#TKT-005', 'Install Ulang PC', 'PC Lambat kena virus.', 'Gudang', 'Staff Budi')">
                <i class="fa-regular fa-eye"></i> Detail
            </button>
            <button class="btn-action btn-update" onclick="openUpdate('#TKT-005', 'Install Ulang PC')">
                <i class="fa-solid fa-pen-to-square"></i> Update Status
            </button>
        </div>
    </div>

    <div id="modalDetail" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Detail Tiket</h3>
                <button class="btn-close" onclick="closeModal('modalDetail')">&times;</button>
            </div>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <table style="width: 100%; font-size: 14px;">
                    <tr><td style="color:#666; padding: 5px 0; width: 120px;">No. Tiket</td><td style="font-weight:700;" id="dId">...</td></tr>
                    <tr><td style="color:#666; padding: 5px 0;">Pelapor</td><td style="font-weight:600;" id="dUser">...</td></tr>
                    <tr><td style="color:#666; padding: 5px 0;">Departemen</td><td style="font-weight:600;" id="dDept">...</td></tr>
                </table>
            </div>
            <h4 style="font-size: 16px; margin-bottom: 10px; color: #333;">Deskripsi Masalah</h4>
            <p id="dDesc" style="font-size: 14px; color: #555; line-height: 1.6; margin-bottom: 25px;">...</p>
            <div style="text-align: right;">
                <button onclick="closeModal('modalDetail')" style="background:#eee; border:none; padding:10px 25px; border-radius:8px; cursor:pointer;">Tutup</button>
            </div>
        </div>
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
    function openDetail(id, subject, desc, dept, user) {
        document.getElementById('dId').innerText = id;
        document.getElementById('dUser').innerText = user;
        document.getElementById('dDept').innerText = dept;
        document.getElementById('dDesc').innerText = desc;
        document.getElementById('modalDetail').style.display = 'flex';
    }

    function openUpdate(id, subject) {
        document.getElementById('uSubject').innerText = id + " - " + subject;
        document.getElementById('modalUpdate').style.display = 'flex';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    document.getElementById('updateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        closeModal('modalUpdate');
        Swal.fire({
            title: 'Menyimpan...',
            timer: 1000,
            timerProgressBar: true,
            didOpen: () => { Swal.showLoading(); }
        }).then(() => {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Status tiket berhasil diperbarui.', confirmButtonColor: '#2e7d32' });
        });
    });

    window.onclick = function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.style.display = 'none';
        }
    }
</script>
@endsection