<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Teknisi')</title>

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
            <span style="display:block; font-size:12px; color:#999; margin-top:5px; font-weight:600; letter-spacing:1px;">TEKNISI</span>
        </div>

        <div class="menu">
            <a href="{{ url('/dashboard/technician') }}"
                class="menu-item {{ Request::is('dashboard/technician') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>

            <a href="{{ route('technician.tasks') }}"
                class="menu-item {{ Route::is('technician.tasks') ? 'active' : '' }}">
                <i class="fa-solid fa-screwdriver-wrench"></i> Tugas Saya
            </a>

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
        const API_URL = "{{ env('API_BASE_URL', 'http://localhost:8000') }}";
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

    {{-- Mobile Sidebar Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            // Fungsi Toggle (Buka/Tutup)
            function toggleSidebar() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }

            // Fungsi Tutup Paksa
            function closeSidebar() {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }

            // Event Listeners
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

    @yield('scripts')
</body>

</html>