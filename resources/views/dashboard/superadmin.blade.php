@extends('layouts.superadmin')
@section('title', 'Dashboard Admin')

@section('css')
    {{-- Load Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Banner & Stats (Tetap) */
        .welcome-banner {
            background: linear-gradient(135deg, #1565c0, #0d47a1);
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(21, 101, 192, 0.2);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .welcome-banner h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .welcome-banner p {
            margin: 5px 0 0;
            opacity: 0.9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-info h3 {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .stat-info p {
            font-size: 12px;
            color: #777;
            margin: 0;
        }

        /* WARNA ICON */
        .bg-blue {
            background: #e3f2fd;
            color: #1565c0;
        }

        .bg-green {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .bg-orange {
            background: #fff3e0;
            color: #f57c00;
        }

        .bg-red {
            background: #ffebee;
            color: #d62828;
        }

        /* LAYOUT BARU: CHART SECTION */
        .charts-wrapper {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
        }

        /* LAYOUT BARU: RECENT TABLE */
        .recent-wrapper {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
        }

        .simple-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .simple-table th {
            text-align: left;
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .simple-table td {
            padding: 12px 10px;
            font-size: 13px;
            color: #444;
            border-bottom: 1px solid #f9f9f9;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge.open {
            background: #fff3e0;
            color: #f57c00;
        }

        .badge.closed {
            background: #e8f5e9;
            color: #2e7d32;
        }
    </style>
@endsection

@section('content')
    <div class="welcome-banner">
        <div>
            <h2>Dashboard Overview</h2>
            <p>Selamat Datang, {{ auth()->user() ? auth()->user()->name : 'Administrator' }}! 👋</p>
        </div>
        <div style="background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 8px; font-size: 13px;">
            📅 {{ date('d F Y') }}
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-blue"><i class="fa-solid fa-users"></i></div>
            <div class="stat-info">
                <h3>120</h3>
                <p>Total User</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-green"><i class="fa-solid fa-screwdriver-wrench"></i></div>
            <div class="stat-info">
                <h3>8</h3>
                <p>Teknisi Aktif</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-orange"><i class="fa-solid fa-ticket"></i></div>
            <div class="stat-info">
                <h3>450</h3>
                <p>Tiket Bulan Ini</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-red"><i class="fa-solid fa-building"></i></div>
            <div class="stat-info">
                <h3>12</h3>
                <p>Departemen</p>
            </div>
        </div>
    </div>

    <div class="charts-wrapper">
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">📈 Tren Tiket (7 Hari Terakhir)</div>
            </div>
            <canvas id="trendChart" style="height: 300px; width: 100%;"></canvas>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">📊 Kategori Masalah</div>
            </div>
            <canvas id="categoryChart" style="height: 250px; width: 100%;"></canvas>
        </div>
    </div>

    <div class="recent-wrapper">
        <div class="chart-header">
            <div class="chart-title">🕒 5 Tiket Terbaru</div>
            <a href="#" style="font-size: 12px; color: #1565c0; font-weight: 600;">Lihat Semua</a>
        </div>
        <table class="simple-table">
            <thead>
                <tr>
                    <th>ID Tiket</th>
                    <th>Judul Masalah</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#T-2023001</td>
                    <td>Printer Macet di HRD</td>
                    <td>Budi Santoso</td>
                    <td><span class="badge open">Process</span></td>
                    <td>Hari ini, 10:00</td>
                </tr>
                <tr>
                    <td>#T-2023002</td>
                    <td>Wifi Lantai 2 Lemot</td>
                    <td>Siti Aminah</td>
                    <td><span class="badge closed">Selesai</span></td>
                    <td>Kemarin, 14:30</td>
                </tr>
                <tr>
                    <td>#T-2023003</td>
                    <td>Install Ulang Windows</td>
                    <td>Joko</td>
                    <td><span class="badge open">Pending</span></td>
                    <td>Kemarin, 09:15</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <script>
        // 1. CHART TREN TIKET (Line Chart)
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [{
                        label: 'Tiket Masuk',
                        data: [12, 19, 3, 5, 10, 3, 7],
                        borderColor: '#1565c0',
                        backgroundColor: 'rgba(21, 101, 192, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Tiket Selesai',
                        data: [10, 15, 5, 4, 8, 3, 6],
                        borderColor: '#2e7d32',
                        backgroundColor: 'transparent',
                        tension: 0.4,
                        borderDash: [5, 5]
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // 2. CHART KATEGORI (Doughnut - Pindahan dari Report)
        const ctxCat = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: ['Hardware', 'Software', 'Network', 'Lainnya'],
                datasets: [{
                    data: [40, 35, 15, 10],
                    backgroundColor: ['#d62828', '#1565c0', '#f57c00', '#757575'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endsection
