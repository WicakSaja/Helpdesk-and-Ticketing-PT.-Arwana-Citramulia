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
    <script>
        async function handleLogin(event) {
            event.preventDefault(); // Mencegah reload halaman biasa

            // 1. Ambil nilai input
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const btn = document.getElementById('btnLogin');

            // Ubah tombol jadi loading
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Loading...';
            btn.disabled = true;

            try {
                // 2. Tembak API Login (Pakai variabel global API_URL tadi)
                const response = await fetch(`${API_URL}/login`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    // 3. SUKSES: Simpan Token!
                    // (Pastikan key token dari temanmu namanya 'token' atau 'access_token')
                    localStorage.setItem('user_token', data.token || data.access_token);

                    // Notifikasi Sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Berhasil!',
                        text: 'Mengalihkan ke dashboard...',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '/dashboard'; // Pindah halaman
                    });

                } else {
                    // 4. GAGAL
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Masuk',
                        text: data.message || 'Email atau password salah.',
                        confirmButtonColor: '#d62828'
                    });
                }

            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error Sistem',
                    text: 'Tidak dapat menghubungi server API.',
                    confirmButtonColor: '#d62828'
                });
            } finally {
                // Kembalikan tombol seperti semula
                btn.innerHTML = 'MASUK SEKARANG <i class="fa-solid fa-arrow-right-to-bracket"></i>';
                btn.disabled = false;
            }
        }
    </script>
@endsection
