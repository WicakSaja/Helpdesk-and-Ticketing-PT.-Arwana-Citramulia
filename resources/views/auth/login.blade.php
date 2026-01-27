@extends('layouts.custom-auth')

@section('title', 'Login')

@section('content')

    <div style="text-align: center; margin-bottom: 30px;">
    <img src="{{ asset('images/logo_arwana.png') }}" alt="Logo Arwana" style="width: 180px; margin-bottom: 15px;">
    
    <h3 style="color: #333; font-weight: 700; font-size: 20px;">HELPDESK SYSTEM</h3>
    <p style="color: #777; font-size: 14px;">PT. Arwana Citramulia Tbk</p>
</div>

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="form-group">
            <label style="font-size: 13px; font-weight: 600; color: #555; margin-bottom: 5px; display:block;">Email
                Perusahaan</label>
            <div class="input-wrapper">
                <span class="icon-box">
                    <i class="fa-solid fa-envelope"></i>
                </span>
                <input type="email" name="email" class="custom-input" placeholder="contoh@arwanacitra.com"
                    value="{{ old('email') }}" required>
            </div>
        </div>

        <div class="form-group">
        <label class="form-label">Password</label>
        <div class="input-wrapper">
            <span class="icon-box">
                <i class="fa-solid fa-lock"></i>
            </span>
            
            <input type="password" name="password" class="custom-input" id="passwordLogin" placeholder="Masukkan password..." required>
            
            <span class="toggle-password" onclick="togglePassword('passwordLogin', this)">
                <i class="fa-solid fa-eye"></i>
            </span>
        </div>
    </div>

        <button type="submit" class="btn-arwana">
            MASUK
        </button>

        <div class="auth-footer">
        Belum punya akun? <a href="{{ route('register') }}" class="link-daftar">Daftar Akun Baru</a>
    </div>
    </form>
@endsection
