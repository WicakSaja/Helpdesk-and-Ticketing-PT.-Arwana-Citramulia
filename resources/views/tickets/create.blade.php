@extends('layouts.requester')
@section('title', 'Buat Tiket Baru')

@section('css')
    @vite(['resources/css/create-ticket.css'])
@endsection

@section('content')
    {{-- Card Container (Tengah) --}}
    <div class="card">
        <div class="card-header">
            <h2>Buat Tiket Kendala</h2>
            <p>Isi formulir di bawah ini untuk melaporkan masalah teknis Anda kepada tim Helpdesk.</p>
        </div>

        <div class="card-body">
            <form id="ticketCreateForm">
                <div class="form-group">
                    <label class="form-label">Subjek / Judul Masalah</label>
                    <input type="text" id="subject" name="subject" class="form-control"
                        placeholder="Contoh: Internet di Ruang Meeting Mati" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori Masalah</label>
                    <div style="position: relative;">
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            <option value="1">Hardware (Perangkat Keras)</option>
                            <option value="2">Software (Aplikasi/Windows)</option>
                            <option value="3">Network (Jaringan/Internet)</option>
                            <option value="4">Lainnya</option>
                        </select>
                        </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi Lengkap</label>
                    <textarea id="description" name="description" class="form-textarea"
                        placeholder="Jelaskan kronologi masalahnya secara detail. Contoh: Saat menyalakan PC, layar tetap hitam..." required></textarea>
                </div>

                <button type="submit" class="btn-submit" id="btnSubmitTicket">
                    <i class="fa-solid fa-paper-plane"></i> KIRIM TIKET SEKARANG
                </button>
            </form>
        </div>
    </div>

    <div id="createSuccessModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Tiket Berhasil Dibuat! 🎉</h3>
                <button class="btn-close" onclick="closeCreateModal()">&times;</button>
            </div>

            <div class="modal-content">
                <div class="detail-row">
                    <span class="detail-label">No. Tiket</span>
                    <span class="detail-value" id="cTicketNo" style="color:#d62828;">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Subjek</span>
                    <span class="detail-value" id="cSub">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Kategori</span>
                    <span class="detail-value" id="cCat">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Waktu</span>
                    <span class="detail-value" id="cTime">-</span>
                </div>
                
                <div class="detail-desc" id="cDesc">
                    </div>

                <div style="margin-top:25px; display:flex; gap:10px;">
                    <a href="{{ route('tickets.index') }}" 
                       onclick="try{sessionStorage.removeItem('last_created_ticket')}catch(e){}"
                       class="btn-submit" 
                       style="background:#1f2937; width:100%; text-decoration:none;">
                       <i class="fa-solid fa-list-check"></i> Lihat Riwayat
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/ticket-create.js') }}?v={{ time() }}"></script>
    <script>
        // Opsional: Script inline untuk modal close logic jika belum ada di js eksternal
        function closeCreateModal() {
            document.getElementById('createSuccessModal').style.display = 'none';
        }
        
        // Modal logic (show) biasanya dipanggil dari ticket-create.js setelah fetch sukses
    </script>
@endsection