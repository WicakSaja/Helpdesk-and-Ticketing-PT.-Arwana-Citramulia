<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Helpdesk Arwana')</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Global CSS --}}
    @vite(['resources/css/global.css'])
    @yield('css')
</head>

<body>

    {{-- MOBILE HEADER --}}
    <div class="mobile-header-bar">
        <button class="mobile-toggle-btn" id="sidebarToggle">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="mobile-logo-container">
            <img src="{{ asset('images/logo_arwana.png') }}" alt="Arwana Ceramics" class="mobile-logo-img">
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- SIDEBAR --}}
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('images/logo_arwana.png') }}" alt="Logo" class="img-logo">
            <span
                style="display:block; font-size:12px; color:#999; margin-top:5px; font-weight:600; letter-spacing:1px;">
                HELPDESK</span> {{-- Label diganti jadi HELPDESK --}}
        </div>

        <div class="menu">
            {{-- Dashboard --}}
            <a href="{{ route('dashboard.helpdesk') }}"
                class="menu-item {{ Route::is('dashboard.helpdesk') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>

            {{-- Tiket Masuk --}}
            <a href="{{ route('helpdesk.incoming') }}"
                class="menu-item {{ Route::is('helpdesk.incoming') ? 'active' : '' }}">
                <i class="fa-solid fa-inbox"></i> Tiket Masuk
                {{-- Badge Counter (Optional) --}}
                <span class="menu-badge" id="openCount" style="display: none;">0</span>
            </a>

            {{-- Action (Reject/Close) --}}
            <a href="{{ route('helpdesk.actions') }}"
                class="menu-item {{ Route::is('helpdesk.actions') ? 'active' : '' }}">
                <i class="fa-solid fa-check-double"></i> Tiket Selesai
                <span class="menu-badge" id="resolvedCount" style="display: none;">0</span>
            </a>

            {{-- MENU MANAJEMEN KATEGORI --}}
            <a href="{{ route('helpdesk.categories') }}"
                class="menu-item {{ Route::is('helpdesk.categories') ? 'active' : '' }}">
                <i class="fa-solid fa-tags"></i> Manajemen Kategori
            </a>

            {{-- Daftar Teknisi --}}
            <a href="{{ route('helpdesk.technicians') }}"
                class="menu-item {{ Route::is('helpdesk.technicians') ? 'active' : '' }}">
                <i class="fa-solid fa-users-gear"></i> Daftar Teknisi
            </a>

            {{-- Semua Tiket --}}
            <a href="{{ route('helpdesk.all') }}" class="menu-item {{ Route::is('helpdesk.all') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i> Semua Data Tiket
            </a>

            {{-- Reports --}}
            <a href="{{ route('helpdesk.reports') }}"
                class="menu-item {{ Route::is('helpdesk.reports') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice"></i> Laporan
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

    {{-- MAIN CONTENT --}}
    <div class="main-content">
        @yield('content')
    </div>

    {{-- SCRIPTS --}}
    <script>
        const API_URL = "{{ env('API_BASE_URL', 'http://localhost:8000') }}";
    </script>
    <script src="{{ asset('js/auth-token-manager.js') }}"></script>
    <script src="{{ asset('js/logout-handler.js') }}"></script>
    <script src="{{ asset('js/role-protection.js') }}"></script>
    <script src="{{ asset('js/page-protection.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Protect Helpdesk pages
        document.addEventListener('DOMContentLoaded', function() {
            // Pastikan Anda punya fungsi ini di role-protection.js
            // Jika belum ada, buat logic cek role 'helpdesk' atau 'admin'
            if (typeof requireHelpdeskRole === 'function') {
                requireHelpdeskRole();
            } else {
                // Fallback: Cek login standar
                const token = sessionStorage.getItem('auth_token');
                if (!token) window.location.href = '/login';
            }
        });
    </script>

    {{-- Script Hitung Tiket Pending (Opsional) --}}
    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            if (window.SKIP_PENDING_COUNT_FETCH) return;

            const token = sessionStorage.getItem('auth_token');
            const openBadge = document.getElementById('openCount');
            const resolvedBadge = document.getElementById('resolvedCount');

            if (token) {
                try {
                    const [openResponse, resolvedResponse] = await Promise.all([
                        fetch('/api/tickets/count?status=open', {
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json'
                            }
                        }),
                        fetch('/api/tickets/count?status=resolved', {
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json'
                            }
                        })
                    ]);

                    if (openResponse.ok && openBadge) {
                        const openResult = await openResponse.json();
                        const openCount = openResult.count || 0;

                        if (openCount > 0) {
                            openBadge.innerText = openCount;
                            openBadge.style.display = 'inline-block';
                        } else {
                            openBadge.style.display = 'none';
                        }
                    }

                    if (resolvedResponse.ok && resolvedBadge) {
                        const resolvedResult = await resolvedResponse.json();
                        const resolvedCount = resolvedResult.count || 0;

                        if (resolvedCount > 0) {
                            resolvedBadge.innerText = resolvedCount;
                            resolvedBadge.style.display = 'inline-block';
                        } else {
                            resolvedBadge.style.display = 'none';
                        }
                    }
                } catch (error) {
                    console.error('Gagal update badge:', error);
                }
            }
        });
    </script>

    {{-- Mobile Sidebar Toggle --}}
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

    @yield('scripts')
</body>

</html>
