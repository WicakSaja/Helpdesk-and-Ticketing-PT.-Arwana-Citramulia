<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin Panel')</title>
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
                style="display:block; font-size:12px; color:#999; margin-top:5px; font-weight:600; letter-spacing:1px;">SUPER
                ADMIN</span>
        </div>

        <div class="menu">
            <a href="{{ route('superadmin.dashboard') }}"
                class="menu-item {{ Route::is('superadmin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>

            <a href="{{ route('superadmin.users') }}"
                class="menu-item {{ Route::is('superadmin.users') ? 'active' : '' }}">
                <i class="fa-solid fa-users-gear"></i> Manajemen User
            </a>

            <a href="{{ route('superadmin.departments') }}"
                class="menu-item {{ Route::is('superadmin.departments') ? 'active' : '' }}">
                <i class="fa-solid fa-building"></i> Departemen
            </a>

            <a href="{{ route('superadmin.reports') }}"
                class="menu-item {{ Route::is('superadmin.reports') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice"></i> Laporan Global
            </a>
        </div>

        <div class="mt-auto">
            @include('partials.switch-role')
            <button type="button" class="btn-reset-password" id="btnResetPassword">
                <i class="fa-solid fa-key"></i> Reset Password Admin
            </button>
            <form action="{{ route('logout') }}" method="POST" style="margin-top: 8px;">
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
    <script>
        const API_URL = "{{ env('API_BASE_URL', 'http://localhost:8000') }}";
    </script>
    <script src="{{ asset('js/auth-token-manager.js') }}"></script>
    <script src="{{ asset('js/logout-handler.js') }}"></script>
    <script src="{{ asset('js/role-protection.js') }}"></script>
    <script src="{{ asset('js/page-protection.js') }}"></script>
    <script>
        // Protect superadmin pages
        document.addEventListener('DOMContentLoaded', function() {
            requireMasterAdminRole();
        });
    </script>

    {{-- Mobile Sidebar Script --}}
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

    <!-- Token Manager for API Authentication -->
    <script src="{{ asset('js/auth-token-manager.js') }}"></script>

    {{-- Reset Password Admin Script --}}
    <style>
        .btn-reset-password {
            width: 100%;
            padding: 12px;
            background: #fff8e1;
            color: #e65100;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-align: left;
            padding-left: 20px;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-family: inherit;
        }

        .btn-reset-password:hover {
            background: #e65100;
            color: white;
        }

        /* Modal Overlay */
        .reset-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.2s ease;
        }

        .reset-modal-overlay.active {
            display: flex;
        }

        /* SweetAlert2 harus di atas modal reset */
        .swal2-container {
            z-index: 10001 !important;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .reset-modal {
            background: white;
            border-radius: 16px;
            width: 440px;
            max-width: 92vw;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            animation: slideUp 0.3s ease;
        }

        .reset-modal-header {
            background: linear-gradient(135deg, #d62828, #ff6b6b);
            color: white;
            padding: 24px 28px;
            text-align: center;
        }

        .reset-modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .reset-modal-header p {
            margin: 6px 0 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .reset-modal-body {
            padding: 28px;
        }

        .reset-modal-body .form-group {
            margin-bottom: 18px;
        }

        .reset-modal-body label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .reset-modal-body input {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }

        .reset-modal-body input:focus {
            outline: none;
            border-color: #d62828;
            box-shadow: 0 0 0 3px rgba(214, 40, 40, 0.1);
        }

        .reset-modal-body input.otp-input {
            text-align: center;
            font-size: 28px;
            letter-spacing: 10px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
        }

        .reset-modal-footer {
            padding: 0 28px 24px;
            display: flex;
            gap: 10px;
        }

        .reset-modal-footer button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            font-family: inherit;
            transition: 0.2s;
        }

        .btn-modal-cancel {
            background: #f3f4f6;
            color: #6b7280;
        }

        .btn-modal-cancel:hover {
            background: #e5e7eb;
        }

        .btn-modal-primary {
            background: #d62828;
            color: white;
        }

        .btn-modal-primary:hover {
            background: #b91c1c;
        }

        .btn-modal-primary:disabled {
            background: #d1d5db;
            cursor: not-allowed;
        }

        .reset-info-box {
            background: #fff5f5;
            border: 1px solid #ffcdd2;
            border-radius: 10px;
            padding: 14px;
            font-size: 13px;
            color: #b91c1c;
            margin-bottom: 18px;
            line-height: 1.6;
        }

        .reset-info-box.warning {
            background: #fffbeb;
            border-color: #fcd34d;
            color: #92400e;
        }

        .reset-info-box i {
            margin-right: 6px;
        }

        .otp-timer {
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            margin-top: 10px;
        }

        .otp-timer strong {
            color: #d62828;
        }

        .resend-link {
            color: #d62828;
            cursor: pointer;
            font-weight: 600;
            text-decoration: underline;
        }

        .resend-link:hover {
            color: #b91c1c;
        }

        .password-strength {
            height: 4px;
            background: #e5e7eb;
            border-radius: 4px;
            margin-top: 6px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s, background 0.3s;
            width: 0;
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle input {
            padding-right: 42px;
        }

        .password-toggle .toggle-eye {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            font-size: 16px;
        }

        .password-toggle .toggle-eye:hover {
            color: #6b7280;
        }

        .modal-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #fff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 6px;
            vertical-align: middle;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    {{-- Reset Password Modal Container --}}
    <div class="reset-modal-overlay" id="resetModalOverlay">
        <div class="reset-modal" id="resetModal">
            {{-- Content injected by JS --}}
        </div>
    </div>

    <script>
        (function() {
            const API = "{{ env('API_BASE_URL', 'http://localhost:8000') }}/api";
            const overlay = document.getElementById('resetModalOverlay');
            const modal = document.getElementById('resetModal');
            let otpExpirationMinutes = 15;
            let countdownInterval = null;
            let adminEmail = '';

            // ======= UTILITY =======
            function closeModal() {
                overlay.classList.remove('active');
                if (countdownInterval) clearInterval(countdownInterval);
            }

            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) closeModal();
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && overlay.classList.contains('active')) closeModal();
            });

            function showModal(html) {
                modal.innerHTML = html;
                overlay.classList.add('active');
            }

            // ======= STEP 1: Konfirmasi =======
            document.getElementById('btnResetPassword').addEventListener('click', function() {
                // Ambil email user dari TokenManager
                const user = window.TokenManager ? window.TokenManager.getUser() : null;
                adminEmail = user ? user.email : '';

                showModal(`
                <div class="reset-modal-header">
                    <h3><i class="fa-solid fa-shield-halved"></i> Reset Password Admin</h3>
                    <p>Verifikasi identitas melalui email</p>
                </div>
                <div class="reset-modal-body">
                    <div class="reset-info-box warning">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Anda akan mereset password akun Master Admin. Kode OTP 6 digit akan dikirim ke email terdaftar untuk verifikasi.
                    </div>
                    <div class="form-group">
                        <label>Email Master Admin</label>
                        <input type="email" id="resetEmail" value="" placeholder="Masukkan email admin" autocomplete="off">
                    </div>
                    <div id="emailMatchError" style="display:none; color:#dc2626; font-size:13px; margin-top:-10px; margin-bottom:12px;">
                        <i class="fa-solid fa-circle-xmark"></i> <span></span>
                    </div>
                </div>
                <div class="reset-modal-footer">
                    <button class="btn-modal-cancel" onclick="document.getElementById('resetModalOverlay').classList.remove('active')">Batal</button>
                    <button class="btn-modal-primary" id="btnSendOtp">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Kode OTP
                    </button>
                </div>
            `);

                document.getElementById('btnSendOtp').addEventListener('click', sendOtp);
                document.getElementById('resetEmail').addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') sendOtp();
                });
            });

            // ======= STEP 2: Kirim OTP =======
            async function sendOtp() {
                const emailInput = document.getElementById('resetEmail');
                const email = emailInput.value.trim();
                const errDiv = document.getElementById('emailMatchError');
                const errSpan = errDiv.querySelector('span');
                errDiv.style.display = 'none';
                emailInput.style.borderColor = '#e5e7eb';

                if (!email) {
                    emailInput.focus();
                    emailInput.style.borderColor = '#d62828';
                    errSpan.textContent = 'Email wajib diisi';
                    errDiv.style.display = 'block';
                    return;
                }

                // Cek apakah email cocok dengan email master admin yang sedang login
                const currentUser = window.TokenManager ? window.TokenManager.getUser() : null;
                if (currentUser && currentUser.email && email.toLowerCase() !== currentUser.email.toLowerCase()) {
                    emailInput.focus();
                    emailInput.style.borderColor = '#d62828';
                    errSpan.textContent = 'Email tidak sesuai';
                    errDiv.style.display = 'block';
                    return;
                }

                adminEmail = email;

                const btn = document.getElementById('btnSendOtp');
                btn.disabled = true;
                btn.innerHTML = '<span class="modal-spinner"></span> Mengirim...';

                try {
                    const res = await fetch(`${API}/admin/forgot-password`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            email
                        })
                    });
                    const data = await res.json();

                    if (res.status === 429) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Kirim Kode OTP';
                        Swal.fire({
                            icon: 'warning',
                            title: 'Mohon Tunggu',
                            text: data.message,
                            confirmButtonColor: '#d62828'
                        });
                        return;
                    }

                    if (data.data && data.data.expiration_minutes) {
                        otpExpirationMinutes = data.data.expiration_minutes;
                    }

                    showOtpStep();

                } catch (err) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Kirim Kode OTP';
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal mengirim kode OTP. Periksa koneksi Anda.',
                        confirmButtonColor: '#d62828'
                    });
                }
            }

            // ======= STEP 3: Input OTP =======
            function showOtpStep() {
                let seconds = otpExpirationMinutes * 60;

                showModal(`
                <div class="reset-modal-header">
                    <h3><i class="fa-solid fa-envelope-open-text"></i> Masukkan Kode OTP</h3>
                    <p>Kode telah dikirim ke ${maskEmail(adminEmail)}</p>
                </div>
                <div class="reset-modal-body">
                    <div class="reset-info-box">
                        <i class="fa-solid fa-info-circle"></i>
                        Cek inbox dan folder spam email Anda. Masukkan kode 6 digit yang diterima.
                    </div>
                    <div class="form-group">
                        <label>Kode OTP</label>
                        <input type="text" id="otpInput" class="otp-input" maxlength="6" placeholder="••••••" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code">
                    </div>
                    <div class="otp-timer" id="otpTimer">
                        Kode berlaku <strong id="countdown"></strong>
                    </div>
                    <div class="otp-timer" style="margin-top:8px;">
                        Tidak menerima kode? <span class="resend-link" id="resendOtp">Kirim ulang</span>
                    </div>
                </div>
                <div class="reset-modal-footer">
                    <button class="btn-modal-cancel" onclick="document.getElementById('resetModalOverlay').classList.remove('active')">Batal</button>
                    <button class="btn-modal-primary" id="btnVerifyOtp">
                        <i class="fa-solid fa-check-circle"></i> Verifikasi
                    </button>
                </div>
            `);

                // Auto-focus & filter input
                const otpInput = document.getElementById('otpInput');
                otpInput.focus();
                otpInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                    if (this.value.length === 6) {
                        document.getElementById('btnVerifyOtp').focus();
                    }
                });
                otpInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && this.value.length === 6) verifyOtpStep();
                });

                // Countdown
                startCountdown(seconds);

                // Resend
                document.getElementById('resendOtp').addEventListener('click', async function() {
                    this.textContent = 'Mengirim...';
                    this.style.pointerEvents = 'none';
                    try {
                        const res = await fetch(`${API}/admin/forgot-password`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                email: adminEmail
                            })
                        });
                        const data = await res.json();
                        if (res.status === 429) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Mohon Tunggu',
                                text: data.message,
                                confirmButtonColor: '#d62828'
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terkirim!',
                                text: 'Kode OTP baru telah dikirim.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            seconds = otpExpirationMinutes * 60;
                            startCountdown(seconds);
                        }
                    } catch (err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal mengirim ulang.',
                            confirmButtonColor: '#d62828'
                        });
                    }
                    this.textContent = 'Kirim ulang';
                    this.style.pointerEvents = 'auto';
                });

                // Verify button
                document.getElementById('btnVerifyOtp').addEventListener('click', verifyOtpStep);
            }

            function startCountdown(seconds) {
                if (countdownInterval) clearInterval(countdownInterval);
                const el = document.getElementById('countdown');
                if (!el) return;

                function update() {
                    const m = Math.floor(seconds / 60);
                    const s = seconds % 60;
                    el.textContent = `${m}:${s.toString().padStart(2, '0')}`;
                    if (seconds <= 0) {
                        clearInterval(countdownInterval);
                        el.textContent = 'Expired';
                        el.style.color = '#dc2626';
                    }
                    seconds--;
                }
                update();
                countdownInterval = setInterval(update, 1000);
            }

            // ======= STEP 4: Verifikasi OTP via API → Tampilkan form password baru =======
            let verifiedOtp = '';

            async function verifyOtpStep() {
                const otp = document.getElementById('otpInput').value.trim();
                if (otp.length !== 6) {
                    document.getElementById('otpInput').focus();
                    document.getElementById('otpInput').style.borderColor = '#d62828';
                    return;
                }

                const btn = document.getElementById('btnVerifyOtp');
                btn.disabled = true;
                btn.innerHTML = '<span class="modal-spinner"></span> Memverifikasi...';

                try {
                    const res = await fetch(`${API}/admin/verify-otp`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            email: adminEmail,
                            otp_code: otp
                        })
                    });
                    const data = await res.json();

                    if (!res.ok) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Verifikasi';

                        if (res.status === 429 && data.remaining_attempts === undefined) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Mohon Tunggu',
                                text: data.message,
                                confirmButtonColor: '#d62828'
                            });
                        } else if (data.remaining_attempts !== undefined) {
                            Swal.fire({
                                icon: 'error',
                                title: 'OTP Tidak Valid',
                                text: data.message,
                                confirmButtonColor: '#d62828'
                            });
                            document.getElementById('otpInput').value = '';
                            document.getElementById('otpInput').focus();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Verifikasi OTP gagal.',
                                confirmButtonColor: '#d62828'
                            });
                        }
                        return;
                    }

                    // OTP valid → simpan dan lanjut ke form password
                    verifiedOtp = otp;
                    showNewPasswordStep();

                } catch (err) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Verifikasi';
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan. Periksa koneksi Anda.',
                        confirmButtonColor: '#d62828'
                    });
                }
            }

            // ======= STEP 5: Input Password Baru =======
            function showNewPasswordStep() {
                if (countdownInterval) clearInterval(countdownInterval);

                showModal(`
                <div class="reset-modal-header">
                    <h3><i class="fa-solid fa-lock"></i> Password Baru</h3>
                    <p>Masukkan password baru untuk akun Master Admin</p>
                </div>
                <div class="reset-modal-body">
                    <div class="form-group">
                        <label>Password Baru</label>
                        <div class="password-toggle">
                            <input type="password" id="newPassword" placeholder="Minimal 8 karakter" minlength="8">
                            <i class="fa-solid fa-eye toggle-eye" id="toggleNew"></i>
                        </div>
                        <div class="password-strength"><div class="password-strength-bar" id="strengthBar"></div></div>
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password</label>
                        <div class="password-toggle">
                            <input type="password" id="confirmPassword" placeholder="Ulangi password baru">
                            <i class="fa-solid fa-eye toggle-eye" id="toggleConfirm"></i>
                        </div>
                    </div>
                    <div id="passwordError" style="display:none; color:#dc2626; font-size:13px; margin-bottom:12px;">
                        <i class="fa-solid fa-circle-xmark"></i> <span></span>
                    </div>
                </div>
                <div class="reset-modal-footer">
                    <button class="btn-modal-cancel" onclick="document.getElementById('resetModalOverlay').classList.remove('active')">Batal</button>
                    <button class="btn-modal-primary" id="btnResetFinal">
                        <i class="fa-solid fa-check"></i> Reset Password
                    </button>
                </div>
            `);

                // Toggle password visibility
                setupToggle('toggleNew', 'newPassword');
                setupToggle('toggleConfirm', 'confirmPassword');

                // Password strength
                document.getElementById('newPassword').addEventListener('input', function() {
                    updateStrength(this.value);
                });

                // Submit
                document.getElementById('btnResetFinal').addEventListener('click', submitReset);
                document.getElementById('confirmPassword').addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') submitReset();
                });

                document.getElementById('newPassword').focus();
            }

            function setupToggle(toggleId, inputId) {
                document.getElementById(toggleId).addEventListener('click', function() {
                    const inp = document.getElementById(inputId);
                    if (inp.type === 'password') {
                        inp.type = 'text';
                        this.classList.replace('fa-eye', 'fa-eye-slash');
                    } else {
                        inp.type = 'password';
                        this.classList.replace('fa-eye-slash', 'fa-eye');
                    }
                });
            }

            function updateStrength(pw) {
                const bar = document.getElementById('strengthBar');
                let score = 0;
                if (pw.length >= 8) score++;
                if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score++;
                if (/\d/.test(pw)) score++;
                if (/[^a-zA-Z0-9]/.test(pw)) score++;

                const widths = ['0%', '25%', '50%', '75%', '100%'];
                const colors = ['#e5e7eb', '#ef4444', '#f59e0b', '#22c55e', '#16a34a'];
                bar.style.width = widths[score];
                bar.style.background = colors[score];
            }

            // ======= STEP 6: Submit Reset =======
            async function submitReset() {
                const pw = document.getElementById('newPassword').value;
                const confirm = document.getElementById('confirmPassword').value;
                const errDiv = document.getElementById('passwordError');
                const errSpan = errDiv.querySelector('span');

                errDiv.style.display = 'none';

                if (pw.length < 8) {
                    errSpan.textContent = 'Password minimal 8 karakter';
                    errDiv.style.display = 'block';
                    document.getElementById('newPassword').focus();
                    return;
                }
                if (pw !== confirm) {
                    errSpan.textContent = 'Konfirmasi password tidak cocok';
                    errDiv.style.display = 'block';
                    document.getElementById('confirmPassword').focus();
                    return;
                }

                const btn = document.getElementById('btnResetFinal');
                btn.disabled = true;
                btn.innerHTML = '<span class="modal-spinner"></span> Memproses...';

                try {
                    const res = await fetch(`${API}/admin/reset-password`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            email: adminEmail,
                            otp_code: verifiedOtp,
                            new_password: pw,
                            new_password_confirmation: confirm
                        })
                    });
                    const data = await res.json();

                    if (!res.ok) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-check"></i> Reset Password';

                        if (data.remaining_attempts !== undefined) {
                            errSpan.textContent = data.message;
                            errDiv.style.display = 'block';
                        } else if (res.status === 422 && data.errors) {
                            const firstErr = Object.values(data.errors)[0];
                            errSpan.textContent = Array.isArray(firstErr) ? firstErr[0] : firstErr;
                            errDiv.style.display = 'block';
                        } else {
                            // OTP invalid/expired → kembali ke step OTP
                            closeModal();
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Reset password gagal.',
                                confirmButtonColor: '#d62828'
                            });
                        }
                        return;
                    }

                    // Success!
                    closeModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Berhasil Direset!',
                        html: 'Password Master Admin telah diperbarui.<br>Anda akan diarahkan ke halaman login.',
                        confirmButtonColor: '#d62828',
                        allowOutsideClick: false
                    }).then(() => {
                        // Clear session & redirect to login
                        if (window.TokenManager) window.TokenManager.clearAuth();
                        window.location.href = '/login';
                    });

                } catch (err) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-check"></i> Reset Password';
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan. Periksa koneksi Anda.',
                        confirmButtonColor: '#d62828'
                    });
                }
            }

            // ======= HELPER =======
            function maskEmail(email) {
                if (!email) return '***';
                const [user, domain] = email.split('@');
                if (user.length <= 3) return user[0] + '***@' + domain;
                return user.substring(0, 3) + '***@' + domain;
            }
        })();
    </script>

    @yield('scripts')
</body>

</html>
