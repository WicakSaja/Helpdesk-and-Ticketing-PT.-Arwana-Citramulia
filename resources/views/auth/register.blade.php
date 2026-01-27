@extends('layouts.custom-auth')

@section('title', 'Daftar Akun Baru')

@section('content')

    <div style="text-align: center; margin-bottom: 25px;">
        <img src="{{ asset('images/logo_arwana.png') }}" alt="Logo Arwana" style="width: 150px; margin-bottom: 10px;">

        <h3 style="color: #333; font-weight: 700; font-size: 18px; margin-bottom: 5px;">REGISTRASI BARU</h3>
        <p style="color: #777; font-size: 14px;">Isi data diri Anda untuk membuat akun</p>
    </div>

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <div class="input-wrapper">
                <span class="icon-box">
                    <i class="fa-solid fa-user"></i>
                </span>
                <input type="text" name="name" class="custom-input" placeholder="Nama Lengkap..."
                    value="{{ old('name') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Nomor WhatsApp</label>
            <div class="input-wrapper">
                <span class="icon-box">
                    <i class="fa-brands fa-whatsapp"></i>
                </span>
                <input type="number" name="phone" class="custom-input" placeholder="Contoh: 0812xxxx"
                    value="{{ old('phone') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Email Kantor</label>
            <div class="input-wrapper">
                <span class="icon-box">
                    <i class="fa-solid fa-envelope"></i>
                </span>
                <input type="email" name="email" class="custom-input" placeholder="nama@arwanacitra.com"
                    value="{{ old('email') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Departemen</label>
            <div class="input-wrapper">
                <span class="icon-box">
                    <i class="fa-solid fa-building"></i>
                </span>
                <select name="department_id" class="custom-input" style="cursor: pointer; background-color: transparent;"
                    required>
                    <option value="" disabled selected>-- Pilih Departemen --</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-wrapper">
                <span class="icon-box"><i class="fa-solid fa-lock"></i></span>

                <input type="password" name="password" class="custom-input" id="passReg"
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

                <input type="password" name="password_confirmation" class="custom-input" id="passConfirm"
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
@endsection
