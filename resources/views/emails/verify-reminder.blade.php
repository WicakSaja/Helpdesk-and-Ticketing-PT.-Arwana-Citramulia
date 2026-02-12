<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verifikasi Email - Arwana Helpdesk</title>
    <link rel="icon" type="image/png" href="{{ asset('images/arwanamerah.jpg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- GLOBAL --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow-x: hidden;

            /* Abstract Background Pattern matching Arwana theme */
            background:
                radial-gradient(circle at 10% 20%, rgba(214, 40, 40, 0.18) 0%, transparent 50%),
                radial-gradient(circle at 90% 80%, rgba(214, 40, 40, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(255, 107, 107, 0.1) 0%, transparent 60%),
                radial-gradient(circle at 30% 70%, rgba(214, 40, 40, 0.12) 0%, transparent 35%),
                radial-gradient(circle, rgba(214, 40, 40, 0.25) 1.5px, transparent 1.5px),
                linear-gradient(135deg, #f8f9fc 0%, #eef1f5 50%, #f4f6f9 100%);
            background-size: 100% 100%, 100% 100%, 100% 100%, 100% 100%, 25px 25px, 100% 100%;
            background-position: 0 0, 0 0, 0 0, 0 0, 0 0, 0 0;
        }

        /* Abstract floating shapes */
        body::before {
            content: '';
            position: fixed;
            top: -150px;
            right: -150px;
            width: 450px;
            height: 450px;
            background: linear-gradient(135deg, rgba(214, 40, 40, 0.2) 0%, rgba(255, 107, 107, 0.1) 100%);
            border-radius: 50%;
            z-index: 0;
            animation: floatShape 15s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: fixed;
            bottom: -120px;
            left: -120px;
            width: 350px;
            height: 350px;
            background: linear-gradient(45deg, rgba(214, 40, 40, 0.18) 0%, rgba(255, 200, 200, 0.1) 100%);
            border-radius: 50%;
            z-index: 0;
            animation: floatShape 12s ease-in-out infinite reverse;
        }

        @keyframes floatShape {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(30px, -30px) scale(1.1);
            }
        }

        /* --- MODAL CONTAINER --- */
        .modal-container {
            position: relative;
            z-index: 1000;
            width: 100%;
            height: 100vh;
            max-width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: slideUp 0.4s ease-out;
        }

        /* --- MODAL CARD --- */
        .modal-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(214, 40, 40, 0.15);
            display: flex;
            align-items: center;
            gap: 60px;
            width: 90%;
            max-width: 900px;
        }

        /* --- ICON SECTION (LEFT) --- */
        .icon-section {
            flex-shrink: 0;
            text-align: center;
        }

        .verification-icon {
            width: 140px;
            height: 140px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            background: linear-gradient(135deg, #d62828 0%, #ff6b6b 100%);
            color: white;
            box-shadow: 0 10px 40px rgba(214, 40, 40, 0.3);
        }

        .logo-brand {
            font-size: 14px;
            font-weight: 600;
            color: #d62828;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 10px;
        }

        /* --- CONTENT SECTION (RIGHT) --- */
        .content-section {
            flex: 1;
            min-width: 0;
        }

        /* --- TITLE --- */
        .modal-title {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        /* --- SUBTITLE --- */
        .modal-subtitle {
            font-size: 16px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        /* --- INFO BOX --- */
        .info-box {
            background: #fff5f5;
            border: 1px solid #ffcdd2;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .info-item {
            display: flex;
            gap: 12px;
            margin-bottom: 14px;
            font-size: 14px;
            color: #475569;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-item i {
            color: #d62828;
            font-size: 16px;
            min-width: 20px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        /* --- ALERT --- */
        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 20px;
            display: none;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        /* --- BUTTONS --- */
        .button-group {
            display: flex;
            gap: 12px;
            flex-wrap: nowrap;
        }

        .btn {
            padding: 14px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            flex: 1;
            white-space: nowrap;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #d62828 0%, #ff6b6b 100%);
            color: white;
            box-shadow: 0 10px 25px rgba(214, 40, 40, 0.3);
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(214, 40, 40, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #d62828;
            border: 2px solid #d62828;
        }

        .btn-secondary:hover:not(:disabled) {
            background: #fff5f5;
            transform: translateY(-2px);
        }

        .btn-logout {
            background: white;
            color: #64748b;
            border: 2px solid #e2e8f0;
        }

        .btn-logout:hover {
            background: #f8fafc;
            color: #475569;
            transform: translateY(-2px);
        }

        .countdown {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 12px;
        }

        /* --- ANIMATIONS --- */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            .modal-card {
                flex-direction: column;
                padding: 40px 25px;
                gap: 30px;
                text-align: center;
            }

            .icon-section {
                margin: 0 auto;
            }

            .content-section {
                text-align: center;
            }

            .modal-title {
                font-size: 24px;
            }

            .modal-subtitle {
                font-size: 14px;
            }

            .info-box {
                text-align: center;
            }

            .info-item {
                justify-content: center;
            }

            .button-group {
                justify-content: center;
            }

            .btn {
                flex: 1;
                min-width: 150px;
            }

            .verification-icon {
                width: 100px;
                height: 100px;
                font-size: 48px;
            }
        }

        @media (max-width: 480px) {
            .modal-container {
                padding: 10px;
            }

            .modal-card {
                padding: 30px 20px;
                gap: 20px;
            }

            .modal-title {
                font-size: 20px;
            }

            .modal-subtitle {
                font-size: 13px;
            }

            .btn {
                padding: 12px 24px;
                font-size: 14px;
                width: 100%;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <script src="{{ asset('js/auth-token-manager.js') }}"></script>

    <!-- Modal Container -->
    <div class="modal-container">
        <div class="modal-card">
            <!-- Icon Section -->
            <div class="icon-section">
                <div class="verification-icon">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="logo-brand">Arwana Helpdesk</div>
            </div>

            <!-- Content Section -->
            <div class="content-section">
                <h2 class="modal-title">Verifikasi Email Anda</h2>

                <p class="modal-subtitle">
                    Untuk melanjutkan menggunakan Arwana Helpdesk, silakan verifikasi email Anda terlebih dahulu.
                    Kami telah mengirimkan link verifikasi ke email <strong id="userEmail"></strong>.
                </p>

                <!-- Alert Messages -->
                <div class="alert alert-success" id="alertSuccess">
                    <i class="fa-solid fa-circle-check"></i>
                    <span id="alertSuccessText"></span>
                </div>
                <div class="alert alert-error" id="alertError">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span id="alertErrorText"></span>
                </div>

                <div class="info-box">
                    <div class="info-item">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Verifikasi email meningkatkan keamanan akun Anda</span>
                    </div>
                    <div class="info-item">
                        <i class="fa-solid fa-envelope-open-text"></i>
                        <span>Periksa inbox email Anda untuk link verifikasi</span>
                    </div>
                    <div class="info-item">
                        <i class="fa-solid fa-clock"></i>
                        <span>Link verifikasi berlaku selama 30 menit</span>
                    </div>
                    <div class="info-item">
                        <i class="fa-solid fa-rotate"></i>
                        <span>Gunakan tombol di bawah jika belum menerima email</span>
                    </div>
                </div>

                <div class="button-group">
                    <button type="button" class="btn btn-logout" id="btnLogout">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </button>
                    <button type="button" class="btn btn-primary" id="btnResend">
                        <i class="fa-solid fa-paper-plane"></i>
                        Kirim Ulang Email Verifikasi
                    </button>
                    <button type="button" class="btn btn-secondary" id="btnCheckStatus">
                        <i class="fa-solid fa-circle-check"></i>
                        Sudah Verifikasi
                    </button>
                </div>

                <p class="countdown" id="countdown" style="display: none;"></p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const token = TokenManager.getToken();
            const user = TokenManager.getUser();

            // Jika tidak ada token, redirect ke login
            if (!token) {
                window.location.href = '/login';
                return;
            }

            // Jika email sudah terverifikasi, redirect ke dashboard
            if (user && user.email_verified_at) {
                TokenManager.redirectToDashboard();
                return;
            }

            // Tampilkan email user
            const emailEl = document.getElementById('userEmail');
            if (user && user.email) {
                emailEl.textContent = user.email;
            }

            const btnResend = document.getElementById('btnResend');
            const btnCheckStatus = document.getElementById('btnCheckStatus');
            const btnLogout = document.getElementById('btnLogout');
            const alertSuccess = document.getElementById('alertSuccess');
            const alertError = document.getElementById('alertError');
            const alertSuccessText = document.getElementById('alertSuccessText');
            const alertErrorText = document.getElementById('alertErrorText');
            const countdownEl = document.getElementById('countdown');

            let cooldown = 0;
            let cooldownInterval = null;

            function showAlert(type, message) {
                alertSuccess.style.display = 'none';
                alertError.style.display = 'none';

                if (type === 'success') {
                    alertSuccessText.textContent = message;
                    alertSuccess.style.display = 'flex';
                } else {
                    alertErrorText.textContent = message;
                    alertError.style.display = 'flex';
                }

                // Auto hide after 8 seconds
                setTimeout(() => {
                    alertSuccess.style.display = 'none';
                    alertError.style.display = 'none';
                }, 8000);
            }

            function startCooldown(seconds) {
                cooldown = seconds;
                btnResend.disabled = true;
                countdownEl.style.display = 'block';

                if (cooldownInterval) clearInterval(cooldownInterval);

                cooldownInterval = setInterval(() => {
                    cooldown--;
                    if (cooldown <= 0) {
                        clearInterval(cooldownInterval);
                        btnResend.disabled = false;
                        countdownEl.style.display = 'none';
                        btnResend.innerHTML =
                            '<i class="fa-solid fa-paper-plane"></i> Kirim Ulang Email Verifikasi';
                    } else {
                        countdownEl.textContent = `Dapat mengirim ulang dalam ${cooldown} detik`;
                        btnResend.innerHTML = `<i class="fa-solid fa-clock"></i> Tunggu ${cooldown}s`;
                    }
                }, 1000);
            }

            // Kirim ulang email verifikasi
            btnResend.addEventListener('click', async function() {
                if (btnResend.disabled) return;

                btnResend.disabled = true;
                btnResend.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengirim...';

                try {
                    const response = await fetch('/api/email/verification-notification', {
                        method: 'POST',
                        headers: TokenManager.getHeaders(),
                    });

                    const data = await response.json();

                    if (response.ok) {
                        if (data.verified) {
                            showAlert('success', 'Email sudah terverifikasi! Mengalihkan...');
                            // Update sessionStorage
                            const updatedUser = TokenManager.getUser();
                            if (updatedUser) {
                                updatedUser.email_verified_at = new Date().toISOString();
                                sessionStorage.setItem(TokenManager.STORAGE_USER, JSON.stringify(
                                    updatedUser));
                            }
                            setTimeout(() => TokenManager.redirectToDashboard(), 1500);
                        } else {
                            showAlert('success', data.message ||
                                'Email verifikasi telah dikirim ulang. Cek inbox Anda.');
                            startCooldown(60);
                        }
                    } else if (response.status === 429) {
                        showAlert('error', 'Terlalu banyak permintaan. Silakan tunggu beberapa saat.');
                        startCooldown(60);
                    } else {
                        showAlert('error', data.message || 'Gagal mengirim email verifikasi.');
                        btnResend.disabled = false;
                        btnResend.innerHTML =
                            '<i class="fa-solid fa-paper-plane"></i> Kirim Ulang Email Verifikasi';
                    }
                } catch (error) {
                    console.error('Resend error:', error);
                    showAlert('error', 'Terjadi kesalahan jaringan. Coba lagi.');
                    btnResend.disabled = false;
                    btnResend.innerHTML =
                        '<i class="fa-solid fa-paper-plane"></i> Kirim Ulang Email Verifikasi';
                }
            });

            // Cek status verifikasi
            btnCheckStatus.addEventListener('click', async function() {
                btnCheckStatus.disabled = true;
                btnCheckStatus.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memeriksa...';

                try {
                    const response = await fetch('/api/email/verification-status', {
                        method: 'GET',
                        headers: TokenManager.getHeaders(),
                    });

                    const data = await response.json();

                    if (response.ok && data.verified) {
                        showAlert('success', 'Email sudah terverifikasi! Mengalihkan ke dashboard...');
                        // Update sessionStorage
                        const updatedUser = TokenManager.getUser();
                        if (updatedUser) {
                            updatedUser.email_verified_at = data.email_verified_at || new Date()
                                .toISOString();
                            sessionStorage.setItem(TokenManager.STORAGE_USER, JSON.stringify(
                                updatedUser));
                        }
                        setTimeout(() => TokenManager.redirectToDashboard(), 1500);
                    } else {
                        showAlert('error', 'Email belum terverifikasi. Silakan cek inbox email Anda.');
                    }
                } catch (error) {
                    console.error('Check status error:', error);
                    showAlert('error', 'Gagal memeriksa status. Coba lagi.');
                } finally {
                    btnCheckStatus.disabled = false;
                    btnCheckStatus.innerHTML =
                        '<i class="fa-solid fa-circle-check"></i> Sudah Verifikasi';
                }
            });

            // Logout
            btnLogout.addEventListener('click', async function() {
                try {
                    await fetch('/api/logout', {
                        method: 'POST',
                        headers: TokenManager.getHeaders(),
                    });
                } catch (e) {
                    // ignore
                }
                TokenManager.clearAuth();
                window.location.href = '/login';
            });
        });
    </script>
</body>

</html>
