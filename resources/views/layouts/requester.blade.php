<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Helpdesk Arwana')</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/global.css'])
    @yield('css')
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

            <a href="{{ route('tickets.create') }}" class="menu-item {{ Route::is('tickets.create') ? 'active' : '' }}">
                <i class="fa-solid fa-plus-circle"></i> Buat Tiket Baru
            </a>

            <a href="{{ route('tickets.index') }}"
                class="menu-item {{ Route::is('tickets.index') || Route::is('tickets.show') ? 'active' : '' }}">
                <i class="fa-solid fa-list-check"></i> Riwayat Tiket
            </a>

            <a href="{{ route('profile') }}" class="menu-item {{ Route::is('profile') ? 'active' : '' }}">
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
