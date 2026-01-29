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
            event.preventDefault(); // Mencegah reload

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const btn = document.getElementById('btnLogin');

            // Loading State
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Loading...';
            btn.disabled = true;

            try {
                // FETCH KE ROUTE LOCAL (CONTROLLER WEB)
                const response = await fetch("{{ route('login') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}" // WAJIB DI LARAVEL WEB
                    },
                    body: JSON.stringify({
                        email: email,       // Controller Web minta 'email'
                        password: password
                    })
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    
                    // Notifikasi Sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Berhasil!',
                        text: 'Mengalihkan ke dashboard...',
                        timer: 1000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect sesuai instruksi Controller
                        window.location.href = data.redirect_url;
                    });

                } else {
                    throw new Error(data.message || 'Email atau password salah.');
                }

            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Masuk',
                    text: error.message || 'Terjadi kesalahan sistem.',
                    confirmButtonColor: '#d62828'
                });
            } finally {
                btn.innerHTML = 'MASUK SEKARANG <i class="fa-solid fa-arrow-right-to-bracket"></i>';
                btn.disabled = false;
            }
        }

        function togglePassword(id, el) {
            let input = document.getElementById(id);
            let icon = el.querySelector('i');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
@endsection