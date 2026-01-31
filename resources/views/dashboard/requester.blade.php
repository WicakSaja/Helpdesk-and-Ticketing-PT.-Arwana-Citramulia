<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Helpdesk Arwana</title>

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

        /* --- 2. SIDEBAR (KIRI) --- */
        .sidebar {
            width: 260px;
            background: white;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #eee;
            position: fixed;
            /* Sidebar diam di tempat */
            height: 100vh;
        }

        .sidebar-logo {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        /* Gambar Logo Arwana */
        .img-logo {
            width: 160px;
            /* Ukuran Logo */
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
            /* Merah muda halus saat hover */
            color: #d62828;
        }

        .menu-item.active {
            background-color: #d62828;
            /* Merah Arwana */
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

        /* --- 3. KONTEN UTAMA (KANAN) --- */
        .main-content {
            flex: 1;
            margin-left: 260px;
            /* Geser konten agar tidak tertutup sidebar */
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

        /* Avatar Bulat */
        .user-avatar {
            width: 45px;
            height: 45px;
            background: #d62828;
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
            grid-template-columns: repeat(3, 1fr);
            /* 3 Kolom */
            gap: 25px;
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
            /* Garis bawah warna */
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-info h3 {
            font-size: 32px;
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
            font-size: 22px;
        }

        /* Warna-warni Kartu */
        .card-blue {
            border-color: #1976d2;
        }

        .card-blue .stat-icon {
            background: #e3f2fd;
            color: #1976d2;
        }

        .card-orange {
            border-color: #f57c00;
        }

        .card-orange .stat-icon {
            background: #fff3e0;
            color: #f57c00;
        }

        .card-green {
            border-color: #388e3c;
        }

        .card-green .stat-icon {
            background: #e8f5e9;
            color: #388e3c;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('images/logo_arwana.png') }}" alt="Arwana Ceramics" class="img-logo">
        </div>

        <div class="menu">
            <a href="{{ route('dashboard.requester') }}" class="menu-item">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
            <a href="{{ route('tickets.create') }}" class="menu-item">
                <i class="fa-solid fa-plus-circle"></i> Buat Tiket Baru
            </a>
            <a href="{{ route('tickets.index') }}" class="menu-item">
                <i class="fa-solid fa-list-check"></i> Riwayat Tiket
            </a>
            <a href="{{ route('profile') }}" class="menu-item {{ Request::routeIs('profile') ? 'active' : '' }}">
                <i class="fa-solid fa-user"></i> Profil Saya
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
                <h2>Halo, {{ Auth::user()->name ?? 'Fadhli (Guest)' }}! 👋</h2>
                <p>Selamat datang di Arwana Helpdesk System</p>
            </div>
            <div class="user-avatar">
                {{ substr(Auth::user()->name ?? 'G', 0, 1) }}
            </div>
        </div>

        <div class="stats-grid">

            <div class="stat-card card-blue">
                <div class="stat-info">
                    <p>Total Tiket Saya</p>
                    <h3>12</h3>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-ticket"></i>
                </div>
            </div>

            <div class="stat-card card-orange">
                <div class="stat-info">
                    <p>Sedang Diproses</p>
                    <h3>5</h3>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-spinner"></i>
                </div>
            </div>

            <div class="stat-card card-green">
                <div class="stat-info">
                    <p>Tiket Selesai</p>
                    <h3>7</h3>
                </div>
                <div class="stat-icon">
                    <i class="fa-solid fa-check-double"></i>
                </div>
            </div>

        </div>

        <div style="background: white; padding: 40px; border-radius: 16px; text-align: center; color: #999;">
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png"
                alt="Empty" style="width: 200px; opacity: 0.7;">
            <p>Belum ada aktivitas terbaru hari ini.</p>
            <br>
            <a href="{{ route('tickets.create') }}" style="color: #d62828; font-weight: 600;">Buat Tiket Sekarang
                &rarr;</a>
        </div>

    </div>

    <script>
        const API_URL = 'http://127.0.0.1:8000';
    </script>
    <script src="{{ asset('js/auth-token-manager.js') }}"></script>
    <script src="{{ asset('js/logout-handler.js') }}"></script>
    <script src="{{ asset('js/role-protection.js') }}"></script>
    <script src="{{ asset('js/page-protection.js') }}"></script>
    <script>
        // Protect this page - require authentication
        document.addEventListener('DOMContentLoaded', function() {
            requireRequesterRole();
        });
    </script>
</body>

</html>
