<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Helpdesk Arwana')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/arwanamerah.jpg') }}">

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
                style="display:block; font-size:12px; color:#999; margin-top:5px; font-weight:600; letter-spacing:1px;">
                REQUESTER</span>
        </div>

        <div class="menu">
            <a href="{{ url('/dashboard/requester') }}"
                class="menu-item {{ Request::is('dashboard/requester') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>

            <a href="{{ route('tickets.create') }}" class="menu-item {{ Route::is('tickets.create') ? 'active' : '' }}">
                <i class="fa-solid fa-plus-circle"></i> Buat Tiket Baru
            </a>

            <a href="{{ route('tickets.actions') }}"
                class="menu-item {{ Route::is('tickets.actions') ? 'active' : '' }}">
                <i class="fa-solid fa-check-double"></i> Tiket Selesai
                <span class="menu-badge" id="resolved-badge" style="display: none;">0</span>
            </a>

            <a href="{{ route('tickets.index') }}"
                class="menu-item {{ Request::is('tickets*') && !Request::is('tickets/create') && !Request::is('tickets/actions') ? 'active' : '' }}">
                <i class="fa-solid fa-list-check"></i> Riwayat Tiket
            </a>

            <a href="{{ route('profile') }}" class="menu-item {{ Route::is('profile') ? 'active' : '' }}">
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
        @include('partials.running-text')
        @yield('content')
    </div>

    {{-- Auth Scripts --}}

    <script src="{{ asset('js/auth-token-manager.js') }}"></script>
    <script src="{{ asset('js/logout-handler.js') }}"></script>
    <script src="{{ asset('js/role-protection.js') }}"></script>
    <script src="{{ asset('js/page-protection.js') }}"></script>
    <script>
        // Protect requester pages
        document.addEventListener('DOMContentLoaded', function() {
            requireRequesterRole();
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
                // Toggle class 'active' di Sidebar DAN Overlay
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }

            // Fungsi Tutup Paksa (saat klik overlay)
            function closeSidebar() {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }

            // Event Listener Tombol
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation(); // Mencegah klik tembus
                    toggleSidebar();
                });
            }

            // Event Listener Overlay (Klik area gelap untuk tutup)
            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            // Load resolved ticket count badge
            loadResolvedBadge();
        });

        async function loadResolvedBadge() {
            try {
                const token = sessionStorage.getItem('auth_token');
                const response = await fetch('/api/my-tickets', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) return;

                const result = await response.json();
                const tickets = Array.isArray(result?.data) ? result.data : [];

                // Hitung tiket yang statusnya "resolved"
                const resolvedCount = tickets.filter(ticket =>
                    (ticket.status?.name || '').toLowerCase() === 'resolved'
                ).length;

                const badge = document.getElementById('resolved-badge');
                if (badge && resolvedCount > 0) {
                    badge.textContent = resolvedCount;
                    badge.style.display = 'inline-block';
                }
            } catch (error) {
                console.error('Failed to load resolved badge:', error);
            }
        }
    </script>

    @yield('scripts')
</body>

</html>
