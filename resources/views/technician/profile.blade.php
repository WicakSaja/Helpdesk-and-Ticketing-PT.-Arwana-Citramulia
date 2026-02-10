@extends('layouts.technician')
@section('title', 'Profil Teknisi')

@section('css')
    @vite(['resources/css/technician-profile.css'])
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Profil Teknisi</h1>
    </div>

    <div class="profile-container">

        <div class="profile-card">
            <div class="avatar-wrapper">
                <img id="profile_avatar" src="" alt="Avatar" class="avatar-img">
            </div>
            <h3 id="profile_name_display" class="user-name">Loading...</h3>
            <span id="profile_role" class="user-role">-</span>

            <div class="profile-stats">
                <div class="profile-stat-item">
                    <div class="profile-stat-label">STATUS</div>
                    <div class="profile-stat-value status-active">
                        <i class="fa-solid fa-circle" style="font-size: 8px;"></i>
                        <span id="profile_status">Loading...</span>
                    </div>
                </div>

                <div class="profile-stat-divider"></div>

                <div class="profile-stat-item">
                    <div class="profile-stat-label">SELESAI BULAN INI</div>
                    <div class="profile-stat-value" style="color: #333;">
                        <span id="profile_tickets_count">-</span> Tiket
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-card">
            <form>
                <div class="section-title"><i class="fa-regular fa-id-card"></i> Informasi Pribadi</div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-input" id="profile_name" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Divisi</label>
                        <input type="text" class="form-input" id="profile_dept" disabled>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-input" id="profile_email" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-input" id="profile_phone" disabled>
                    </div>
                </div>

                <div class="section-title" style="margin-top: 15px;"><i class="fa-solid fa-shield-halved"></i> Keamanan Akun
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Password Lama</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-input" id="old_pass" placeholder="Masukkan password lama">
                            <span class="toggle-password" onclick="togglePass('old_pass', this)"><i
                                    class="fa-regular fa-eye"></i></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-input" id="new_pass"
                                placeholder="Biarkan kosong jika tidak diubah">
                            <span class="toggle-password" onclick="togglePass('new_pass', this)"><i
                                    class="fa-regular fa-eye"></i></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-input" id="conf_pass" placeholder="Ulangi password baru">
                            <span class="toggle-password" onclick="togglePass('conf_pass', this)"><i
                                    class="fa-regular fa-eye"></i></span>
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
    <script src="{{ asset('js/technician-profile.js') }}?v={{ time() }}"></script>
@endsection
