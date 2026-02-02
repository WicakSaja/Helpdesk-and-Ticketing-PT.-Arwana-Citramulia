@extends('layouts.requester')
@section('title', 'Buat Tiket Baru')

@section('css')
    @vite(['resources/css/create-ticket.css'])

    <style>
        .card {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
            max-width: 800px;
            margin: 0 auto;
        }

        .card-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .card-header p {
            color: #777;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
            background-color: #fcfcfc;
        }

        .form-textarea {
            height: 120px;
            resize: none;
        }

        .form-control:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: #d62828;
            background-color: white;
            box-shadow: 0 0 0 4px rgba(214, 40, 40, 0.05);
        }

        .btn-submit {
            background: #d62828;
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            width: 100%;
            justify-content: center;
            font-size: 16px;
            box-shadow: 0 5px 15px rgba(214, 40, 40, 0.2);
        }

        .btn-submit:hover {
            background: #b01f1f;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(214, 40, 40, 0.3);
        }

        /* Modal styles (small subset to ensure modal looks correct on this page) */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(2px);
        }

        .modal-box {
            background: white;
            width: 500px;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.3s ease;
            position: relative;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #999;
            cursor: pointer;
        }

        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }

        .detail-label {
            width: 100px;
            color: #777;
            font-size: 13px;
            font-weight: 500;
        }

        .detail-value {
            flex: 1;
            color: #333;
            font-size: 14px;
            font-weight: 600;
        }

        .detail-desc {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 10px;
            font-size: 14px;
            color: #555;
            line-height: 1.6;
            margin-top: 10px;
            border: 1px solid #eee;
        }
    </style>

@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Buat Tiket Kendala</h2>
            <p>Isi formulir di bawah ini untuk melaporkan masalah teknis Anda.</p>
        </div>

        <div class="card-body">
            <form id="ticketCreateForm">
                <div class="form-group">
                    <label class="form-label">Subjek / Judul Masalah</label>
                    <input type="text" id="subject" name="subject" class="form-control"
                        placeholder="Contoh: Internet di Ruang Meeting Mati" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori Masalah</label>
                    <select id="category_id" name="category_id" class="form-select">
                        <option value="1">Hardware (Perangkat Keras)</option>
                        <option value="2">Software (Aplikasi/Windows)</option>
                        <option value="3">Network (Jaringan/Internet)</option>
                        <option value="4">Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi Lengkap</label>
                    <textarea id="description" name="description" class="form-textarea"
                        placeholder="Jelaskan detail kronologi masalahnya..." required></textarea>
                </div>

                <button type="submit" class="btn-submit" id="btnSubmitTicket">
                    <i class="fa-solid fa-paper-plane"></i> KIRIM TIKET SEKARANG
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Success After Create (aligned with Riwayat modal) -->
    <div id="createSuccessModal" class="modal-overlay" style="display:none;">
        <div class="modal-box" style="width:480px;">
            <div class="modal-header">
                <h3 style="font-size:18px; font-weight:700; margin:0;">Tiket Berhasil Dibuat</h3>
                <button class="btn-close" onclick="closeCreateModal()">&times;</button>
            </div>

            <div style="margin-top:10px;">
                <div class="detail-row"><span class="detail-label">No. Tiket</span><span class="detail-value"
                        id="cTicketNo">-</span></div>
                <div class="detail-row"><span class="detail-label">Subjek</span><span class="detail-value"
                        id="cSub">-</span></div>
                <div class="detail-row"><span class="detail-label">Kategori</span><span class="detail-value"
                        id="cCat">-</span></div>
                <div class="detail-row"><span class="detail-label">Waktu</span><span class="detail-value"
                        id="cTime">-</span></div>
                <div style="margin-top:10px;">
                    <div class="detail-desc" id="cDesc">-</div>
                </div>

                <div style="margin-top:20px; display:flex; gap:10px; justify-content:flex-end;">
                    <a href="{{ route('tickets.index') }}"
                        onclick="try{sessionStorage.removeItem('last_created_ticket')}catch(e){}" class="btn-submit"
                        style="background:#1565c0; width:auto; padding:10px 20px;">OK</a>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/auth-token-manager.js') }}"></script>
    <script src="{{ asset('js/role-protection.js') }}"></script>
    <script src="{{ asset('js/page-protection.js') }}"></script>
    <script src="{{ asset('js/ticket-create.js') }}?v={{ time() }}"></script>
@endsection
