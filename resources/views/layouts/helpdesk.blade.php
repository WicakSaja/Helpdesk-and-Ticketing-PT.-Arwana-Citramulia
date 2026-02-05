<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Helpdesk Arwana')</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/global.css'])
    @yield('head')
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
            <img src="{{ asset('images/logo_arwana.png') }}" alt="Arwana Ceramics" class="img-logo">
            <span
                style="display:block; font-size:12px; color:#999; margin-top:5px; font-weight:600; letter-spacing:1px;">
                HELPDESK</span>
        </div>

        <div class="menu">
            <a href="{{ route('dashboard.helpdesk') }}"
                class="menu-item {{ Route::is('dashboard.helpdesk') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>

            <a href="{{ route('helpdesk.incoming') }}"
                class="menu-item {{ Route::is('helpdesk.incoming') ? 'active' : '' }}">
                <i class="fa-solid fa-inbox"></i> Tiket Masuk
                <span class="menu-badge" id="pendingCount" style="display: none;">0</span>
            </a>

            <a href="{{ route('helpdesk.actions') }}"
                class="menu-item {{ Route::is('helpdesk.actions') ? 'active' : '' }}">
                <i class="fa-solid fa-check-double"></i> Aksi Tiket
            </a>

            <a href="{{ route('helpdesk.technicians') }}"
                class="menu-item {{ Route::is('helpdesk.technicians') ? 'active' : '' }}">
                <i class="fa-solid fa-users-gear"></i> Daftar Teknisi
            </a>

            <a href="{{ route('helpdesk.all') }}" class="menu-item {{ Route::is('helpdesk.all') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i> Semua Data Tiket
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
    <script src="{{ asset('js/role-protection.js') }}"></script>
    <script src="{{ asset('js/page-protection.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            // Skip if page sets flag to disable this fetch
            if (window.SKIP_PENDING_COUNT_FETCH) return;

            const token = sessionStorage.getItem('auth_token') || localStorage.getItem('auth_token');
            const badge = document.getElementById('pendingCount');

            if (token && badge) {
                try {
                    // Fetch hanya count (lightweight endpoint)
                    const response = await fetch('/api/tickets/count?status=open', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const result = await response.json();
                        const count = result.count || 0;

                        if (count > 0) {
                            badge.innerText = count;
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                } catch (error) {
                    console.error('Gagal update badge:', error);
                }
            }
        });
    </script>

    {{-- Mobile Sidebar Script (UPDATED) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            // Fungsi Toggle (Buka/Tutup)
            function toggleSidebar() {
                // Kita mainkan Class 'active' saja, biar CSS yang atur animasi
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }

            // Fungsi Tutup Paksa (saat klik overlay)
            function closeSidebar() {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }

            // Event Listener
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation(); // Mencegah klik tembus
                    toggleSidebar();
                });
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }
        });
    </script>
    @yield('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
