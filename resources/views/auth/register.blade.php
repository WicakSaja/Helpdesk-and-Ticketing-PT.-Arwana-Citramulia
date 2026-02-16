@extends('layouts.custom-auth')

@section('title', 'Daftar Akun Baru')

@section('content')

    <div style="text-align: center; margin-bottom: 25px;">
        <img src="{{ asset('images/logo_arwana.png') }}" alt="Logo Arwana" style="width: 150px; margin-bottom: 10px;">

        <h3 style="color: #333; font-weight: 700; font-size: 18px; margin-bottom: 5px;">REGISTRASI AKUN</h3>
        <p style="color: #777; font-size: 14px;">Isi data diri Anda untuk membuat akun</p>
    </div>

    <form onsubmit="handleRegister(event)">

        <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <div class="input-wrapper">
                <span class="icon-box">
                    <i class="fa-solid fa-user"></i>
                </span>
                <input type="text" id="nameReg" class="custom-input" placeholder="Nama Lengkap..." required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Nomor WhatsApp</label>
            <div class="input-wrapper">
                <span class="icon-box">
                    <i class="fa-brands fa-whatsapp"></i>
                </span>
                <input type="number" id="phoneReg" class="custom-input" placeholder="Contoh: 0812xxxx" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Email Kantor</label>
            <div class="input-wrapper">
                <span class="icon-box">
                    <i class="fa-solid fa-envelope"></i>
                </span>
                <input type="email" id="emailReg" class="custom-input" placeholder="nama@arwanacitra.com" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Departemen</label>
            <div class="input-wrapper">
                <span class="icon-box">
                    <i class="fa-solid fa-building"></i>
                </span>
                <select id="departmentSelect" class="custom-input" 
                    style="cursor: pointer; background-color: transparent;" required>
                    <option value="" disabled selected>-- Pilih Departemen --</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-wrapper">
                <span class="icon-box"><i class="fa-solid fa-lock"></i></span>

                <input type="password" class="custom-input" id="passReg"
                    placeholder="Minimal 8 karakter..." required>

                <span class="toggle-password" onclick="togglePassword('passReg', this)">
                    <i class="fa-solid fa-eye"></i>
                </span>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Ulangi Password</label>
            <div class="input-wrapper">
                <span class="icon-box"><i class="fa-solid fa-check-double"></i></span>

                <input type="password" class="custom-input" id="passConfirm"
                    placeholder="Ketik ulang password..." required>

                <span class="toggle-password" onclick="togglePassword('passConfirm', this)">
                    <i class="fa-solid fa-eye"></i>
                </span>
            </div>
        </div>

        <button type="submit" class="btn-arwana">
            DAFTAR SEKARANG
        </button>

        <div class="auth-footer">
            Sudah punya akun? <a href="{{ route('login') }}" class="link-daftar">Login disini</a>
        </div>

    </form>

    <script src="{{ asset('js/auth-token-manager.js') }}"></script>
    <script src="{{ asset('js/role-protection.js') }}"></script>
    <script src="{{ asset('js/auth-form-handler.js') }}"></script>
    <script>
        // Redirect to dashboard if already logged in, or clear invalid auth data
        document.addEventListener('DOMContentLoaded', function() {
            requireGuestSync();
        });
    </script>
@endsection
