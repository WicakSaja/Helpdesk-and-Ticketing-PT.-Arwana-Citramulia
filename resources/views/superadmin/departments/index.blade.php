@extends('layouts.superadmin')
@section('title', 'Manajemen Departemen')

@section('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* CSS CONSISTENT WITH USERS */
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    .page-title { font-size: 24px; font-weight: 700; color: #333; margin: 0; }
    .btn-add { background: #1565c0; color: white; padding: 12px 25px; border-radius: 10px; font-weight: 600; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s; }
    .btn-add:hover { background: #0d47a1; }
    
    .table-container { background: white; padding: 30px; border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); }
    .dept-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .dept-table th { text-align: left; color: #888; padding: 15px; border-bottom: 2px solid #f0f0f0; font-size: 12px; font-weight: 700; text-transform: uppercase; }
    .dept-table td { padding: 15px; border-bottom: 1px solid #f9f9f9; font-size: 14px; color: #333; vertical-align: middle; transition: background 0.3s; }
    .dept-table tr:hover td { background: #fcfcfc; }

    .btn-icon { width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: 0.3s; margin-right: 5px; }
    .btn-edit { background: #fff3e0; color: #f57c00; } .btn-edit:hover { background: #f57c00; color: white; }
    .btn-del { background: #ffebee; color: #d62828; } .btn-del:hover { background: #d62828; color: white; }

    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: none; align-items: center; justify-content: center; z-index: 2000; backdrop-filter: blur(4px); }
    .modal-box { background: white; width: 450px; padding: 30px; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.25); animation: slideIn 0.3s ease; }
    @keyframes slideIn { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    
    .form-group { margin-bottom: 15px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: #444; margin-bottom: 5px; }
    .form-input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; }

    /* Animasi Hapus */
    .fade-out { animation: fadeOut 0.5s forwards; }
    @keyframes fadeOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(-20px); } }
</style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Departemen</h1>
        <button class="btn-add" onclick="openModal()"><i class="fa-solid fa-plus"></i> Tambah Baru</button>
    </div>

    <div class="table-container">
        <table class="dept-table">
            <thead>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th>Nama Departemen</th>
                    <th>Kode</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr id="row-1">
                    <td>1</td>
                    <td><strong>Information Technology (IT)</strong></td>
                    <td><span style="background:#e3f2fd; color:#1565c0; padding:4px 8px; border-radius:4px; font-size:12px; font-weight:600;">DEPT-IT</span></td>
                    <td style="text-align: right;">
                        <button class="btn-icon btn-edit" onclick="editDept(1, 'Information Technology (IT)', 'DEPT-IT')"><i class="fa-solid fa-pen"></i></button>
                        <button class="btn-icon btn-del" onclick="deleteDept(1, 'IT Support')"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
                <tr id="row-2">
                    <td>2</td>
                    <td><strong>Human Resource (HRD)</strong></td>
                    <td><span style="background:#f3e5f5; color:#7b1fa2; padding:4px 8px; border-radius:4px; font-size:12px; font-weight:600;">DEPT-HR</span></td>
                    <td style="text-align: right;">
                        <button class="btn-icon btn-edit" onclick="editDept(2, 'Human Resource (HRD)', 'DEPT-HR')"><i class="fa-solid fa-pen"></i></button>
                        <button class="btn-icon btn-del" onclick="deleteDept(2, 'HRD')"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
                <tr id="row-3">
                    <td>3</td>
                    <td><strong>Produksi</strong></td>
                    <td><span style="background:#e8f5e9; color:#2e7d32; padding:4px 8px; border-radius:4px; font-size:12px; font-weight:600;">DEPT-PRO</span></td>
                    <td style="text-align: right;">
                        <button class="btn-icon btn-edit" onclick="editDept(3, 'Produksi', 'DEPT-PRO')"><i class="fa-solid fa-pen"></i></button>
                        <button class="btn-icon btn-del" onclick="deleteDept(3, 'Produksi')"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="deptModal" class="modal-overlay">
        <div class="modal-box">
            <h3 style="margin-bottom: 20px;" id="modalTitle">Tambah Departemen</h3>
            <form id="deptForm">
                <div class="form-group">
                    <label class="form-label">Nama Departemen</label>
                    <input type="text" id="deptName" class="form-input" placeholder="Contoh: Finance" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kode Singkatan</label>
                    <input type="text" id="deptCode" class="form-input" placeholder="Contoh: DEPT-FIN" required>
                </div>
                
                <div style="text-align: right; margin-top: 25px;">
                    <button type="button" onclick="closeModal()" style="background:white; border:1px solid #ddd; padding:10px 20px; border-radius:8px; cursor:pointer; margin-right: 10px;">Batal</button>
                    <button type="submit" style="background:#1565c0; color:white; border:none; padding:10px 25px; border-radius:8px; cursor:pointer; font-weight:600;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function openModal() {
        document.getElementById('modalTitle').innerText = "Tambah Departemen";
        document.getElementById('deptName').value = "";
        document.getElementById('deptCode').value = "";
        document.getElementById('deptModal').style.display = 'flex';
    }

    function editDept(id, name, code) {
        document.getElementById('modalTitle').innerText = "Edit Departemen";
        document.getElementById('deptName').value = name;
        document.getElementById('deptCode').value = code;
        document.getElementById('deptModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('deptModal').style.display = 'none';
    }

    // --- FUNGSI HAPUS YANG LEBIH KEREN ---
    function deleteDept(id, name) {
        Swal.fire({
            title: 'Hapus ' + name + '?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d62828',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // 1. Ambil Elemen Baris
                const row = document.getElementById('row-' + id);
                
                // 2. Efek Fade Out
                row.classList.add('fade-out');

                // 3. Hapus setelah animasi selesai
                setTimeout(() => {
                    row.remove();
                    Swal.fire({
                        title: 'Terhapus!',
                        text: 'Departemen ' + name + ' berhasil dihapus.',
                        icon: 'success',
                        confirmButtonColor: '#1565c0'
                    });
                }, 500);
            }
        })
    }

    document.getElementById('deptForm').addEventListener('submit', function(e) {
        e.preventDefault();
        closeModal();
        Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Data departemen tersimpan!', confirmButtonColor: '#1565c0' });
    });

    window.onclick = function(event) {
        if (event.target.classList.contains('modal-overlay')) { closeModal(); }
    }
</script>
@endsection