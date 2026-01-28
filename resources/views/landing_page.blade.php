<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Arwana Helpdesk</title>

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
            /* Pattern Background Halus */
            background-image: radial-gradient(#d1d1d1 1px, transparent 1px);
            background-size: 24px 24px;
            padding: 20px;
        }

        a {
            text-decoration: none;
        }

        /* --- CARD CONTAINER (Satu Kotak untuk Semua) --- */
        .landing-card {
            background: white;
            width: 100%;
            max-width: 480px;
            /* Ukuran pas, tidak terlalu lebar */
            padding: 50px 40px;
            border-radius: 24px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0, 0, 0, 0.02);
            position: relative;
            overflow: hidden;
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
            background: white;
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
            &copy; 2026 IT Department - Arwana Plant 5
        </div>
    </div>

</body>

</html>
