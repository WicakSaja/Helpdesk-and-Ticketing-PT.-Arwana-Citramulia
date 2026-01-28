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
            <form action="{{ route('tickets.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Subjek / Judul Masalah</label>
                    <input type="text" name="subject" class="form-control" placeholder="Contoh: Internet di Ruang Meeting Mati" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori Masalah</label>
                    <select name="category_id" class="form-select">
                        <option value="1">Hardware (Perangkat Keras)</option>
                        <option value="2">Software (Aplikasi/Windows)</option>
                        <option value="3">Network (Jaringan/Internet)</option>
                        <option value="4">Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi Lengkap</label>
                    <textarea name="description" class="form-textarea" placeholder="Jelaskan detail kronologi masalahnya..." required></textarea>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-paper-plane"></i> KIRIM TIKET SEKARANG
                </button>
            </form>
        </div>
    </div>
@endsection