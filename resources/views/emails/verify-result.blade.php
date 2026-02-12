<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hasil Verifikasi - Arwana Helpdesk</title>
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

        /* --- MAIN CONTAINER --- */
        .result-container {
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

        /* --- CARD --- */
        .result-card {
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
        .result-icon-section {
            flex-shrink: 0;
            text-align: center;
        }

        .result-icon {
            width: 140px;
            height: 140px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .result-icon.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .result-icon.error {
            background: linear-gradient(135deg, #d62828 0%, #ff6b6b 100%);
            color: white;
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
        .result-content {
            flex: 1;
            min-width: 0;
        }

        .result-title {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .result-message {
            font-size: 16px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .result-details {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .detail-item {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
            font-size: 14px;
            color: #475569;
        }

        .detail-item:last-child {
            margin-bottom: 0;
        }

        .detail-item i {
            color: #10b981;
            font-size: 16px;
            min-width: 20px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        /* Success detail items - hijau */
        .result-details.success .detail-item i {
            color: #10b981;
        }

        /* Error detail items - merah */
        .result-details.error .detail-item i {
            color: #d62828;
        }

        /* --- BUTTONS --- */
        .button-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 32px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-primary {
            background: linear-gradient(135deg, #d62828 0%, #ff6b6b 100%);
            color: white;
            box-shadow: 0 10px 25px rgba(214, 40, 40, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(214, 40, 40, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #d62828;
            border: 2px solid #d62828;
        }

        .btn-secondary:hover {
            background: #fff5f5;
            transform: translateY(-2px);
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
            .result-card {
                flex-direction: column;
                padding: 40px 25px;
                gap: 30px;
                text-align: center;
            }

            .result-icon-section {
                margin: 0 auto;
            }

            .result-content {
                text-align: center;
            }

            .result-title {
                font-size: 24px;
            }

            .result-message {
                font-size: 14px;
            }

            .button-group {
                justify-content: center;
            }

            .btn {
                flex: 1;
                min-width: 150px;
            }

            .result-icon {
                width: 100px;
                height: 100px;
                font-size: 48px;
            }
        }

        @media (max-width: 480px) {
            .result-container {
                padding: 10px;
            }

            .result-card {
                padding: 30px 20px;
                gap: 20px;
            }

            .result-title {
                font-size: 20px;
            }

            .result-message {
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
    <div class="result-container">
        <div class="result-card">
            <!-- Icon Section -->
            <div class="result-icon-section">
                <div class="result-icon" id="resultIcon"
                    style="background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%); color: white;">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                </div>
                <div class="logo-brand">Arwana Helpdesk</div>
            </div>

            <!-- Content Section -->
            <div class="result-content">
                <h2 class="result-title" id="resultTitle">Memverifikasi Email...</h2>

                <p class="result-message" id="resultMessage">
                    Mohon tunggu, kami sedang memverifikasi alamat email Anda.
                </p>

                <div class="result-details" id="resultDetails">
                    <div class="detail-item">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                        <span>Sedang menghubungi server...</span>
                    </div>
                </div>

                <div class="button-group" id="buttonGroup" style="display: none;">
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            const params = new URLSearchParams(window.location.search);
            const verifyUrl = params.get('verify_url');

            const resultIcon = document.getElementById('resultIcon');
            const resultTitle = document.getElementById('resultTitle');
            const resultMessage = document.getElementById('resultMessage');
            const resultDetails = document.getElementById('resultDetails');
            const buttonGroup = document.getElementById('buttonGroup');

            if (!verifyUrl) {
                showError(
                    'Link Tidak Valid',
                    'URL verifikasi tidak ditemukan. Pastikan Anda menggunakan link yang benar dari email.',
                    [{
                            icon: 'fa-solid fa-exclamation-triangle',
                            text: 'Parameter verifikasi tidak ditemukan di URL'
                        },
                        {
                            icon: 'fa-solid fa-envelope',
                            text: 'Pastikan klik link langsung dari email verifikasi'
                        },
                    ]
                );
                return;
            }

            try {
                const response = await fetch(verifyUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (response.ok && data.verified) {
                    showSuccess(data.message);
                } else {
                    showError(
                        'Verifikasi Gagal',
                        data.message || 'Tautan verifikasi tidak valid atau sudah kedaluwarsa.',
                        [{
                                icon: 'fa-solid fa-exclamation-circle',
                                text: data.message || 'Verifikasi gagal'
                            },
                            {
                                icon: 'fa-solid fa-clock',
                                text: 'Tautan verifikasi memiliki masa berlaku terbatas'
                            },
                            {
                                icon: 'fa-solid fa-redo',
                                text: 'Silakan minta tautan verifikasi baru'
                            },
                        ]
                    );
                }
            } catch (error) {
                console.error('Verification error:', error);
                showError(
                    'Terjadi Kesalahan',
                    'Tidak dapat menghubungi server. Periksa koneksi internet Anda dan coba lagi.',
                    [{
                            icon: 'fa-solid fa-wifi',
                            text: 'Periksa koneksi internet Anda'
                        },
                        {
                            icon: 'fa-solid fa-redo',
                            text: 'Coba muat ulang halaman ini'
                        },
                    ]
                );
            }

            function showSuccess(message) {
                resultIcon.removeAttribute('style');
                resultIcon.className = 'result-icon success';
                resultIcon.innerHTML = '<i class="fa-solid fa-check"></i>';
                resultTitle.textContent = 'Email Terverifikasi!';
                resultMessage.textContent = message ||
                    'Terima kasih telah memverifikasi email Anda. Akun Anda sekarang sudah aktif dan siap digunakan.';

                resultDetails.className = 'result-details success';
                resultDetails.innerHTML = `
                    <div class="detail-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Email berhasil diverifikasi</span>
                    </div>
                    <div class="detail-item">
                        <i class="fa-solid fa-lock"></i>
                        <span>Akun Anda sekarang aman dan terlindungi</span>
                    </div>
                    <div class="detail-item">
                        <i class="fa-solid fa-arrow-right"></i>
                        <span>Anda dapat langsung menggunakan semua fitur</span>
                    </div>
                `;

                buttonGroup.style.display = 'flex';
                buttonGroup.innerHTML = `
                    <a href="{{ route('home') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Login Sekarang
                    </a>
                `;
            }

            function showError(title, message, details) {
                resultIcon.removeAttribute('style');
                resultIcon.className = 'result-icon error';
                resultIcon.innerHTML = '<i class="fa-solid fa-times"></i>';
                resultTitle.textContent = title;
                resultMessage.textContent = message;

                resultDetails.className = 'result-details error';
                resultDetails.innerHTML = details.map(d => `
                    <div class="detail-item">
                        <i class="${d.icon}"></i>
                        <span>${d.text}</span>
                    </div>
                `).join('');

                buttonGroup.style.display = 'flex';
                buttonGroup.innerHTML = `
                    <a href="{{ route('home') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Login & Kirim Ulang
                    </a>
                `;
            }
        });
    </script>
</body>

</html>
