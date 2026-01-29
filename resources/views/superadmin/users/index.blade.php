@extends('layouts.superadmin')
@section('title', 'Manajemen User')

@section('css')
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

        /* Loading Spinner */
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        /* PAGINATION */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 25px;
            padding: 0 30px;
        }

        .pagination-info {
            color: #666;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .per-page-selector {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            outline: none;
        }

        .per-page-selector:focus {
            border-color: #1565c0;
        }

        .pagination-buttons {
            display: flex;
            gap: 8px;
        }

        .page-btn {
            padding: 8px 14px;
            border: 1px solid #ddd;
            background: white;
            color: #666;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            transition: 0.3s;
        }

        .page-btn:hover:not(:disabled) {
            background: #1565c0;
            color: white;
            border-color: #1565c0;
        }

        .page-btn.active {
            background: #1565c0;
            color: white;
            border-color: #1565c0;
        }

        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
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
                <tr style="text-align: center;">
                    <td colspan="5" style="padding: 40px;">
                        <i class="fa-solid fa-spinner" style="font-size: 24px; animation: spin 1s linear infinite;"></i>
                        <p style="margin-top: 10px; color: #999;">Loading data pengguna...</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination-container">
            <div class="pagination-info">
                <span id="paginationInfoText">Menampilkan 0 dari 0 users</span>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label for="perPageSelect" style="font-size: 13px;">Per halaman:</label>
                    <select id="perPageSelect" class="per-page-selector" onchange="changePerPage()">
                        <option value="10">10</option>
                        <option value="15" selected>15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
            <div class="pagination-buttons" id="paginationButtons">
                <!-- Buttons will be generated dynamically -->
            </div>
        </div>
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
                        <select class="form-select" id="uDept" required>
                            <option value="" disabled selected>Loading...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role (Hak Akses)</label>
                        <select class="form-select" id="uRole">
                            <option value="technician">Technician</option>
                            <option value="helpdesk">Helpdesk</option>
                            <option value="supervisor">Supervisor</option>
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
        // Define API_URL for this page
        const API_URL = ("{{ config('app.url') }}".trim() || window.location.origin).replace(/\/$/, '');
        
        // Get token from session/localStorage
        const authToken = localStorage.getItem('user_token')
        
        // Pagination state
        let currentPage = 1;
        let currentPerPage = 15;
        
        // Change per page handler
        function changePerPage() {
            currentPerPage = parseInt(document.getElementById('perPageSelect').value);
            currentPage = 1; // Reset to first page
            loadUsers(currentPage, currentPerPage);
        }
        
        // Fetch Departments dari API
        async function loadDepartments() {
            try {
                const response = await fetch(`${API_URL}/api/departments`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                populateDepartmentSelect(result.data);
            } catch (error) {
                console.error('Error fetching departments:', error);
                document.getElementById('uDept').innerHTML = '<option value="" disabled selected>Error loading departments</option>';
            }
        }

        // Populate Department Select Dropdown
        function populateDepartmentSelect(departments) {
            const select = document.getElementById('uDept');
            select.innerHTML = '<option value="" disabled selected>-- Pilih Departemen --</option>';
            
            departments.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept.id;
                option.textContent = dept.name.charAt(0).toUpperCase() + dept.name.slice(1); // Capitalize first letter
                select.appendChild(option);
            });
        }

        // Fetch Users dari API
        async function loadUsers(page = 1, perPage = null) {
            // Use currentPerPage if perPage not provided
            if (perPage === null) {
                perPage = currentPerPage;
            }
            
            currentPage = page; // Update current page state
            
            try {
                const response = await fetch(`${API_URL}/api/users?page=${page}&per_page=${perPage}`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                populateTable(result.data.data);
                updatePagination(result.data);
            } catch (error) {
                console.error('Error fetching users:', error);
                document.getElementById('userTableBody').innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #d62828;">
                            <i class="fa-solid fa-exclamation-circle" style="font-size: 24px;"></i>
                            <p style="margin-top: 10px;">Gagal memuat data pengguna</p>
                        </td>
                    </tr>
                `;
            }
        }

        // Update Pagination UI
        function updatePagination(paginationData) {
            const infoText = document.getElementById('paginationInfoText');
            const buttons = document.getElementById('paginationButtons');
            
            // Update info text
            infoText.textContent = `Menampilkan ${paginationData.from || 0} - ${paginationData.to || 0} dari ${paginationData.total} users`;
            
            // Clear existing buttons
            buttons.innerHTML = '';
            
            // Previous button
            const prevBtn = document.createElement('button');
            prevBtn.className = 'page-btn';
            prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
            prevBtn.disabled = paginationData.current_page === 1;
            prevBtn.onclick = () => loadUsers(paginationData.current_page - 1);
            buttons.appendChild(prevBtn);
            
            // Page number buttons
            const startPage = Math.max(1, paginationData.current_page - 2);
            const endPage = Math.min(paginationData.last_page, paginationData.current_page + 2);
            
            // First page if not in range
            if (startPage > 1) {
                const firstBtn = document.createElement('button');
                firstBtn.className = 'page-btn';
                firstBtn.textContent = '1';
                firstBtn.onclick = () => loadUsers(1);
                buttons.appendChild(firstBtn);
                
                if (startPage > 2) {
                    const dots = document.createElement('span');
                    dots.textContent = '...';
                    dots.style.padding = '0 8px';
                    dots.style.color = '#999';
                    buttons.appendChild(dots);
                }
            }
            
            // Page numbers
            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.className = 'page-btn' + (i === paginationData.current_page ? ' active' : '');
                pageBtn.textContent = i;
                pageBtn.onclick = () => loadUsers(i);
                buttons.appendChild(pageBtn);
            }
            
            // Last page if not in range
            if (endPage < paginationData.last_page) {
                if (endPage < paginationData.last_page - 1) {
                    const dots = document.createElement('span');
                    dots.textContent = '...';
                    dots.style.padding = '0 8px';
                    dots.style.color = '#999';
                    buttons.appendChild(dots);
                }
                
                const lastBtn = document.createElement('button');
                lastBtn.className = 'page-btn';
                lastBtn.textContent = paginationData.last_page;
                lastBtn.onclick = () => loadUsers(paginationData.last_page);
                buttons.appendChild(lastBtn);
            }
            
            // Next button
            const nextBtn = document.createElement('button');
            nextBtn.className = 'page-btn';
            nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
            nextBtn.disabled = paginationData.current_page === paginationData.last_page;
            nextBtn.onclick = () => loadUsers(paginationData.current_page + 1);
            buttons.appendChild(nextBtn);
        }

        // Populate Table dengan Data
        function populateTable(users) {
            const tableBody = document.getElementById('userTableBody');
            tableBody.innerHTML = '';

            if (users.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #999;">
                            <i class="fa-solid fa-inbox" style="font-size: 24px;"></i>
                            <p style="margin-top: 10px;">Tidak ada data pengguna</p>
                        </td>
                    </tr>
                `;
                return;
            }

            users.forEach(user => {
                const roleClass = getRoleClass(user.roles[0]);
                const roleName = formatRoleName(user.roles[0]);
                const departmentName = user.department ? user.department.name : '-';

                const row = document.createElement('tr');
                row.id = `user-${user.id}`;
                row.innerHTML = `
                    <td>
                        <div style="font-weight: 600;">${user.name}</div>
                        <small style="color:#999;">${user.email}</small>
                    </td>
                    <td><span class="badge ${roleClass}">${roleName}</span></td>
                    <td>${departmentName}</td>
                    <td><span class="badge status-active" id="badge-${user.id}">Aktif</span></td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-icon btn-edit" 
                            onclick="editUser(${user.id}, '${user.name}', '${user.email}', '${user.phone}', '${user.roles[0]}', ${user.department_id || 'null'})">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button type="button" class="btn-icon btn-toggle-off" id="btn-status-${user.id}"
                            onclick="toggleStatus(${user.id}, '${user.name}', 'active')">
                            <i class="fa-solid fa-power-off"></i>
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Get Badge Class berdasarkan Role
        function getRoleClass(role) {
            const roleMap = {
                'master-admin': 'role-admin',
                'admin': 'role-admin',
                'helpdesk': 'role-admin',
                'supervisor': 'role-admin',
                'technician': 'role-tech',
                'requester': 'role-user'
            };
            return roleMap[role] || 'role-user';
        }

        // Format Role Name untuk Display
        function formatRoleName(role) {
            const nameMap = {
                'master-admin': 'Master Admin',
                'admin': 'Admin',
                'helpdesk': 'Helpdesk',
                'supervisor': 'Supervisor',
                'technician': 'Technician',
                'requester': 'Requester'
            };
            return nameMap[role] || role;
        }

        // Load users saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers(currentPage, currentPerPage);
            loadDepartments(); // Load departments for modal dropdown
        });

        // Variable to track edit mode
        let editingUserId = null;

        // 1. OPEN & CLOSE MODAL
        function openModal() {
            editingUserId = null; // Reset edit mode
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

        function editUser(userId, name, email, phone, role, deptId) {
            editingUserId = userId; // Store user ID for edit mode
            document.getElementById('modalTitle').innerText = "Edit User";

            // Isi Data Lama
            document.getElementById('uName').value = name;
            document.getElementById('uEmail').value = email;
            document.getElementById('uPhone').value = phone;
            document.getElementById('uRole').value = role;
            
            // Set department by ID if exists
            if (deptId) {
                document.getElementById('uDept').value = deptId;
            }

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
        document.getElementById('userForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const name = document.getElementById('uName').value;
            const email = document.getElementById('uEmail').value;
            const phone = document.getElementById('uPhone').value;
            const password = document.getElementById('uPassword').value;
            const role = document.getElementById('uRole').value;
            const departmentId = document.getElementById('uDept').value;

            // Check if edit or create mode
            const isEditMode = editingUserId !== null;

            // Validate required fields
            if (!name || !email || !phone || !departmentId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Data Tidak Lengkap',
                    text: 'Mohon isi semua field yang diperlukan',
                    confirmButtonColor: '#d62828'
                });
                return;
            }

            // For create mode, password is required
            if (!isEditMode && !password) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Wajib',
                    text: 'Password harus diisi saat menambah user baru',
                    confirmButtonColor: '#d62828'
                });
                return;
            }

            closeModal();

            // Show loading
            Swal.fire({
                title: isEditMode ? 'Mengupdate...' : 'Menyimpan...',
                text: isEditMode ? 'Sedang mengupdate data user' : 'Sedang membuat user baru',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            try {
                if (isEditMode) {
                    // UPDATE USER
                    const updateData = {
                        name: name,
                        email: email,
                        phone: phone,
                        department_id: parseInt(departmentId),
                        roles: [role]
                    };

                    const response = await fetch(`${API_URL}/api/users/${editingUserId}`, {
                        method: 'PUT',
                        headers: {
                            'Authorization': `Bearer ${authToken}`,
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(updateData)
                    });

                    const result = await response.json();
                    
                    console.log('Update Response Status:', response.status);
                    console.log('Update Response Data:', result);

                    if (!response.ok) {
                        if (result.errors) {
                            let errorMessages = '';
                            for (const [field, messages] of Object.entries(result.errors)) {
                                errorMessages += `<strong>${field}:</strong> ${messages.join(', ')}<br>`;
                            }
                            throw new Error(errorMessages);
                        }
                        throw new Error(result.message || `HTTP error! status: ${response.status}`);
                    }

                    // If password is filled, reset password separately
                    if (password && password.trim()) {
                        try {
                            const resetResponse = await fetch(`${API_URL}/api/users/${editingUserId}/reset-password`, {
                                method: 'POST',
                                headers: {
                                    'Authorization': `Bearer ${authToken}`,
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({ password: password })
                            });

                            const resetResult = await resetResponse.json();
                            console.log('Password Reset Response:', resetResponse.status, resetResult);

                            if (!resetResponse.ok) {
                                console.error('Password reset error:', resetResult);
                                // Don't throw, just warn - user data already updated
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Sebagian Berhasil',
                                    html: `Data user berhasil diupdate, tapi gagal reset password.<br><br>Error: ${resetResult.message || 'Unknown error'}`,
                                    confirmButtonColor: '#f57c00'
                                });
                                loadUsers(currentPage, currentPerPage);
                                return; // Exit early, don't show success message
                            }
                        } catch (resetError) {
                            console.error('Password reset exception:', resetError);
                            Swal.fire({
                                icon: 'warning',
                                title: 'Sebagian Berhasil',
                                html: `Data user berhasil diupdate, tapi gagal reset password.<br><br>Error: ${resetError.message}`,
                                confirmButtonColor: '#f57c00'
                            });
                            loadUsers(currentPage, currentPerPage);
                            return;
                        }
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'User berhasil diupdate.',
                        confirmButtonColor: '#1565c0',
                        timer: 2000,
                        showConfirmButton: false
                    });

                } else {
                    // CREATE USER
                    const response = await fetch(`${API_URL}/api/users`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${authToken}`,
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            name: name,
                            email: email,
                            phone: phone,
                            password: password,
                            department_id: parseInt(departmentId),
                            roles: [role]
                        })
                    });

                    const result = await response.json();
                    
                    console.log('Create Response Status:', response.status);
                    console.log('Create Response Data:', result);

                    if (!response.ok) {
                        if (result.errors) {
                            let errorMessages = '';
                            for (const [field, messages] of Object.entries(result.errors)) {
                                errorMessages += `<strong>${field}:</strong> ${messages.join(', ')}<br>`;
                            }
                            throw new Error(errorMessages);
                        }
                        throw new Error(result.message || `HTTP error! status: ${response.status}`);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'User berhasil ditambahkan.',
                        confirmButtonColor: '#1565c0',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }

                // Reload users table
                loadUsers(currentPage, currentPerPage);

            } catch (error) {
                console.error('Error saving user:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    html: error.message || 'Terjadi kesalahan saat menyimpan user',
                    confirmButtonColor: '#d62828'
                });
            }
        });

        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                closeModal();
            }
        }
    </script>
@endsection
