<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Teknisi')</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/global.css'])
    <style>
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
