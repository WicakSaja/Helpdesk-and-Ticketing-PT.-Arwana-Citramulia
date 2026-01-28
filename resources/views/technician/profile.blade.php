@extends('layouts.technician')
@section('title', 'Profil Teknisi')

@section('css')
<style>
    /* Layout Utama */
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .page-title { font-size: 26px; font-weight: 700; color: #333; margin: 0; }
    
    .profile-container { display: grid; grid-template-columns: 320px 1fr; gap: 30px; align-items: start; }

    /* CARD KIRI (FOTO) */
    .profile-card { background: white; padding: 40px 30px; border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); text-align: center; }
    .avatar-wrapper { width: 140px; height: 140px; margin: 0 auto 25px; position: relative; }
    .avatar-img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 5px solid #fff0f0; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    
    .user-name { font-size: 20px; font-weight: 700; color: #333; margin-bottom: 8px; }
    /* Role tetap hijau biar identitas teknisi, tapi soft */
    .user-role { font-size: 13px; color: #2e7d32; background: #e8f5e9; padding: 6px 15px; border-radius: 20px; display: inline-block; font-weight: 600; }

    /* CARD KANAN (FORM) */
    .settings-card { background: white; padding: 35px; border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); }
    .section-title { font-size: 16px; font-weight: 700; color: #333; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; gap: 10px; }
    
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px; }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 10px; }
    .form-input { width: 100%; padding: 12px 15px; border: 1px solid #eee; border-radius: 10px; font-size: 14px; outline: none; transition: 0.3s; color: #333; background: #fcfcfc; }
    .form-input:focus { border-color: #d62828; background: white; box-shadow: 0 0 0 4px rgba(214, 40, 40, 0.05); }
    .form-input:disabled { background: #f8f9fa; color: #999; cursor: not-allowed; border-color: #eee; }
    
    .password-wrapper { position: relative; }
    .password-wrapper input { padding-right: 45px; }
    .toggle-password { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #bbb; transition: 0.3s; }
    .toggle-password:hover { color: #d62828; }
    
    /* Tombol Merah (Konsisten) */
    .btn-save { background: #d62828; color: white; padding: 12px 30px; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.3s; display: inline-flex; align-items: center; gap: 10px; float: right; }
    .btn-save:hover { background: #b01f1f; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(214, 40, 40, 0.2); }
</style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Profil Teknisi</h1>
    </div>

    <div class="profile-container">
        
        <div class="profile-card">
            <div class="avatar-wrapper">
                <img src="https://ui-avatars.com/api/?name=Teknisi+Andi&background=d62828&color=fff&size=256" alt="Avatar" class="avatar-img">
            </div>
            <h3 class="user-name">Teknisi Andi</h3>
            <span class="user-role">Mechanical Support</span>

            <div style="margin-top: 30px; background: #f8f9fa; padding: 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
                
                <div style="text-align: left;">
                    <small style="color: #999; font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">STATUS</small>
                    <div style="font-size: 13px; color: #2e7d32; font-weight: 700; margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-circle" style="font-size: 8px;"></i> Available
                    </div>
                </div>

                <div style="width: 1px; height: 30px; background: #ddd;"></div>

                <div style="text-align: right;">
                    <small style="color: #999; font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">SELESAI BULAN INI</small>
                    <div style="font-size: 13px; color: #333; font-weight: 700; margin-top: 4px;">
                        45 Tiket
                    </div>
                </div>

            </div>
        </div>

        <div class="settings-card">
            <form>
                <div class="section-title"><i class="fa-regular fa-id-card" style="color:#d62828;"></i> Informasi Pribadi</div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-input" value="Andi Pratama" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIK / ID</label>
                        <input type="text" class="form-input" value="TECH-005" disabled>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Email Perusahaan</label>
                        <input type="email" class="form-input" value="andi.tech@arwanacitra.com" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Divisi / Spesialisasi</label>
                        <input type="text" class="form-input" value="Maintenance - Mechanical" disabled>
                    </div>
                </div>

                <div class="section-title" style="margin-top: 40px;"><i class="fa-solid fa-shield-halved" style="color:#d62828;"></i> Keamanan Akun</div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-input" id="new_pass" placeholder="Biarkan kosong jika tidak diubah">
                            <span class="toggle-password" onclick="togglePass('new_pass', this)"><i class="fa-regular fa-eye"></i></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-input" id="conf_pass" placeholder="Ulangi password baru">
                            <span class="toggle-password" onclick="togglePass('conf_pass', this)"><i class="fa-regular fa-eye"></i></span>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 20px; overflow: hidden;">
                    <button type="button" class="btn-save">
                        <i class="fa-solid fa-check"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection

@section('scripts')
<script>
    function togglePass(inputId, iconElement) {
        const input = document.getElementById(inputId);
        const icon = iconElement.querySelector('i');
        if (input.type === "password") {
            input.type = "text"; 
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = "password"; 
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
@endsection