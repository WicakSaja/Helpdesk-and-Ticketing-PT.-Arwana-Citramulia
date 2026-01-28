@extends('layouts.requester')
@section('title', 'Profil Saya')

@section('css')
    <style>
        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 25px;
        }

        /* Layout Grid */
        .profile-container {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }

        /* Card Kiri (Foto) */
        .profile-card {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
            text-align: center;
            height: fit-content;
        }

        .avatar-wrapper {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
        }

        .avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff0f0;
        }

        .user-name {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .user-role {
            font-size: 13px;
            color: #777;
            background: #f4f6f9;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-block;
        }

        .profile-stats {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-around;
        }

        .stat-item h4 {
            font-size: 20px;
            color: #d62828;
            margin-bottom: 0;
        }

        .stat-item span {
            font-size: 12px;
            color: #888;
        }

        /* Card Kanan (Form) */
        .settings-card {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
        }

        .form-section-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
            color: #333;
        }

        .form-input:disabled {
            background: #f9f9f9;
            cursor: not-allowed;
        }

        /* Password Toggle */
        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 45px;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
        }

        .btn-save {
            background: #d62828;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            float: right;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-save:hover {
            background: #b01f1f;
        }
    </style>
@endsection

@section('content')
    <div class="page-title">Pengaturan Profil</div>

    <div class="profile-container">
        <div class="profile-card">
            <div class="avatar-wrapper">
                <img src="https://ui-avatars.com/api/?name=User+Requester&background=d62828&color=fff&size=200" alt="Avatar"
                    class="avatar-img">
            </div>
            <h3 class="user-name">User Requester</h3>
            <span class="user-role">Staff Produksi</span>
            <div class="profile-stats">
                <div class="stat-item">
                    <h4>12</h4><span>Total Tiket</span>
                </div>
                <div class="stat-item">
                    <h4>0</h4><span>Komplain</span>
                </div>
            </div>
        </div>

        <div class="settings-card">
            <form>
                <h4 class="form-section-title"><i class="fa-solid fa-id-card" style="margin-right:8px; color:#d62828;"></i>
                    Informasi Pribadi</h4>
                <div class="form-grid">
                    <div class="form-group"><label class="form-label">Nama Lengkap</label><input type="text"
                            class="form-input" value="User Requester" disabled></div>
                    <div class="form-group"><label class="form-label">NIK</label><input type="text" class="form-input"
                            value="12345678" disabled></div>
                </div>
                <div class="form-group"><label class="form-label">Email</label><input type="email" class="form-input"
                        value="user@arwanacitra.com" disabled></div>

                <h4 class="form-section-title" style="margin-top: 30px;"><i class="fa-solid fa-lock"
                        style="margin-right:8px; color:#d62828;"></i> Keamanan</h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-input" id="new_pass">
                            <span class="toggle-password" onclick="togglePass('new_pass', this)"><i
                                    class="fa-regular fa-eye"></i></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-input" id="conf_pass">
                            <span class="toggle-password" onclick="togglePass('conf_pass', this)"><i
                                    class="fa-regular fa-eye"></i></span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
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
