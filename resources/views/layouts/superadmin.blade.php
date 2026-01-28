<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Admin Panel')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* GLOBAL STYLE */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: #f4f6f9; display: flex; min-height: 100vh; }
        a { text-decoration: none; }
        
        /* SIDEBAR */
        .sidebar { width: 260px; background: white; padding: 30px 20px; display: flex; flex-direction: column; border-right: 1px solid #eee; position: fixed; height: 100vh; z-index: 1000; }
        .sidebar-logo { text-align: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #f0f0f0; }
        .img-logo { width: 160px; height: auto; }
        
        .menu-item { display: flex; align-items: center; padding: 12px 15px; color: #666; border-radius: 10px; margin-bottom: 8px; font-weight: 500; font-size: 14px; transition: 0.3s; position: relative; }
        .menu-item i { width: 30px; font-size: 16px; }
        .menu-item:hover { background-color: #e3f2fd; color: #1565c0; }
        
        /* Active State */
        .menu-item.active { background-color: #1565c0; color: white; box-shadow: 0 4px 10px rgba(21, 101, 192, 0.3); }

        .mt-auto { margin-top: auto; }
        .btn-logout { width: 100%; padding: 12px; background: #fff0f0; color: #d62828; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; text-align: left; padding-left: 20px; transition: 0.3s; }
        .btn-logout:hover { background: #d62828; color: white; }

        /* KONTEN UTAMA */
        .main-content { flex: 1; margin-left: 260px; padding: 35px 40px; }

        @yield('css')
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('images/logo_arwana.png') }}" alt="Logo" class="img-logo">
            <span style="display:block; font-size:12px; color:#999; margin-top:5px; font-weight:600; letter-spacing:1px;">SUPER ADMIN</span>
        </div>

        <div class="menu">
            <a href="{{ route('superadmin.dashboard') }}" 
               class="menu-item {{ Route::is('superadmin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>

            <a href="{{ route('superadmin.users') }}" 
               class="menu-item {{ Route::is('superadmin.users') ? 'active' : '' }}">
                <i class="fa-solid fa-users-gear"></i> Manajemen User
            </a>

            <a href="{{ route('superadmin.departments') }}" 
               class="menu-item {{ Route::is('superadmin.departments') ? 'active' : '' }}">
                <i class="fa-solid fa-building"></i> Departemen
            </a>

            <a href="{{ route('superadmin.reports') }}" 
   class="menu-item {{ Route::is('superadmin.reports') ? 'active' : '' }}">
    <i class="fa-solid fa-file-invoice"></i> Laporan Global
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

    @yield('scripts')
</body>
</html>