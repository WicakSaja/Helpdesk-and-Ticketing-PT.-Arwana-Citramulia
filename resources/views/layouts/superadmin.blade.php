<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin Panel')</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/global.css'])
    @yield('css')
</head>

<body>

    <div class="mobile-header-bar">
        <button class="mobile-toggle-btn" id="sidebarToggle">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="mobile-logo-container">
            <img src="{{ asset('images/logo_arwana.png') }}" alt="Arwana Ceramics" class="mobile-logo-img">
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('images/logo_arwana.png') }}" alt="Logo" class="img-logo">
            <span
                style="display:block; font-size:12px; color:#999; margin-top:5px; font-weight:600; letter-spacing:1px;">SUPER
                ADMIN</span>
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

    {{-- Auth Scripts --}}
    <script>
        const API_URL = "{{ env('API_BASE_URL', 'http://localhost:8000') }}";
    </script>
    <script src="{{ asset('js/auth-token-manager.js') }}"></script>
    <script src="{{ asset('js/logout-handler.js') }}"></script>
    <script src="{{ asset('js/role-protection.js') }}"></script>
    <script src="{{ asset('js/page-protection.js') }}"></script>
    <script>
        // Protect superadmin pages
        document.addEventListener('DOMContentLoaded', function() {
            requireMasterAdminRole();
        });
    </script>

    {{-- Mobile Sidebar Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }

            function closeSidebar() {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleSidebar();
                });
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }
        });
    </script>

    <!-- Token Manager for API Authentication -->
    <script src="{{ asset('js/auth-token-manager.js') }}"></script>

    @yield('scripts')
</body>

</html>
