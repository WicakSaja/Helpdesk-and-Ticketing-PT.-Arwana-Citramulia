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
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 999; }
    .modal-box { background: white; width: 450px; padding: 30px; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: slideDown 0.3s ease; }
    @keyframes slideDown { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .btn-close-modal { background: #eee; border: none; padding: 10px 25px; border-radius: 8px; cursor: pointer; margin-top: 20px; font-weight: 600; color: #555; transition: 0.3s; }
    .btn-close-modal:hover { background: #ddd; }
    
    .detail-header { text-align: center; margin-bottom: 20px; }
    .detail-avatar { width: 80px; height: 80px; background: #eee; border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: bold; }
    .detail-tasks { background: #f9f9f9; padding: 15px; border-radius: 10px; }
    .detail-item { display: flex; justify-content: space-between; font-size: 13px; padding: 8px 0; border-bottom: 1px solid #eee; }
    .detail-item:last-child { border-bottom: none; }
</style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Tim Teknisi Plant 5</h1>
        <p class="page-subtitle">Monitoring status personel teknisi yang siap bertugas.</p>
    </div>

    <div class="tech-grid">
        <div class="tech-card border-ready" onclick="openDetailModal('Budi Santoso', 'Mekanik', 'Available', 'e8f5e9', '2e7d32')">
            <div class="tech-avatar" style="background: #e8f5e9; color: #2e7d32;">BS</div>
            <div class="tech-name">Budi Santoso</div>
            <div class="tech-spec">Mekanik (Mesin)</div>
            <div class="tech-status st-ready"><i class="fa-solid fa-circle" style="font-size:10px;"></i> Available</div>
            <div class="task-count">
                <span>Sedang Dikerjakan: <b>0</b></span>
                <span>Selesai: <b>15</b></span>
            </div>
        </div>

        <div class="tech-card border-busy" onclick="openDetailModal('Citra', 'Elektrikal', 'Sibuk', 'ffebee', 'd62828')">
            <div class="tech-avatar" style="background: #ffebee; color: #d62828;">C</div>
            <div class="tech-name">Citra</div>
            <div class="tech-spec">Elektrikal & PLC</div>
            <div class="tech-status st-busy"><i class="fa-solid fa-circle" style="font-size:10px;"></i> Sibuk</div>
            <div class="task-count">
                <span>Sedang Dikerjakan: <b>2</b></span>
                <span>Selesai: <b>12</b></span>
            </div>
        </div>

        <div class="tech-card border-ready" onclick="openDetailModal('Andi Pratama', 'IT Support', 'Available', 'e3f2fd', '1976d2')">
            <div class="tech-avatar" style="background: #e3f2fd; color: #1976d2;">AP</div>
            <div class="tech-name">Andi Pratama</div>
            <div class="tech-spec">IT Support</div>
            <div class="tech-status st-ready"><i class="fa-solid fa-circle" style="font-size:10px;"></i> Available</div>
            <div class="task-count">
                <span>Sedang Dikerjakan: <b>0</b></span>
                <span>Selesai: <b>20</b></span>
            </div>
        </div>

        <div class="tech-card border-ready" onclick="openDetailModal('Rudi Hartono', 'Mekanik', 'Available', 'fff3e0', 'e65100')">
            <div class="tech-avatar" style="background: #fff3e0; color: #e65100;">RH</div>
            <div class="tech-name">Rudi Hartono</div>
            <div class="tech-spec">Mekanik (Umum)</div>
            <div class="tech-status st-ready"><i class="fa-solid fa-circle" style="font-size:10px;"></i> Available</div>
            <div class="task-count">
                <span>Sedang Dikerjakan: <b>0</b></span>
                <span>Selesai: <b>8</b></span>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="detailModal">
        <div class="modal-box">
            <div class="detail-header">
                <div class="detail-avatar" id="dAvatar">X</div>
                <h3 style="margin-bottom: 5px; font-size: 20px;" id="dName">Nama</h3>
                <span style="background:#f4f6f9; padding:5px 12px; border-radius:20px; font-size:12px; color:#555;" id="dSpec">Spesialis</span>
                <div style="margin-top:10px; font-weight:600; color:#2e7d32;" id="dStatus">Status</div>
            </div>

            <h4 style="font-size:14px; margin-bottom:10px; color:#555;">Riwayat Pekerjaan Terbaru</h4>
            <div class="detail-tasks">
                <div class="detail-item">
                    <span>#TKT-005: Perbaikan Sensor</span>
                    <span style="color:#2e7d32; font-weight:600;">Selesai</span>
                </div>
                <div class="detail-item">
                    <span>#TKT-008: Install Ulang PC</span>
                    <span style="color:#f57c00; font-weight:600;">Proses</span>
                </div>
                <div class="detail-item">
                    <span>#TKT-001: Cek Kabel LAN</span>
                    <span style="color:#2e7d32; font-weight:600;">Selesai</span>
                </div>
            </div>

            <div style="text-align: center;">
                <button class="btn-close-modal" onclick="closeDetailModal()">Tutup Detail</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function openDetailModal(name, spec, status, colorBg, colorText) {
        document.getElementById('detailModal').style.display = 'flex';
        document.getElementById('dName').innerText = name;
        document.getElementById('dSpec').innerText = spec;
        document.getElementById('dStatus').innerText = status;
        
        if(status === 'Sibuk') {
            document.getElementById('dStatus').style.color = '#d62828';
        } else {
            document.getElementById('dStatus').style.color = '#2e7d32';
        }

        const avatar = document.getElementById('dAvatar');
        avatar.innerText = name.match(/\b(\w)/g).join('').substring(0,2);
        avatar.style.background = '#' + colorBg;
        avatar.style.color = '#' + colorText;
    }

    function closeDetailModal() { 
        document.getElementById('detailModal').style.display = 'none'; 
    }
</script>
@endsection