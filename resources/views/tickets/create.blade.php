@extends('layouts.requester')
@section('title', 'Buat Tiket Baru')

@section('css')
<style>
    /* CSS Khusus Form */
    .card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); max-width: 800px; margin: 0 auto; }
    .card-header h2 { font-size: 24px; font-weight: 700; color: #333; margin-bottom: 5px; }
    .card-header p { color: #777; font-size: 14px; margin-bottom: 25px; }
    
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-weight: 600; font-size: 14px; color: #555; margin-bottom: 8px; }
    
    .form-control, .form-select, .form-textarea { 
        width: 100%; padding: 12px 15px; border: 1px solid #ddd; 
        border-radius: 10px; font-size: 14px; outline: none; transition: 0.3s; 
        background-color: #fcfcfc;
    }
    .form-textarea { height: 120px; resize: none; }
    
    .form-control:focus, .form-select:focus, .form-textarea:focus { 
        border-color: #d62828; background-color: white;
        box-shadow: 0 0 0 4px rgba(214, 40, 40, 0.05); 
    }
    
    .btn-submit { 
        background: #d62828; color: white; padding: 14px 30px; 
        border: none; border-radius: 10px; font-weight: 600; cursor: pointer; 
        transition: 0.3s; display: flex; align-items: center; gap: 10px; margin-top: 20px;
        width: 100%; justify-content: center; font-size: 16px;
        box-shadow: 0 5px 15px rgba(214, 40, 40, 0.2);
    }
    .btn-submit:hover { background: #b01f1f; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(214, 40, 40, 0.3); }
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
                    <input type="text" id="subject" name="subject" class="form-control" placeholder="Contoh: Internet di Ruang Meeting Mati" required>
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
                    <textarea id="description" name="description" class="form-textarea" placeholder="Jelaskan detail kronologi masalahnya..." required></textarea>
                </div>

                <button type="submit" class="btn-submit" id="btnSubmitTicket">
                    <i class="fa-solid fa-paper-plane"></i> KIRIM TIKET SEKARANG
                </button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const API_URL = 'http://127.0.0.1:8000';
    </script>
    <script src="{{ asset('js/auth-token-manager.js') }}"></script>
    <script src="{{ asset('js/role-protection.js') }}"></script>
    <script src="{{ asset('js/page-protection.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            requireRequesterRole();

            const form = document.getElementById('ticketCreateForm');
            const btn = document.getElementById('btnSubmitTicket');

            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                const subject = document.getElementById('subject').value.trim();
                const description = document.getElementById('description').value.trim();
                const categoryId = document.getElementById('category_id').value;

                if (!subject || !description || !categoryId) {
                    showAlert('error', 'Validasi Gagal', 'Semua field harus diisi.');
                    return;
                }

                setButtonLoading(btn, true);

                try {
                    const response = await fetch(`${API_URL}/api/tickets`, {
                        method: 'POST',
                        headers: TokenManager.getHeaders(),
                        body: JSON.stringify({
                            subject: subject,
                            description: description,
                            category_id: Number(categoryId),
                            channel: 'web'
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showAlert('success', 'Berhasil', 'Tiket berhasil dibuat. Mengalihkan...');
                        setTimeout(() => {
                            window.location.href = "{{ route('tickets.index') }}";
                        }, 1200);
                    } else {
                        const errorMsg = data.message || 'Gagal membuat tiket.';
                        showAlert('error', 'Gagal', errorMsg);
                    }
                } catch (error) {
                    showAlert('error', 'Error', error.message || 'Tidak dapat menghubungi server API');
                } finally {
                    setButtonLoading(btn, false);
                }
            });
        });

        function setButtonLoading(button, isLoading) {
            if (!button) return;
            if (isLoading) {
                button.disabled = true;
                button.dataset.originalText = button.innerHTML;
                button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengirim...';
            } else {
                button.disabled = false;
                button.innerHTML = button.dataset.originalText || button.innerHTML;
            }
        }

        function showAlert(type, title, text) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: type,
                    title: title,
                    text: text,
                    confirmButtonColor: '#d62828'
                });
                return;
            }
            alert(`${title}: ${text}`);
        }
    </script>
@endsection