<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tiket - Helpdesk Arwana</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/ticket-style.css'])

    <style>
        /* Layout Detail */
        .detail-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .ticket-title { font-size: 20px; font-weight: 700; color: #333; margin-bottom: 5px; }
        .ticket-meta { font-size: 13px; color: #777; display: flex; gap: 15px; align-items: center; }
        .meta-item { display: flex; align-items: center; gap: 5px; }

        /* Status Badge (Pinjam style dari index) */
        .st-progress { background: #fff3e0; color: #e65100; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid #ffe0b2; }

        /* Conversation Area */
        .conversation-box { background: #f8f9fa; border-radius: 12px; padding: 20px; border: 1px solid #eee; }
        
        .chat-bubble { margin-bottom: 20px; display: flex; gap: 15px; }
        .chat-avatar {
            width: 40px; height: 40px; background: #ddd; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; font-weight: bold; color: #555;
        }
        .avatar-tech { background: #d62828; color: white; } /* Teknisi Merah */
        
        .chat-content { flex: 1; }
        .chat-name { font-weight: 600; font-size: 14px; color: #333; margin-bottom: 2px; }
        .chat-time { font-size: 11px; color: #999; margin-left: 5px; font-weight: 400; }
        
        .chat-text {
            background: white; padding: 15px; border-radius: 0 12px 12px 12px;
            border: 1px solid #eee; color: #444; font-size: 14px; line-height: 1.5;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        
        /* Balasan Teknisi (Align Right / Beda warna dikit) */
        .bubble-tech .chat-text {
            background: #fff5f5; border: 1px solid #ffcdd2;
        }

        /* Form Balasan */
        .reply-area { margin-top: 30px; }
        .reply-box { width: 100%; border: 1px solid #ddd; border-radius: 8px; padding: 15px; font-family: 'Poppins'; font-size: 14px; min-height: 100px; resize: vertical; outline: none; }
        .reply-box:focus { border-color: #d62828; }
        
        .btn-reply {
            background: #d62828; color: white; border: none; padding: 10px 25px;
            border-radius: 6px; font-weight: 600; cursor: pointer; margin-top: 10px;
            display: inline-flex; align-items: center; gap: 8px;
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <div class="logo-area">
            <i class="fa-solid fa-headset"></i> Helpdesk
        </div>
        <a href="{{ route('tickets.index') }}" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="container" style="max-width: 900px;">
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
                            Halo IT, tolong install ulang laptop HRD yang baru karena banyak aplikasi bawaan yang berat. Password admin sudah saya reset standar. Terima kasih.
                        </div>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">

                <div class="conversation-box">
                    <h4 style="margin-bottom: 20px; color:#555;">Aktivitas Tiket</h4>

                    <div class="chat-bubble bubble-tech">
                        <div class="chat-avatar avatar-tech"><i class="fa-solid fa-user-gear"></i></div>
                        <div class="chat-content">
                            <div class="chat-name">Teknisi Support <span class="chat-time">27 Jan 2026, 09:45</span></div>
                            <div class="chat-text">
                                Siap pak, tiket sudah kami terima (Assigned). Teknisi akan segera ke ruangan HRD dalam 10 menit. Mohon disiapkan unitnya.
                            </div>
                        </div>
                    </div>

                    <div class="chat-bubble bubble-tech">
                        <div class="chat-avatar avatar-tech"><i class="fa-solid fa-user-gear"></i></div>
                        <div class="chat-content">
                            <div class="chat-name">Teknisi Support <span class="chat-time">27 Jan 2026, 10:15</span></div>
                            <div class="chat-text">
                                Update: Status diubah menjadi <b>In Progress</b>. Sedang proses backup data sebelum install ulang. Estimasi selesai jam 1 siang nanti.
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