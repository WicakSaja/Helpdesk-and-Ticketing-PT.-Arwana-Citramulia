@extends('layouts.custom-auth')

@section('title', 'Daftar Akun Baru')

@section('content')

    <div style="text-align: center; margin-bottom: 25px;">
        <img src="{{ asset('images/logo_arwana.png') }}" alt="Logo Arwana" style="width: 150px; margin-bottom: 10px;">
        <h3 style="color: #333; font-weight: 700; font-size: 18px; margin-bottom: 5px;">REGISTRASI BARU</h3>
        <p style="color: #777; font-size: 14px;">Isi data diri Anda untuk membuat akun</p>
    </div>

    <form onsubmit="handleRegister(event)">

        <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <div class="input-wrapper">
                <span class="icon-box"><i class="fa-solid fa-user"></i></span>
                <input type="text" id="name" class="custom-input" placeholder="Nama Lengkap..." required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Nomor WhatsApp</label>
            <div class="input-wrapper">
                <span class="icon-box"><i class="fa-brands fa-whatsapp"></i></span>
                <input type="number" id="phone" class="custom-input" placeholder="Contoh: 0812xxxx" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Email Kantor</label>
            <div class="input-wrapper">
                <span class="icon-box"><i class="fa-solid fa-envelope"></i></span>
                <input type="email" id="email" class="custom-input" placeholder="nama@arwanacitra.com" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Departemen</label>
            <div class="input-wrapper">
                <span class="icon-box"><i class="fa-solid fa-building"></i></span>
                <select id="department_id" class="custom-input" style="cursor: pointer; background-color: transparent;"
                    required>
                    <option value="" disabled selected>-- Pilih Departemen --</option>

                    {{-- Cek Data dari Controller --}}
                    @if (isset($departments) && count($departments) > 0)
                        @foreach ($departments as $dept)
                            <option value="{{ $dept['id'] }}">{{ $dept['name'] }}</option>
                        @endforeach
                    @else
                        <option value="" disabled>⚠ Data Departemen Kosong / Gagal Load</option>
                    @endif
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-wrapper">
                <span class="icon-box"><i class="fa-solid fa-lock"></i></span>
                <input type="password" id="password" class="custom-input" placeholder="Minimal 8 karakter..." required>
                <span class="toggle-password" onclick="togglePassword('password', this)">
                    <i class="fa-solid fa-eye"></i>
                </span>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Ulangi Password</label>
            <div class="input-wrapper">
                <span class="icon-box"><i class="fa-solid fa-check-double"></i></span>
                <input type="password" id="password_confirmation" class="custom-input" placeholder="Ketik ulang password..."
                    required>
                <span class="toggle-password" onclick="togglePassword('password_confirmation', this)">
                    <i class="fa-solid fa-eye"></i>
                </span>
            </div>
        </div>

        <button type="submit" class="btn-arwana" id="btnRegister">
            DAFTAR SEKARANG
        </button>

        <div class="auth-footer">
            Sudah punya akun? <a href="{{ route('login') }}" class="link-daftar">Login disini</a>
        </div>

    </form>

    <script>
        async function handleRegister(event) {
            event.preventDefault();

            const btn = document.getElementById('btnRegister');

            // Ambil Data Input
            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                department_id: document.getElementById('department_id').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value,
            };

            // Validasi Password Match Client Side
            if (formData.password !== formData.password_confirmation) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Password Tidak Sama',
                    text: 'Pastikan password konfirmasi sesuai.'
                });
                return;
            }

            // Loading State
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
            btn.disabled = true;

            try {
                // Fetch ke Route Laravel Local
                const response = await fetch("{{ route('register') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registrasi Berhasil!',
                        text: 'Akun Anda telah dibuat. Mengalihkan...',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = data.redirect_url;
                    });
                } else {
                    throw new Error(data.message || 'Registrasi gagal.');
                }

            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Daftar',
                    text: error.message || 'Terjadi kesalahan sistem.',
                    confirmButtonColor: '#d62828'
                });
            } finally {
                btn.innerHTML = 'DAFTAR SEKARANG';
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
