@extends('layouts.requester')
@section('title', 'Profil Saya')

@section('css')
    @vite(['resources/css/profile.css'])
    <style>
        @yield('css')
    </style>
@endsection

@section('content')
    <div class="page-title">Pengaturan Profil</div>

    <div class="profile-container">
        <div class="profile-card">
            <div class="avatar-wrapper">
                <img id="profile_avatar"
                    src=""
                    alt="Avatar" class="avatar-img">
            </div>
            <h3 id="profile_name_display" class="user-name">Loading...</h3>
            <span id="profile_role"
                class="user-role">-</span>
            <div class="profile-stats">
                <div class="stat-item">
                    <h4 id="profile_ticket_count">0</h4>
                    <span>Total Tiket</span>
                </div>
            </div>
        </div>

        <div class="settings-card">
            <form>
                <h4 class="form-section-title"><i class="fa-solid fa-id-card" style="margin-right:8px; color:#d62828;"></i>
                    Informasi Pribadi</h4>
                <div class="form-grid">
                    <div class="form-group"><label class="form-label">Nama Lengkap</label><input type="text"
                            id="profile_name" name="profile_name" class="form-input"
                            disabled></div>
                    <div class="form-group"><label class="form-label">Nomor Telepon</label><input type="text"
                            id="profile_phone" name="profile_phone" class="form-input"
                            disabled></div>
                </div>
                <div class="form-group"><label class="form-label">Email</label><input type="email" id="profile_email"
                        name="profile_email" class="form-input"
                        disabled></div>

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

        // Load profile data from sessionStorage
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                const authUserJson = sessionStorage.getItem('auth_user');
                const authRolesJson = sessionStorage.getItem('auth_roles');
                
                if (!authUserJson) {
                    console.warn('No auth_user found in sessionStorage');
                    return;
                }

                const user = JSON.parse(authUserJson);
                const roles = authRolesJson ? JSON.parse(authRolesJson) : [];
                
                // Update profile display
                const nameEl = document.getElementById('profile_name_display');
                const roleEl = document.getElementById('profile_role');
                const avatarImg = document.getElementById('profile_avatar');
                const nameInput = document.getElementById('profile_name');
                const phoneInput = document.getElementById('profile_phone');
                const emailInput = document.getElementById('profile_email');
                const ticketCountEl = document.getElementById('profile_ticket_count');

                if (nameEl) nameEl.innerText = user.name || 'User';
                if (roleEl) roleEl.innerText = (roles && roles.length > 0) ? roles[0] : 'Requester';
                if (avatarImg) avatarImg.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name || 'User')}&background=d62828&color=fff&size=200`;
                if (nameInput) nameInput.value = user.name || '';
                if (phoneInput) phoneInput.value = user.phone || '';
                if (emailInput) emailInput.value = user.email || '';

                // Fetch ticket count from API
                const ticketsRes = await fetch(API_URL + '/api/my-tickets', {
                    headers: (typeof TokenManager !== 'undefined' && typeof TokenManager.getHeaders === 'function') 
                        ? TokenManager.getHeaders() 
                        : {'Content-Type': 'application/json'}
                });
                if (ticketsRes.ok) {
                    const ticketsJson = await ticketsRes.json();
                    const items = ticketsJson.data || (Array.isArray(ticketsJson) ? ticketsJson : []);
                    if (ticketCountEl) ticketCountEl.innerText = items.length || 0;
                }

                // Attach handler for password change (friendly messages if backend not available)
                const saveBtn = document.querySelector('.btn-save');
                if (saveBtn) {
                    saveBtn.addEventListener('click', async function() {
                        const pass = (document.getElementById('new_pass') || {}).value || '';
                        const conf = (document.getElementById('conf_pass') || {}).value || '';

                        const showMsg = (type, title, text) => {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: type,
                                    title: title,
                                    text: text,
                                    confirmButtonColor: '#d62828'
                                });
                                return;
                            }
                            alert(title + ': ' + text);
                        };

                        if (!pass || pass.length < 8) return showMsg('error', 'Error',
                            'Password baru minimal 8 karakter');
                        if (pass !== conf) return showMsg('error', 'Error',
                            'Konfirmasi password tidak cocok');

                        try {
                            const headers2 = (typeof TokenManager !== 'undefined' &&
                                    typeof TokenManager.getHeaders === 'function') ? TokenManager
                                .getHeaders() : {
                                    'Content-Type': 'application/json'
                                };
                            const res = await fetch(API_URL + '/api/change-password', {
                                method: 'POST',
                                headers: headers2,
                                body: JSON.stringify({
                                    password: pass,
                                    password_confirmation: conf
                                })
                            });

                            if (res.status === 404) {
                                return showMsg('info', 'Belum Tersedia',
                                    'Fitur ubah password belum diaktifkan di server. Silakan hubungi administrator.'
                                    );
                            }
                            if (res.status === 401) {
                                return showMsg('warning', 'Belum Terautentikasi',
                                    'Silakan login ulang.');
                            }
                            if (res.status === 403) {
                                return showMsg('warning', 'Tidak Diizinkan',
                                    'Anda tidak memiliki izin untuk melakukan perubahan ini.');
                            }
                            if (res.status === 422) {
                                let json = {};
                                try {
                                    json = await res.json();
                                } catch (e) {}
                                const msg = (json.errors) ? Object.values(json.errors).flat().join(
                                    ' ') : (json.message || 'Validasi gagal');
                                return showMsg('error', 'Validasi', msg);
                            }
                            if (!res.ok) {
                                let json = {};
                                try {
                                    json = await res.json();
                                } catch (e) {}
                                return showMsg('error', 'Error', json.message ||
                                    'Terjadi kesalahan');
                            }

                            showMsg('success', 'Sukses', 'Password berhasil diubah');
                            if (document.getElementById('new_pass')) document.getElementById(
                                'new_pass').value = '';
                            if (document.getElementById('conf_pass')) document.getElementById(
                                'conf_pass').value = '';
                        } catch (err) {
                            console.error('change password', err);
                            showMsg('error', 'Error',
                            'Gagal menghubungi server. Cek koneksi Anda.');
                        }
                    });
                }
            } catch (err) {
                console.warn('Profile load failed', err);
            }
        });
    </script>
@endsection
