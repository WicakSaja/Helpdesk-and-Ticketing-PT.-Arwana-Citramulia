<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Helpdesk Arwana</title>

    @vite(['resources/css/auth-style.css'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="main-wrapper">
        <div class="auth-card">
            @yield('content')
        </div>
    </div>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#d62828',
                timer: 3000
            });
        @endif

        @if ($errors->any())
            var pesanError = '';
            @foreach ($errors->all() as $error)
                pesanError += '• {{ $error }}\n';
            @endforeach
            Swal.fire({
                icon: 'error',
                title: 'Gagal Masuk',
                text: pesanError,
                confirmButtonColor: '#d62828'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                confirmButtonColor: '#d62828'
            });
        @endif

        function togglePassword(inputId, iconElement) {
            const input = document.getElementById(inputId);
            const icon = iconElement.querySelector('i');

            if (input.type === "password") {
                input.type = "text"; // Tampilkan password
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash'); // Ganti ikon jadi mata dicoret
            } else {
                input.type = "password"; // Sembunyikan lagi (jadi bulat-bulat)
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye'); // Ganti ikon jadi mata biasa
            }
        }
    </script>

</body>

</html>
