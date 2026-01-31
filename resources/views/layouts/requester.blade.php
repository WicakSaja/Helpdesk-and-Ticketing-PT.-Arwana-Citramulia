<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Helpdesk Arwana')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* --- GLOBAL STYLE --- */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: #f4f6f9; display: flex; min-height: 100vh; }
        a { text-decoration: none; }
        
        /* --- SIDEBAR --- */
        .sidebar { width: 260px; background: white; padding: 30px 20px; display: flex; flex-direction: column; border-right: 1px solid #eee; position: fixed; height: 100vh; z-index: 100; }
        .sidebar-logo { text-align: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0; }
        .img-logo { width: 160px; height: auto; }
        
        .menu-item { display: flex; align-items: center; padding: 12px 15px; color: #666; border-radius: 10px; margin-bottom: 8px; font-weight: 500; font-size: 14px; transition: 0.3s; position: relative; }
        .menu-item i { width: 30px; font-size: 16px; }
        .menu-item:hover { background-color: #ffebee; color: #d62828; }
        
        /* Active State */
        .menu-item.active { background-color: #d62828; color: white; box-shadow: 0 4px 10px rgba(214, 40, 40, 0.3); }

        .mt-auto { margin-top: auto; }
        .btn-logout { width: 100%; padding: 12px; background: #fff0f0; color: #d62828; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; text-align: left; padding-left: 20px; transition: 0.3s; }
        .btn-logout:hover { background: #d62828; color: white; }

        /* --- KONTEN UTAMA --- */
        .main-content { flex: 1; margin-left: 260px; padding: 35px 40px; }

        /* Style CSS Tambahan Per Halaman */
        @yield('css')
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('images/logo_arwana.png') }}" alt="Logo" class="img-logo">
        </div>

        <div class="menu">
            <a href="{{ url('/dashboard/requester') }}" 
               class="menu-item {{ Request::is('dashboard/requester') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>

            <a href="{{ route('tickets.create') }}" 
               class="menu-item {{ Route::is('tickets.create') ? 'active' : '' }}">
                <i class="fa-solid fa-plus-circle"></i> Buat Tiket Baru
            </a>

            <a href="{{ route('tickets.index') }}" 
               class="menu-item {{ Route::is('tickets.index') || Route::is('tickets.show') ? 'active' : '' }}">
                <i class="fa-solid fa-list-check"></i> Riwayat Tiket
            </a>

            <a href="{{ route('profile') }}" 
               class="menu-item {{ Route::is('profile') ? 'active' : '' }}">
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
        @yield('content')
    </div>

    {{-- Auth Scripts --}}
    <script>
        const API_URL = 'http://127.0.0.1:8000';
    </script>
    <script src="{{ asset('js/auth-token-manager.js') }}"></script>
    <script src="{{ asset('js/logout-handler.js') }}"></script>

    @yield('scripts')
</body>
</html>