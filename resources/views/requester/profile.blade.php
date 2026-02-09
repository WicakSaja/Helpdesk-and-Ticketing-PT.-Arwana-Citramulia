@extends('layouts.requester')
@section('title', 'Profil Saya')

@section('css')
    {{-- CSS Eksternal --}}
    @vite(['resources/css/profile.css'])
@endsection

@section('content')
    <div class="page-title">Pengaturan Profil</div>

    <div class="profile-container">
        
        {{-- KARTU PROFIL (Layout Kiri-Kanan di Mobile) --}}
        <div class="profile-card">
            {{-- 1. AVATAR (Kiri) --}}
            <div class="avatar-wrapper">
                {{-- Default src kosong agar tidak ada gambar broken link saat loading --}}
                <img id="profile_avatar" src="" alt="Avatar" class="avatar-img" style="display: none;" onload="this.style.display='block'">
                <div id="avatar_loading" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#f0f0f0; border-radius:50%;">
                    <i class="fa-solid fa-spinner fa-spin" style="color:#ccc;"></i>
                </div>
            </div>

            {{-- 2. TEXT INFO (Kanan) --}}
            <div class="profile-details">
                {{-- Nama --}}
                <h3 id="profile_name_display" class="user-name">
                    <span style="font-size:14px; color:#999; font-weight:400;">Memuat...</span>
                </h3>
                
                {{-- Role (Default Spinner) --}}
                <span id="profile_role" class="user-role">
                    <i class="fa-solid fa-circle-notch fa-spin" style="font-size:10px;"></i>
                </span>
                
                {{-- Total Tiket (Default Spinner) --}}
                <div class="profile-stats">
                    <div class="stat-item">
                        <h4 id="profile_ticket_count">
                            <i class="fa-solid fa-spinner fa-spin" style="font-size:16px; color:#d62828;"></i>
                        </h4>
                        <span>Total Tiket</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM SETTINGS --}}
        <div class="settings-card">
            <form>
                <h4 class="form-section-title">
                    <i class="fa-solid fa-id-card" style="margin-right:10px; color:#d62828;"></i>
                    Informasi Pribadi
                </h4>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" id="profile_name" class="form-input" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" id="profile_phone" class="form-input" disabled>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Alamat Email</label>
                    <input type="email" id="profile_email" class="form-input" disabled>
                </div>

                <h4 class="form-section-title" style="margin-top: 30px;">
                    <i class="fa-solid fa-shield-halved" style="margin-right:10px; color:#d62828;"></i>
                    Keamanan
                </h4>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Password Lama</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-input" id="old_pass" placeholder="Masukkan password lama">
                            <span class="toggle-password" onclick="togglePass('old_pass', this)">
                                <i class="fa-regular fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-input" id="new_pass" placeholder="Minimal 8 karakter">
                            <span class="toggle-password" onclick="togglePass('new_pass', this)">
                                <i class="fa-regular fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-input" id="conf_pass" placeholder="Ulangi password">
                            <span class="toggle-password" onclick="togglePass('conf_pass', this)">
                                <i class="fa-regular fa-eye"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div style="overflow: hidden;">
                    <button type="button" class="btn-save">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
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

        document.addEventListener('DOMContentLoaded', async function() {
            try {
                // SETUP BASE URL
                let baseUrl = (typeof API_URL !== 'undefined') ? API_URL : '';
                baseUrl = baseUrl.replace(/\/$/, "");

                const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
                if (!token) return;

                const meRes = await fetch(`${baseUrl}/api/me`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!meRes.ok) {
                    console.warn('Gagal memuat data profil:', meRes.status);
                    return;
                }

                const meJson = await meRes.json();
                const user = meJson.user || {};
                const roles = meJson.roles || [];
                
                // 1. RENDER NAMA & FORM (Instan)
                const nameEl = document.getElementById('profile_name_display');
                if (nameEl) nameEl.innerText = user.name || 'User';
                
                document.getElementById('profile_name').value = user.name || '';
                document.getElementById('profile_phone').value = user.phone || '-';
                document.getElementById('profile_email').value = user.email || '';

                // 2. RENDER ROLE (DATA ASLI DARI DATABASE)
                const roleEl = document.getElementById('profile_role');
                if (roleEl) {
                    let displayRole = '-';
                    if (roles && roles.length > 0) {
                        // Ambil role asli (misal: "requester")
                        const rawRole = roles[0].toString();
                        
                        // Hanya ubah huruf pertama jadi besar (Capitalize)
                        // requester -> Requester
                        displayRole = rawRole.charAt(0).toUpperCase() + rawRole.slice(1);
                    }
                    roleEl.innerText = displayRole;
                }
                
                // 3. RENDER AVATAR
                const avatarImg = document.getElementById('profile_avatar');
                const avatarLoading = document.getElementById('avatar_loading');
                if (avatarImg) {
                    const avatarName = encodeURIComponent(user.name || 'User');
                    avatarImg.src = `https://ui-avatars.com/api/?name=${avatarName}&background=d62828&color=fff&size=256&bold=true`;
                    // Sembunyikan loading spinner avatar saat gambar termuat
                    avatarImg.onload = function() {
                        if(avatarLoading) avatarLoading.style.display = 'none';
                        avatarImg.style.display = 'block';
                    }
                }

                // 4. FETCH TOTAL TIKET
                if (token) {
                    try {
                        const ticketsRes = await fetch(`${baseUrl}/api/my-tickets`, {
                            headers: { 
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json' 
                            }
                        });
                        
                        if (ticketsRes.ok) {
                            const json = await ticketsRes.json();
                            const items = json.data?.data ? json.data.data : (json.data || []);
                            document.getElementById('profile_ticket_count').innerText = items.length || 0;
                        } else {
                            document.getElementById('profile_ticket_count').innerText = '0';
                        }
                    } catch (e) {
                        console.error('Gagal ambil tiket:', e);
                        document.getElementById('profile_ticket_count').innerText = '-';
                    }
                } else {
                    document.getElementById('profile_ticket_count').innerText = '0';
                }

                // 5. GANTI PASSWORD
                const saveBtn = document.querySelector('.btn-save');
                if (saveBtn) {
                    saveBtn.addEventListener('click', async function() {
                        const oldPass = document.getElementById('old_pass').value;
                        const pass = document.getElementById('new_pass').value;
                        const conf = document.getElementById('conf_pass').value;

                        const showMsg = (type, title, text) => {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({ icon: type, title: title, text: text, confirmButtonColor: '#d62828' });
                            } else {
                                alert(title + ': ' + text);
                            }
                        };

                        if (!oldPass) return showMsg('warning', 'Peringatan', 'Password lama wajib diisi');
                        if (!pass || pass.length < 8) return showMsg('warning', 'Peringatan', 'Password baru minimal 8 karakter');
                        if (pass !== conf) return showMsg('error', 'Error', 'Konfirmasi password tidak cocok');

                        const originalBtnText = saveBtn.innerHTML;
                        saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
                        saveBtn.disabled = true;

                        try {
                            const res = await fetch(`${baseUrl}/api/change-password`, {
                                method: 'POST',
                                headers: {
                                    'Authorization': `Bearer ${token}`,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    old_password: oldPass,
                                    new_password: pass
                                })
                            });

                            const json = await res.json();

                            if (res.ok) {
                                showMsg('success', 'Berhasil', 'Password telah diperbarui.');
                                document.getElementById('old_pass').value = '';
                                document.getElementById('new_pass').value = '';
                                document.getElementById('conf_pass').value = '';
                            } else {
                                const msg = json.message || 'Gagal mengubah password';
                                showMsg('error', 'Gagal', msg);
                            }
                        } catch (err) {
                            showMsg('error', 'Error', 'Gagal menghubungi server.');
                        } finally {
                            saveBtn.innerHTML = originalBtnText;
                            saveBtn.disabled = false;
                        }
                    });
                }
            } catch (err) {
                console.warn('Profile Error:', err);
            }
        });
    </script>
@endsection