@extends('layouts.custom-auth')

@section('title', 'Login')

@section('content')

    <div style="text-align: center; margin-bottom: 30px;">
        <img src="{{ asset('images/logo_arwana.png') }}" alt="Logo Arwana" style="width: 180px; margin-bottom: 15px;">

        <h3 style="color: #333; font-weight: 700; font-size: 20px;">HELPDESK SYSTEM</h3>
        <p style="color: #777; font-size: 14px;">PT. Arwana Citramulia Tbk</p>
    </div>

    <form onsubmit="handleLogin(event)">

        <div class="form-group">
            <label class="form-label">Email Perusahaan</label>
            <div class="input-wrapper">
                <span class="icon-box"><i class="fa-solid fa-envelope"></i></span>
                <input type="email" id="email" class="custom-input" placeholder="nama@arwanacitra.com" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-wrapper">
                <span class="icon-box"><i class="fa-solid fa-lock"></i></span>
                <input type="password" id="password" class="custom-input" placeholder="Masukkan password..." required>
                <span class="toggle-password" onclick="togglePassword('password', this)">
                    <i class="fa-solid fa-eye"></i>
                </span>
            </div>
        </div>

        <button type="submit" class="btn-arwana" id="btnLogin">
            MASUK SEKARANG <i class="fa-solid fa-arrow-right-to-bracket"></i>
        </button>

    </form>

    <div class="auth-footer">
        <p>Belum punya akun? <a href="{{ route('register') }}" class="link-daftar">Daftar di sini</a></p>
    </div>
    
    <script src="{{ asset('js/auth-token-manager.js') }}"></script>
    <script src="{{ asset('js/role-protection.js') }}"></script>
    <script src="{{ asset('js/auth-form-handler.js') }}"></script>
    <script>
        // Redirect to dashboard if already logged in
        document.addEventListener('DOMContentLoaded', function() {
            requireGuest();
        });
    </script>
@endsection
