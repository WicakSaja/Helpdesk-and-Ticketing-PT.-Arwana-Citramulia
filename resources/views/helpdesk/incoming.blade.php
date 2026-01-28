@extends('layouts.helpdesk')
@section('title', 'Tiket Masuk')

@section('css')
<style>
    /* HEADER: Flexbox biar Judul di Kiri, Lonceng di Kanan */
    .page-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; /* Vertikal tengah */
        margin-bottom: 30px; 
        padding-right: 20px; 
    }
    
    .page-title { font-size: 24px; font-weight: 700; color: #333; margin-bottom: 5px; }
    .page-subtitle { color: #777; font-size: 13px; margin: 0; }
    
    /* NOTIFIKASI (Kanan) */
    .alert-badge { 
        background: #ffebee; color: #d62828; 
        padding: 10px 20px; border-radius: 8px; 
        font-weight: 600; font-size: 13px; 
        border: 1px solid #ffcdd2; 
        display: flex; align-items: center; gap: 10px; 
        box-shadow: 0 2px 5px rgba(214, 40, 40, 0.1);
    }

    /* TABLE CARD */
    .table-card { 
        background: white; padding: 30px; 
        border-radius: 16px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); 
    }
    
    .custom-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    
    .custom-table th { 
        text-align: left; color: #888; padding: 15px; 
        border-bottom: 1px solid #eee; font-size: 12px; 
        font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
    }
    
    /* Padding diperbesar biar TIDAK BERDEMPET */
    .custom-table td { 
        padding: 25px 15px; /* Atas-Bawah 25px */
        border-bottom: 1px solid #f9f9f9; 
        font-size: 14px; color: #333; vertical-align: middle; 
    }
    .custom-table tr:hover td { background: #fcfcfc; }
    
    .badge-dept { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; display: inline-block; }
    .bg-red { background: #ffebee; color: #d62828; }
    .bg-blue { background: #e3f2fd; color: #1976d2; }
    
    .btn-assign { 
        background: #1976d2; color: white; padding: 10px 20px; 
        border-radius: 8px; font-size: 12px; font-weight: 600; 
        border: none; cursor: pointer; transition: 0.3s; 
        display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-assign:hover { background: #1565c0; box-shadow: 0 4px 10px rgba(25, 118, 210, 0.2); }

    /* MODAL */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 999; }
    .modal-box { background: white; width: 450px; padding: 30px; border-radius: 16px; animation: slideDown 0.3s ease; box-shadow: 0 20px 50px rgba(0,0,0,0.2); }
    @keyframes slideDown { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    
    .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px; }
    .form-select, .form-input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 5px; outline: none; font-size: 14px; }
    .btn-save { background: #d62828; color: white; padding: 12px 25px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; }
    .btn-cancel { background: #f5f5f5; color: #555; padding: 12px 25px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; }
</style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Tiket Masuk</h1>
            <p class="page-subtitle">Antrian tiket baru yang perlu ditugaskan.</p>
        </div>
        
        <div class="alert-badge">
            <i class="fa-solid fa-bell"></i> 
            <span>5 Tiket Perlu Tindakan</span>
        </div>
    </div>

    <div class="table-card">
        <table class="custom-table">
            <thead>
                <tr>
                    <th width="35%">Tiket Info</th>
                    <th width="20%">Lokasi</th>
                    <th width="15%">Kategori</th>
                    <th width="15%">Waktu</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div style="font-weight: 700; font-size: 15px; margin-bottom: 5px;">Mesin Press Bunyi Kasar</div>
                        <div style="font-size: 12px; color: #888;">#TKT-009 • Pelapor: Mandor Line 3</div>
                    </td>
                    <td><span class="badge-dept bg-red">Produksi - Plant 5</span></td>
                    <td>Mechanical</td>
                    <td>10 Menit lalu</td>
                    <td>
                        <button class="btn-assign" onclick="openModal('TKT-009', 'Mesin Press Bunyi Kasar')">
                            <i class="fa-solid fa-user-plus"></i> Pilih Teknisi
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="font-weight: 700; font-size: 15px; margin-bottom: 5px;">PC Admin Gudang Error</div>
                        <div style="font-size: 12px; color: #888;">#TKT-008 • Pelapor: Staff Logistik</div>
                    </td>
                    <td><span class="badge-dept bg-blue">Gudang Finish Good</span></td>
                    <td>IT Support</td>
                    <td>30 Menit lalu</td>
                    <td>
                        <button class="btn-assign" onclick="openModal('TKT-008', 'PC Admin Gudang Error')">
                            <i class="fa-solid fa-user-plus"></i> Pilih Teknisi
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="modal-overlay" id="assignModal">
        <div class="modal-box">
            <h3 style="margin-bottom:20px; font-size: 18px; font-weight: 700;">Assign Teknisi</h3>
            
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
        Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Tiket telah ditugaskan ke teknisi.', timer: 2000, showConfirmButton: false, confirmButtonColor: '#d62828' });
    }
</script>
@endsection