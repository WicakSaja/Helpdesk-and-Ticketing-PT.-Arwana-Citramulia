@extends('layouts.superadmin')
@section('title', 'Manajemen Departemen')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/department.css') }}">
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Manajemen Departemen</h1>
        <button class="btn-add" onclick="openModal()">
            <i class="fa-solid fa-plus"></i> Tambah Departemen
        </button>
    </div>

    <div class="table-container">
        <table class="dept-table">
            <thead>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th>Nama Departemen</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <tr>
                    <td colspan="3" class="loading-row">
                        <i class="fa-solid fa-spinner fa-spin" style="font-size: 24px;"></i>
                        <p style="margin-top: 10px;">Sedang memuat data...</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="pagination-wrapper">
            <div class="pagination-info" id="paginationInfo">Menampilkan 0 data</div>
            <div class="pagination-controls">
                <button class="page-btn" id="btnPrev"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="page-btn active" id="btnPageNum">1</button>
                <button class="page-btn" id="btnNext"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>
    </div>

    <div id="deptModal" class="modal-overlay">
        <div class="modal-box">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h3 style="font-size: 20px; font-weight:700;" id="modalTitle">Tambah Departemen</h3>
                <span onclick="closeModal()" style="cursor:pointer; font-size:24px; color:#999;">&times;</span>
            </div>
            
            <div id="errorMessage" class="text-error" style="background: #ffebee; padding: 10px; border-radius: 6px; margin-bottom: 15px; display: none;"></div>

            <form onsubmit="handleSave(event)">
                <div class="form-group">
                    <label class="form-label">Nama Departemen</label>
                    <input type="text" id="deptName" class="form-input" placeholder="Contoh: IT Support" required>
                </div>
                
                <div style="text-align: right; margin-top: 25px;">
                    <button type="button" onclick="closeModal()" style="background:white; border:1px solid #ddd; padding:10px 25px; border-radius:8px; cursor:pointer; margin-right: 10px; font-weight:600; color:#555;">Batal</button>
                    <button type="submit" id="btnSave" style="background:#1565c0; color:white; border:none; padding:10px 30px; border-radius:8px; cursor:pointer; font-weight:600;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/department.js') }}?v={{ time() }}"></script>
@endsection