<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Supervisor - Arwana Plant 5</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- 1. GLOBAL STYLE --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4f6f9;
            display: flex;
            min-height: 100vh;
        }

        a {
            text-decoration: none;
        }

        /* --- 2. SIDEBAR --- */
        .sidebar {
            width: 260px;
            background: white;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #eee;
            position: fixed;
            height: 100vh;
        }

        .sidebar-logo {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        .img-logo {
            width: 160px;
            height: auto;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #666;
            border-radius: 10px;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
            transition: 0.3s;
        }

        .menu-item i {
            width: 30px;
            font-size: 16px;
        }

        .menu-item:hover {
            background-color: #ffebee;
            color: #d62828;
        }

        .menu-item.active {
            background-color: #d62828;
            color: white;
            box-shadow: 0 4px 10px rgba(214, 40, 40, 0.3);
        }

        .mt-auto {
            margin-top: auto;
        }

        .btn-logout {
            width: 100%;
            padding: 12px;
            background: #fff0f0;
            color: #d62828;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            text-align: left;
            padding-left: 20px;
        }

        .btn-logout:hover {
            background: #d62828;
            color: white;
        }

        /* --- 3. KONTEN UTAMA --- */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 30px 40px;
        }

        .header-welcome {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .user-info h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 5px;
        }

        .user-info p {
            color: #888;
            font-size: 14px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: #7b1fa2;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
        }

        /* --- 4. KARTU STATISTIK --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
            border-bottom: 4px solid transparent;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-info h3 {
            font-size: 28px;
            color: #333;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-info p {
            color: #777;
            font-size: 13px;
            font-weight: 500;
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

        /* Warna Kartu */
        .card-blue {
            border-color: #1976d2;
        }

        .card-blue .stat-icon {
            background: #e3f2fd;
            color: #1976d2;
        }

        .card-green {
            border-color: #388e3c;
        }

        .card-green .stat-icon {
            background: #e8f5e9;
            color: #388e3c;
        }

        .card-red {
            border-color: #d32f2f;
        }

        .card-red .stat-icon {
            background: #ffebee;
            color: #d32f2f;
        }

        .card-orange {
            border-color: #f57c00;
        }

        .card-orange .stat-icon {
            background: #fff3e0;
            color: #f57c00;
        }

        /* --- 5. CHART / PROGRESS CONTAINER --- */
        .chart-container {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 25px;
        }

        .progress-group {
            margin-bottom: 20px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }

        .progress-bar {
            height: 10px;
            background: #f0f0f0;
            border-radius: 20px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 20px;
            transition: width 1s ease-in-out;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('images/logo_arwana.png') }}" alt="Arwana Ceramics" class="img-logo">
        </div>

        <div class="menu">
            <a href="#" class="menu-item active">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
            <a href="#" class="menu-item">
                <i class="fa-solid fa-industry"></i> Laporan Produksi
            </a>
            <a href="#" class="menu-item">
                <i class="fa-solid fa-users-gear"></i> Tim Maintenance
            </a>
            <a href="#" class="menu-item">
                <i class="fa-solid fa-file-contract"></i> SLA & Sparepart
            </a>
        </div>

        <div class="mt-auto">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="header-welcome">
            <div class="user-info">
                <h2>Selamat Siang, Supervisor! 🏭</h2>
                <p>Monitoring performa Maintenance & IT Plant 5.</p>
            </div>
            <div class="user-avatar">S</div>
        </div>

        <div class="stats-grid">
            <div class="stat-card card-blue">
                <div class="stat-info">
                    <p>Total Laporan (YTD)</p>
                    <h3>2,450</h3>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
            </div>

            <div class="stat-card card-green">
                <div class="stat-info">
                    <p>Machine Uptime</p>
                    <h3>98.5%</h3>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-gauge-high"></i>
                </div>
            </div>

            <div class="stat-card card-red">
                <div class="stat-info">
                    <p>Total Downtime</p>
                    <h3>45 Jam</h3>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>

            <div class="stat-card card-orange">
                <div class="stat-info">
                    <p>Pending Sparepart</p>
                    <h3>5 Item</h3>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
        </div>

        <div class="chart-container">
            <h3 class="section-title">Distribusi Kategori Kerusakan</h3>

            <div class="progress-group">
                <div class="progress-label">
                    <span>Mechanical (Mesin Press, Conveyor, Kiln)</span>
                    <span style="color: #d62828;">50%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 50%; background: #d62828;"></div>
                </div>
            </div>

            <div class="progress-group">
                <div class="progress-label">
                    <span>Electrical & Sensor (PLC, Inverter)</span>
                    <span style="color: #f57c00;">30%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 30%; background: #f57c00;"></div>
                </div>
            </div>

            <div class="progress-group">
                <div class="progress-label">
                    <span>IT Infrastructure (PC, Network, CCTV)</span>
                    <span style="color: #1976d2;">20%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 20%; background: #1976d2;"></div>
                </div>
            </div>
        </div>

    </div>

    {{-- Auth Scripts --}}
    <script>
        const API_URL = '{{ config('app.api_url') }}';
    </script>
    <script src="{{ asset('js/auth-token-manager.js') }}"></script>
    <script src="{{ asset('js/logout-handler.js') }}"></script>
    <script src="{{ asset('js/role-protection.js') }}"></script>
    <script src="{{ asset('js/page-protection.js') }}"></script>
    <script>
        // Protect supervisor pages
        document.addEventListener('DOMContentLoaded', function() {
            requireSupervisorRole();
        });
    </script>
</body>

</html>
