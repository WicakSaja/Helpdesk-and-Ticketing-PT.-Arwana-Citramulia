@extends('layouts.superadmin')
@section('title', 'Manajemen User')

@section('css')
    @vite(['resources/css/users.css'])
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Manajemen User</h1>
        <div class="header-actions">
            <button class="btn-filter-toggle" id="btnFilterToggle" type="button" onclick="toggleFilters()">
                <i class="fa-solid fa-sliders"></i>
            </button>
            <button class="btn-add btn-add-icon" onclick="openModal()" title="Tambah User">
                <i class="fa-solid fa-user-plus"></i>
                <span class="btn-text">Tambah User</span>
            </button>
        </div>
    </div>

    {{-- Search & Filter Controls --}}
    <div class="controls-wrapper">
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Cari nama, email, atau telepon...">
        </div>
        <div class="filters-right" id="filtersRight">
            <select class="filter-select" id="filterRole">
                <option value="">Semua Role</option>
                <option value="helpdesk">Helpdesk</option>
                <option value="technician">Technician</option>
                <option value="requester">Requester</option>
            </select>
            <select class="filter-select" id="filterDepartment">
                <option value="">Semua Departemen</option>
                {{-- Diisi JS --}}
            </select>
            <select class="filter-select" id="filterStatus">
                <option value="">Semua Status</option>
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
            </select>
        </div>
    </div>

    <div class="table-container">
        <table class="user-table">
            <thead>
                <tr>
                    <th>Nama User</th>
                    <th>Role</th>
                    <th>Departemen</th>
                    <th>Status</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                <tr style="text-align: center;">
                    <td colspan="5" style="padding: 12px;">
                        <i class="fa-solid fa-spinner" style="font-size: 24px; animation: spin 1s linear infinite;"></i>
                        <p style="margin-top: 5px; color: #999; font-size: 13px;">Loading data pengguna...</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination-container">
            <div class="pagination-info">
                <span id="paginationInfoText">Menampilkan 0 dari 0 users</span>
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
                            <option value="requester">Requester</option>
                            <option value="technician">Technician</option>
                            <option value="helpdesk">Helpdesk</option>
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
    <script src="{{ asset('js/users.js') }}?v={{ time() }}"></script>
@endsection