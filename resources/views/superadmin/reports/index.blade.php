@extends('layouts.superadmin')
@section('title', 'Laporan Global')

@section('css')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* HEADER & FILTER */
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    .page-title { font-size: 24px; font-weight: 700; color: #333; margin: 0; }

    .filter-box { 
        background: white; padding: 20px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); 
        display: flex; gap: 15px; align-items: end; margin-bottom: 30px;
    }
    .form-group { flex: 1; }
    .form-label { font-size: 13px; font-weight: 600; color: #555; margin-bottom: 8px; display: block; }
    .form-input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; }
    
    .btn-filter { background: #1565c0; color: white; border: none; padding: 10px 25px; border-radius: 8px; font-weight: 600; cursor: pointer; height: 42px; transition: 0.3s; }
    .btn-filter:hover { background: #0d47a1; }
    
    .btn-export { background: #2e7d32; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s; }
    .btn-export:hover { background: #1b5e20; }

    /* STATS CARDS */
    .report-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
    .report-card { background: white; padding: 20px; border-radius: 12px; border-bottom: 4px solid #ddd; box-shadow: 0 5px 15px rgba(0,0,0,0.03); }
    .rc-title { font-size: 12px; color: #888; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .rc-value { font-size: 28px; font-weight: 700; color: #333; margin-top: 5px; }
    .rc-desc { font-size: 11px; color: #2e7d32; margin-top: 5px; font-weight: 500; }
    .rc-desc.down { color: #d62828; }

    /* CHARTS & TABLE LAYOUT */
    .content-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 30px; }
    .chart-container { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); }
    
    .table-wrapper { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); }
    .report-table { width: 100%; border-collapse: collapse; }
    .report-table th { text-align: left; padding: 12px; border-bottom: 2px solid #eee; font-size: 12px; color: #888; font-weight: 700; text-transform: uppercase; }
    .report-table td { padding: 12px; border-bottom: 1px solid #f9f9f9; font-size: 13px; color: #333; }
    .status-done { background: #e8f5e9; color: #2e7d32; padding: 3px 10px; border-radius: 20px; font-weight: 600; font-size: 11px; }
    .status-wait { background: #fff3e0; color: #f57c00; padding: 3px 10px; border-radius: 20px; font-weight: 600; font-size: 11px; }

    /* ANIMATION */
    .fade-in { animation: fadeIn 0.5s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Laporan & Statistik</h1>
        <button class="btn-export" onclick="exportData()">
            <i class="fa-solid fa-file-excel"></i> Export Excel
        </button>
    </div>

    <div class="filter-box fade-in">
        <div class="form-group">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" class="form-input" value="{{ date('Y-m-01') }}">
        </div>
        <div class="form-group">
            <label class="form-label">Tanggal Akhir</label>
            <input type="date" class="form-input" value="{{ date('Y-m-d') }}">
        </div>
        <div class="form-group">
            <label class="form-label">Departemen</label>
            <select class="form-input">
                <option value="">Semua Departemen</option>
                <option value="IT">IT Support</option>
                <option value="HR">HRD</option>
                <option value="PRO">Produksi</option>
            </select>
        </div>
        <button class="btn-filter" onclick="applyFilter()">Terapkan Filter</button>
    </div>

    <div class="report-grid fade-in">
        <div class="report-card" style="border-color: #1565c0;">
            <div class="rc-title">Total Tiket Masuk</div>
            <div class="rc-value">150</div>
            <div class="rc-desc"><i class="fa-solid fa-arrow-up"></i> 12% dari bulan lalu</div>
        </div>
        <div class="report-card" style="border-color: #2e7d32;">
            <div class="rc-title">Tiket Selesai (Resolved)</div>
            <div class="rc-value">138</div>
            <div class="rc-desc"><i class="fa-solid fa-check"></i> 92% Completion Rate</div>
        </div>
        <div class="report-card" style="border-color: #f57c00;">
            <div class="rc-title">Rata-rata Respon</div>
            <div class="rc-value">15 <span style="font-size:14px; font-weight:500;">Menit</span></div>
            <div class="rc-desc"><i class="fa-solid fa-bolt"></i> Sangat Cepat</div>
        </div>
        <div class="report-card" style="border-color: #d62828;">
            <div class="rc-title">Tiket Pending / Overdue</div>
            <div class="rc-value">12</div>
            <div class="rc-desc down"><i class="fa-solid fa-circle-exclamation"></i> Perlu Penanganan</div>
        </div>
    </div>

    <div class="content-grid fade-in">
        
        <div class="chart-container">
            <h3 style="font-size:16px; font-weight:700; margin-bottom:20px; color:#333;">Tren Kategori Masalah</h3>
            <canvas id="categoryChart"></canvas>
        </div>

        <div class="table-wrapper">
            <h3 style="font-size:16px; font-weight:700; margin-bottom:20px; color:#333;">Performa Teknisi (Top 5)</h3>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Nama Teknisi</th>
                        <th>Tiket Selesai</th>
                        <th>Rating User</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-weight:600;">Teknisi Andi</td>
                        <td>45</td>
                        <td style="color:#f57c00;"><i class="fa-solid fa-star"></i> 4.9</td>
                    </tr>
                    <tr>
                        <td style="font-weight:600;">Teknisi Budi</td>
                        <td>42</td>
                        <td style="color:#f57c00;"><i class="fa-solid fa-star"></i> 4.8</td>
                    </tr>
                    <tr>
                        <td style="font-weight:600;">Teknisi Citra</td>
                        <td>38</td>
                        <td style="color:#f57c00;"><i class="fa-solid fa-star"></i> 4.7</td>
                    </tr>
                    <tr>
                        <td style="font-weight:600;">Teknisi Dedi</td>
                        <td>30</td>
                        <td style="color:#f57c00;"><i class="fa-solid fa-star"></i> 4.5</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // 1. CHART JS CONFIGURATION
    const ctx = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut', // Bisa diganti 'bar' atau 'pie'
        data: {
            labels: ['Hardware', 'Software / Aplikasi', 'Jaringan / Network', 'Lainnya'],
            datasets: [{
                data: [40, 35, 15, 10],
                backgroundColor: [
                    '#d62828', // Merah (Hardware)
                    '#1565c0', // Biru (Software)
                    '#f57c00', // Orange (Network)
                    '#757575'  // Abu (Lainnya)
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // 2. SIMULASI FILTER
    function applyFilter() {
        let btn = document.querySelector('.btn-filter');
        let originalText = btn.innerText;
        btn.innerText = 'Memuat Data...';
        btn.disabled = true;

        setTimeout(() => {
            btn.innerText = originalText;
            btn.disabled = false;
            Swal.fire({
                icon: 'success',
                title: 'Data Diperbarui',
                text: 'Laporan telah disesuaikan dengan filter tanggal.',
                timer: 1500,
                showConfirmButton: false
            });
        }, 800);
    }

    // 3. SIMULASI EXPORT
    function exportData() {
        Swal.fire({
            title: 'Sedang Mengunduh...',
            text: 'Menyiapkan file Laporan_Bulanan.xlsx',
            timer: 2000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        }).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Download Selesai!',
                text: 'File laporan berhasil disimpan di perangkat Anda.',
                confirmButtonColor: '#2e7d32'
            });
        });
    }
</script>
@endsection