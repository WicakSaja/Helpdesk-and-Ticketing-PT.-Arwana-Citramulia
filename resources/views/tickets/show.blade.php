<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tiket - Helpdesk Arwana</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/ticket-style.css'])


</head>

<body>

    <div class="top-bar">
        <div class="logo-area">
            <i class="fa-solid fa-headset"></i> Helpdesk
        </div>
        <a href="{{ route('tickets.index') }}" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="container container-lg">
        <div class="card">
            <div class="card-body">

                <div class="detail-header">
                    <div>
                        <div class="ticket-title">Install Ulang Windows HRD</div>
                        <div class="ticket-meta">
                            <span class="meta-item"><i class="fa-solid fa-hashtag"></i> TKT-UUID-003</span>
                            <span class="meta-item"><i class="fa-regular fa-clock"></i> 27 Jan 2026, 09:30</span>
                            <span class="meta-item"><i class="fa-solid fa-folder"></i> Software</span>
                        </div>
                    </div>
                    <div>
                        <span class="st-progress">In Progress</span>
                    </div>
                </div>

                <div class="chat-bubble">
                    <div class="chat-avatar">ME</div>
                    <div class="chat-content">
                        <div class="chat-name">Saya (User) <span class="chat-time">27 Jan 2026, 09:30</span></div>
                        <div class="chat-text">
                            Halo IT, tolong install ulang laptop HRD yang baru karena banyak aplikasi bawaan yang berat.
                            Password admin sudah saya reset standar. Terima kasih.
                        </div>
                    </div>
                </div>

                <hr class="divider">

                <div class="conversation-box">
                    <h4 class="section-title">Aktivitas Tiket</h4>

                    <div class="chat-bubble bubble-tech">
                        <div class="chat-avatar avatar-tech"><i class="fa-solid fa-user-gear"></i></div>
                        <div class="chat-content">
                            <div class="chat-name">Teknisi Support <span class="chat-time">27 Jan 2026, 09:45</span>
                            </div>
                            <div class="chat-text">
                                Siap pak, tiket sudah kami terima (Assigned). Teknisi akan segera ke ruangan HRD dalam
                                10 menit. Mohon disiapkan unitnya.
                            </div>
                        </div>
                    </div>

                    <div class="chat-bubble bubble-tech">
                        <div class="chat-avatar avatar-tech"><i class="fa-solid fa-user-gear"></i></div>
                        <div class="chat-content">
                            <div class="chat-name">Teknisi Support <span class="chat-time">27 Jan 2026, 10:15</span>
                            </div>
                            <div class="chat-text">
                                Update: Status diubah menjadi <b>In Progress</b>. Sedang proses backup data sebelum
                                install ulang. Estimasi selesai jam 1 siang nanti.
                            </div>
                        </div>
                    </div>

                </div>

                <div class="reply-area">
                    <textarea class="reply-box" placeholder="Ketik balasan Anda di sini jika ada info tambahan..."></textarea>
                    <button class="btn-reply">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Balasan
                    </button>
                </div>

            </div>
        </div>
    </div>

</body>

</html>
