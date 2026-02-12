<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Arwana Helpdesk</title>
    <link rel="icon" type="image/png" href="{{ asset('images/arwanamerah.jpg') }}">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- GLOBAL STYLE --- */
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

            /* Abstract Background Pattern */
            background:
                /* Floating circles */
                radial-gradient(circle at 10% 20%, rgba(214, 40, 40, 0.18) 0%, transparent 50%),
                radial-gradient(circle at 90% 80%, rgba(214, 40, 40, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(255, 107, 107, 0.1) 0%, transparent 60%),
                radial-gradient(circle at 30% 70%, rgba(214, 40, 40, 0.12) 0%, transparent 35%),
                /* Geometric dots pattern */
                radial-gradient(circle, rgba(214, 40, 40, 0.25) 1.5px, transparent 1.5px),
                /* Base gradient */
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
                transform: translate(-25px, 25px) scale(1.08);
            }
        }

        a {
            text-decoration: none;
        }

        /* --- CARD CONTAINER (Satu Kotak untuk Semua) --- */
        .landing-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            width: 100%;
            max-width: 480px;
            /* Ukuran pas, tidak terlalu lebar */
            padding: 50px 40px;
            border-radius: 24px;
            text-align: center;
            box-shadow:
                0 25px 80px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.6),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
            z-index: 1;
            animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        /* Hiasan Garis Merah di Atas */
        .landing-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #d62828, #ff6b6b);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- LOGO & HEADER --- */
        .logo-img {
            width: 140px;
            margin-bottom: 25px;
            filter: drop-shadow(0 5px 10px rgba(0, 0, 0, 0.05));
        }

        .app-title {
            font-size: 22px;
            font-weight: 800;
            color: #333;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .app-desc {
            font-size: 14px;
            color: #666;
            margin-bottom: 35px;
            line-height: 1.6;
        }

        /* --- ACTION BUTTONS (Login & Register) --- */
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .btn-primary {
            background: #d62828;
            color: white;
            padding: 14px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            transition: 0.3s;
            box-shadow: 0 8px 20px rgba(214, 40, 40, 0.25);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-primary:hover {
            background: #b01f1f;
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(214, 40, 40, 0.35);
        }

        .btn-outline {
            background: white;
            color: #555;
            padding: 14px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            border: 2px solid #f0f0f0;
            transition: 0.3s;
        }

        .btn-outline:hover {
            border-color: #d62828;
            color: #d62828;
            background: #fff5f5;
        }

        /* --- FOOTER FEATURES --- */
        .features-divider {
            margin: 35px 0 25px;
            border-top: 1px solid #eee;
            position: relative;
        }

        .features-divider span {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.95);
            padding: 0 10px;
            font-size: 11px;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .features-list {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .feature-item {
            font-size: 11px;
            color: #777;
            background: #f8f9fa;
            padding: 6px 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .feature-item i {
            color: #d62828;
        }

        .copyright {
            margin-top: 30px;
            font-size: 11px;
            color: #bbb;
        }

        /* ============================================= */
        /* RESPONSIVE MEDIA QUERIES */
        /* ============================================= */

        /* Tablet Portrait (768px and below) */
        @media screen and (max-width: 768px) {
            .landing-card {
                padding: 45px 35px;
                max-width: 420px;
                border-radius: 22px;
            }

            .logo-img {
                width: 130px;
            }

            .app-title {
                font-size: 20px;
            }

            .app-desc {
                font-size: 13px;
                margin-bottom: 30px;
            }

            body::before {
                width: 350px;
                height: 350px;
                top: -120px;
                right: -120px;
            }

            body::after {
                width: 250px;
                height: 250px;
                bottom: -100px;
                left: -100px;
            }
        }

        /* Mobile Large (576px and below) */
        @media screen and (max-width: 576px) {
            body {
                padding: 15px;
            }

            .landing-card {
                padding: 35px 25px;
                border-radius: 18px;
                max-width: 100%;
            }

            .logo-img {
                width: 110px;
                margin-bottom: 20px;
            }

            .app-title {
                font-size: 18px;
                margin-bottom: 8px;
            }

            .app-desc {
                font-size: 12px;
                margin-bottom: 25px;
            }

            .btn-group {
                gap: 12px;
            }

            .btn-primary,
            .btn-outline {
                padding: 12px;
                font-size: 14px;
            }

            .features-divider {
                margin: 28px 0 20px;
            }

            .features-list {
                gap: 10px;
            }

            .feature-item {
                font-size: 10px;
                padding: 5px 10px;
            }

            .copyright {
                margin-top: 25px;
                font-size: 10px;
            }

            body::before {
                width: 250px;
                height: 250px;
                top: -100px;
                right: -100px;
            }

            body::after {
                width: 180px;
                height: 180px;
                bottom: -70px;
                left: -70px;
            }
        }

        /* Mobile Small (400px and below) */
        @media screen and (max-width: 400px) {
            body {
                padding: 10px;
            }

            .landing-card {
                padding: 30px 20px;
                border-radius: 16px;
            }

            .logo-img {
                width: 90px;
                margin-bottom: 15px;
            }

            .app-title {
                font-size: 16px;
            }

            .app-desc {
                font-size: 11px;
                margin-bottom: 20px;
            }

            .btn-primary,
            .btn-outline {
                padding: 11px;
                font-size: 13px;
                border-radius: 40px;
            }

            .features-list {
                gap: 8px;
            }

            .feature-item {
                font-size: 9px;
                padding: 4px 8px;
                border-radius: 6px;
            }

            body::before,
            body::after {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="landing-card">
        <img src="{{ asset('images/logo_arwana.png') }}" alt="Arwana Logo" class="logo-img">

        <h1 class="app-title">Integrated Helpdesk System</h1>
        <p class="app-desc">
            Selamat datang di Portal Layanan<br>
            <b>PT. Arwana Citramulia Tbk</b>.<br>
            Laporkan kendala operasional Anda di sini.
        </p>

        <div class="btn-group">
            <a href="{{ route('login') }}" class="btn-primary">
                <i class="fa-solid fa-right-to-bracket"></i> Login untuk Memulai
            </a>

            <a href="{{ route('register') }}" class="btn-outline">
                Buat Akun Baru
            </a>
        </div>

        <div class="features-divider"><span>Cakupan Layanan</span></div>

        <div class="features-list">
            <div class="feature-item"><i class="fa-solid fa-laptop-code"></i> IT Support</div>
            <div class="feature-item"><i class="fa-solid fa-gears"></i> Maintenance</div>
            <div class="feature-item"><i class="fa-solid fa-building"></i> Fasilitas</div>
        </div>

        <div class="copyright">
            &copy; 2026 IT Department - ITN Malang Internship Program
        </div>
    </div>

</body>

</html>
