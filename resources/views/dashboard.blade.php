<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Helpdesk Arwana</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
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

        /* Sidebar Sederhana */
        .sidebar {
            width: 250px;
            background-color: #ffffff;
            color: #333;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #d62828;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .menu-item {
            padding: 12px 15px;
            color: #555;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
            display: block;
            transition: 0.3s;
        }

        .menu-item:hover,
        .menu-item.active {
            background-color: #ffeaea;
            color: #d62828;
            font-weight: 600;
        }

        /* Logout Button di bawah */
        .mt-auto {
            margin-top: auto;
        }

        .btn-logout {
            width: 100%;
            padding: 12px;
            background-color: #d62828;
            /* Merah Arwana */
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-logout:hover {
            background-color: #b51f1f;
        }

        /* Konten Utama */
        .main-content {
            flex: 1;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .welcome-text h2 {
            color: #333;
        }

        .welcome-text p {
            color: #777;
            font-size: 14px;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .card h3 {
            font-size: 16px;
            color: #777;
            margin-bottom: 10px;
        }

        .card .number {
            font-size: 32px;
            font-weight: 700;
            color: #333;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="logo">
            <span>ARWANA</span> HELPDESK
        </div>

        <a href="#" class="menu-item active">Dashboard</a>
        <a href="#" class="menu-item">Buat Tiket Baru</a>
        <a href="#" class="menu-item">Riwayat Tiket</a>
        <a href="#" class="menu-item">Profil Saya</a>

        <div class="mt-auto">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <div class="welcome-text">
                <h2>Halo, {{ Auth::user()->name }}! 👋</h2>
                <p>Departemen: {{ Auth::user()->department->name ?? 'Belum ada departemen' }}</p>
            </div>
            <div class="profile-pic">
                <div
                    style="width: 45px; height: 45px; background: #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            </div>
        </div>

        <div class="card-container">
            <div class="card">
                <h3>Total Tiket Saya</h3>
                <div class="number">0</div>
            </div>
            <div class="card">
                <h3>Sedang Diproses</h3>
                <div class="number">0</div>
            </div>
            <div class="card">
                <h3>Selesai</h3>
                <div class="number" style="color: green;">0</div>
            </div>
        </div>
    </div>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        @endif
    </script>

</body>

</html>
