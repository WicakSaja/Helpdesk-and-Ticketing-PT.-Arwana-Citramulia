<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Teknisi')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS GLOBAL (Sesuai kode Mas Fadhli) */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: #f4f6f9; display: flex; min-height: 100vh; }
        a { text-decoration: none; }
        
        /* SIDEBAR */
        .sidebar { width: 260px; background: white; padding: 30px 20px; display: flex; flex-direction: column; border-right: 1px solid #eee; position: fixed; height: 100vh; z-index: 1000; }
        .sidebar-logo { text-align: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0; }
        .img-logo { width: 160px; height: auto; }
        
        .menu-item { display: flex; align-items: center; padding: 12px 15px; color: #666; border-radius: 10px; margin-bottom: 8px; font-weight: 500; font-size: 14px; transition: 0.3s; position: relative; }
        .menu-item i { width: 30px; font-size: 16px; }
        .menu-item:hover { background-color: #ffebee; color: #d62828; }
        
        /* Active State (Merah Arwana) */
        .menu-item.active { background-color: #d62828; color: white; box-shadow: 0 4px 10px rgba(214, 40, 40, 0.3); }
        .menu-badge { background: #d62828; color: white; font-size: 10px; padding: 2px 8px; border-radius: 20px; position: absolute; right: 15px; font-weight: 600; }

        .mt-auto { margin-top: auto; }
        .btn-logout { width: 100%; padding: 12px; background: #fff0f0; color: #d62828; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; text-align: left; padding-left: 20px; transition: 0.3s; }
        .btn-logout:hover { background: #d62828; color: white; }

        /* KONTEN UTAMA */
        .main-content { flex: 1; margin-left: 260px; padding: 30px 40px; }

        /* Area CSS Tambahan */
        @yield('css')
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('images/logo_arwana.png') }}" alt="Logo" class="img-logo">
        </div>

        <div class="menu">
            <a href="{{ url('/dashboard/technician') }}" 
               class="menu-item {{ Request::is('dashboard/technician') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>

            <a href="{{ route('technician.tasks') }}" 
               class="menu-item {{ Route::is('technician.tasks') ? 'active' : '' }}">
                <i class="fa-solid fa-screwdriver-wrench"></i> Tugas Saya
                <span class="menu-badge" style="background: white; color: #d62828;">2</span> </a>

            <a href="{{ route('technician.history') }}" 
               class="menu-item {{ Route::is('technician.history') ? 'active' : '' }}">
                <i class="fa-solid fa-clipboard-check"></i> Riwayat Selesai
            </a>

            <a href="{{ route('technician.profile') }}" 
               class="menu-item {{ Route::is('technician.profile') ? 'active' : '' }}">
                <i class="fa-solid fa-user"></i> Profil Saya
            </a>
        </div>

        <div class="mt-auto">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            </form>
        </div>
    </div>

    <div class="main-content">
        @yield('content')
    </div>

    {{-- Auth Scripts --}}
    <script>
        const API_URL = 'http://127.0.0.1:8000';
    </script>
    <script src="{{ asset('js/auth-token-manager.js') }}"></script>
    <script src="{{ asset('js/logout-handler.js') }}"></script>
    <script src="{{ asset('js/role-protection.js') }}"></script>
    <script src="{{ asset('js/page-protection.js') }}"></script>
    <script>
        // Protect technician pages
        document.addEventListener('DOMContentLoaded', function() {
            requireTechnicianRole();
        });
    </script>

    @yield('scripts')
</body>
</html>