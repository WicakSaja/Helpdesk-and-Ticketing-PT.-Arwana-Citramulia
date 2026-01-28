@extends('layouts.superadmin')
@section('title', 'Manajemen User')

@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* HEADER & BUTTONS */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .btn-add {
            background: #1565c0;
            color: white;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            box-shadow: 0 4px 10px rgba(21, 101, 192, 0.2);
        }

        .btn-add:hover {
            background: #0d47a1;
            transform: translateY(-2px);
        }

        /* TABLE STYLES */
        .table-container {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
        }

        .user-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .user-table th {
            text-align: left;
            color: #888;
            padding: 15px;
            border-bottom: 2px solid #f0f0f0;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .user-table td {
            padding: 15px;
            border-bottom: 1px solid #f9f9f9;
            font-size: 14px;
            color: #333;
            vertical-align: middle;
        }

        /* Efek Hover Baris */
        .user-table tr {
            transition: background 0.2s;
        }

        .user-table tr:hover td {
            background: #fcfcfc;
        }

        /* Style untuk baris Nonaktif */
        .row-inactive td {
            background: #f9f9f9;
            color: #aaa;
        }

        .row-inactive .btn-edit {
            pointer-events: none;
            opacity: 0.5;
            filter: grayscale(100%);
        }

        /* BADGES */
        .badge {
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            display: inline-block;
        }

        .role-admin {
            background: #e3f2fd;
            color: #1565c0;
        }

        .role-tech {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .role-user {
            background: #fce4ec;
            color: #c2185b;
        }

        .status-active {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .status-inactive {
            background: #ffebee;
            color: #d62828;
            border: 1px solid #ffcdd2;
        }

        /* ACTION BUTTONS */
        .btn-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
            margin-right: 5px;
            position: relative;
            z-index: 10;
        }

        .btn-icon i {
            pointer-events: none;
        }

        .btn-edit {
            background: #fff3e0;
            color: #f57c00;
        }

        .btn-edit:hover {
            background: #f57c00;
            color: white;
            transform: scale(1.05);
        }

        .btn-toggle-off {
            background: #ffebee;
            color: #d62828;
        }

        .btn-toggle-off:hover {
            background: #d62828;
            color: white;
            transform: scale(1.05);
        }

        .btn-toggle-on {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .btn-toggle-on:hover {
            background: #2e7d32;
            color: white;
            transform: scale(1.05);
        }

        /* MODAL FORM */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            backdrop-filter: blur(4px);
        }

        .modal-box {
            background: white;
            width: 600px;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Grid Form untuk 2 Kolom */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 5px;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        .form-input:focus,
        .form-select:focus {
            border-color: #1565c0;
            box-shadow: 0 0 0 3px rgba(21, 101, 192, 0.1);
        }

        /* Password Toggle */
        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
        }

        .toggle-password:hover {
            color: #1565c0;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Manajemen User</h1>
        <button class="btn-add" onclick="openModal()"><i class="fa-solid fa-user-plus"></i> Tambah User</button>
    </div>

    <div class="table-container">
        <table class="user-table">
            <thead>
                <tr>
                    <th>Nama User</th>
                    <th>Role</th>
                    <th>Departemen</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody id="userTableBody">

                <tr id="user-1">
                    <td>
                        <div style="font-weight: 600;">Super Admin</div>
                        <small style="color:#999;">admin@arwana.com</small>
                    </td>
                    <td><span class="badge role-admin">Super Admin</span></td>
                    <td>IT Dept</td>
                    <td><span class="badge status-active" id="badge-1">Aktif</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-icon btn-edit"
                            onclick="editUser('Super Admin', 'admin@arwana.com', '081234567890', 'Super Admin', 'IT')"><i
                                class="fa-solid fa-pen"></i></button>
                        <button type="button" class="btn-icon btn-toggle-off" style="opacity:0.3; cursor:not-allowed;"><i
                                class="fa-solid fa-power-off"></i></button>
                    </td>
                </tr>

                <tr id="user-2">
                    <td>
                        <div style="font-weight: 600;">Teknisi Andi</div>
                        <small style="color:#999;">andi@arwana.com</small>
                    </td>
                    <td><span class="badge role-tech">Technician</span></td>
                    <td>Maintenance</td>
                    <td><span class="badge status-active" id="badge-2">Aktif</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-icon btn-edit"
                            onclick="editUser('Teknisi Andi', 'andi@arwana.com', '089876543210', 'Technician', 'Maintenance')"><i
                                class="fa-solid fa-pen"></i></button>
                        <button type="button" class="btn-icon btn-toggle-off" id="btn-status-2"
                            onclick="toggleStatus(2, 'Teknisi Andi', 'active')"><i
                                class="fa-solid fa-power-off"></i></button>
                    </td>
                </tr>

                <tr id="user-3" class="row-inactive">
                    <td>
                        <div style="font-weight: 600;">User Requester</div>
                        <small style="color:#999;">user@arwana.com</small>
                    </td>
                    <td><span class="badge role-user">Requester</span></td>
                    <td>Produksi</td>
                    <td><span class="badge status-inactive" id="badge-3">Nonaktif</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-icon btn-edit"><i class="fa-solid fa-pen"></i></button>
                        <button type="button" class="btn-icon btn-toggle-on" id="btn-status-3"
                            onclick="toggleStatus(3, 'User Requester', 'inactive')"><i
                                class="fa-solid fa-rotate-left"></i></button>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>

    <div id="userModal" class="modal-overlay">
        <div class="modal-box">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h3 style="font-size: 20px; font-weight:700;" id="modalTitle">Tambah User Baru</h3>
                <span onclick="closeModal()" style="cursor:pointer; font-size:24px; color:#999;">&times;</span>
            </div>

            <form id="userForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" id="uName" class="form-input" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Perusahaan</label>
                        <input type="email" id="uEmail" class="form-input" placeholder="email@arwana.com" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">No. Telepon / WA</label>
                        <input type="text" id="uPhone" class="form-input" placeholder="08..." required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="uPassword" class="form-input" placeholder="Min. 8 Karakter">
                            <i class="fa-solid fa-eye toggle-password" onclick="togglePass()"></i>
                        </div>
                        <small id="passHint" style="color:#888; font-size:11px; display:none;">*Kosongkan jika tidak ingin
                            mengubah password.</small>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Departemen</label>
                        <select class="form-select" id="uDept">
                            <option value="IT">IT</option>
                            <option value="Produksi">Produksi</option>
                            <option value="HRD">HRD / GA</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Gudang">Gudang / Logistik</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role (Hak Akses)</label>
                        <select class="form-select" id="uRole">
                            <option value="Requester">Requester</option>
                            <option value="Technician">Technician</option>
                            <option value="Helpdesk">Helpdesk</option>
                            <option value="Super Admin">Super Admin</option>
                        </select>
                    </div>
                </div>

                <div style="text-align: right; margin-top: 30px;">
                    <button type="button" onclick="closeModal()"
                        style="background:white; border:1px solid #ddd; padding:10px 25px; border-radius:8px; cursor:pointer; margin-right: 10px; font-weight:600; color:#555;">Batal</button>
                    <button type="submit"
                        style="background:#1565c0; color:white; border:none; padding:10px 30px; border-radius:8px; cursor:pointer; font-weight:600;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // 1. OPEN & CLOSE MODAL
        function openModal() {
            document.getElementById('modalTitle').innerText = "Tambah User Baru";

            // Reset Form
            document.getElementById('uName').value = "";
            document.getElementById('uEmail').value = "";
            document.getElementById('uPhone').value = "";
            document.getElementById('uPassword').value = "";

            // Password Wajib saat Tambah
            document.getElementById('uPassword').setAttribute('required', 'required');
            document.getElementById('uPassword').placeholder = "Wajib Diisi";
            document.getElementById('passHint').style.display = 'none';

            document.getElementById('userModal').style.display = 'flex';
        }

        function editUser(name, email, phone, role, dept) {
            document.getElementById('modalTitle').innerText = "Edit User";

            // Isi Data Lama
            document.getElementById('uName').value = name;
            document.getElementById('uEmail').value = email;
            document.getElementById('uPhone').value = phone;
            document.getElementById('uRole').value = role;
            document.getElementById('uDept').value = dept;

            // Password Opsional saat Edit
            document.getElementById('uPassword').value = "";
            document.getElementById('uPassword').removeAttribute('required');
            document.getElementById('uPassword').placeholder = "Biarkan kosong...";
            document.getElementById('passHint').style.display = 'block';

            document.getElementById('userModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('userModal').style.display = 'none';
        }

        // 2. TOGGLE PASSWORD VISIBILITY
        function togglePass() {
            const input = document.getElementById('uPassword');
            const icon = document.querySelector('.toggle-password');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // 3. TOGGLE STATUS LOGIC
        function toggleStatus(id, name, currentStatus) {
            let isDeactivating = (currentStatus === 'active');
            let titleText = isDeactivating ? 'Nonaktifkan User?' : 'Aktifkan Kembali?';
            let bodyText = isDeactivating ?
                `User <strong>${name}</strong> tidak akan bisa login.` :
                `User <strong>${name}</strong> akan dapat login kembali.`;
            let confirmColor = isDeactivating ? '#d62828' : '#2e7d32';

            Swal.fire({
                html: `
                <div style="text-align: center;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 15px auto; background: ${isDeactivating ? '#ffebee' : '#e8f5e9'}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid ${isDeactivating ? 'fa-user-slash' : 'fa-user-check'}" style="font-size: 36px; color: ${confirmColor};"></i>
                    </div>
                    <h2 style="font-size: 22px; font-weight: 700; color: #333; margin-bottom: 10px;">${titleText}</h2>
                    <p style="color: #666; font-size: 14px;">${bodyText}</p>
                </div>
            `,
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#E0E0E0',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: '<span style="color:#555">Batal</span>',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const row = document.getElementById('user-' + id);
                    const badge = document.getElementById('badge-' + id);
                    const btn = document.getElementById('btn-status-' + id);

                    if (isDeactivating) {
                        row.classList.add('row-inactive');
                        badge.className = 'badge status-inactive';
                        badge.innerText = 'Nonaktif';
                        btn.className = 'btn-icon btn-toggle-on';
                        btn.innerHTML = '<i class="fa-solid fa-rotate-left"></i>';
                        btn.setAttribute('onclick', `toggleStatus(${id}, '${name}', 'inactive')`);
                        Swal.fire({
                            icon: 'success',
                            title: 'User Nonaktif',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        row.classList.remove('row-inactive');
                        badge.className = 'badge status-active';
                        badge.innerText = 'Aktif';
                        btn.className = 'btn-icon btn-toggle-off';
                        btn.innerHTML = '<i class="fa-solid fa-power-off"></i>';
                        btn.setAttribute('onclick', `toggleStatus(${id}, '${name}', 'active')`);
                        Swal.fire({
                            icon: 'success',
                            title: 'User Aktif',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                }
            })
        }

        // 4. SIMPAN DATA
        document.getElementById('userForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let name = document.getElementById('uName').value;
            let email = document.getElementById('uEmail').value;
            let role = document.getElementById('uRole').value;
            let dept = document.getElementById('uDept').value;

            closeModal();

            Swal.fire({
                title: 'Menyimpan...',
                timer: 800,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading()
                }
            }).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'User berhasil disimpan.',
                    confirmButtonColor: '#1565c0',
                    timer: 2000
                });

                // Simulasi Tambah ke Tabel
                let table = document.getElementById('userTableBody');
                let newId = Math.floor(Math.random() * 1000);
                let badgeClass = role === 'Super Admin' ? 'role-admin' : (role === 'Technician' ?
                    'role-tech' : 'role-user');

                let newRow = `
                <tr id="user-${newId}">
                    <td><div style="font-weight: 600;">${name}</div><small style="color:#999;">${email}</small></td>
                    <td><span class="badge ${badgeClass}">${role}</span></td>
                    <td>${dept}</td>
                    <td><span class="badge status-active" id="badge-${newId}">Aktif</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-icon btn-edit" onclick="editUser('${name}', '${email}', '08xxxx', '${role}', '${dept}')"><i class="fa-solid fa-pen"></i></button>
                        <button type="button" class="btn-icon btn-toggle-off" id="btn-status-${newId}" onclick="toggleStatus(${newId}, '${name}', 'active')"><i class="fa-solid fa-power-off"></i></button>
                    </td>
                </tr>
            `;
                table.insertAdjacentHTML('beforeend', newRow);
            });
        });

        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                closeModal();
            }
        }
    </script>
@endsection
