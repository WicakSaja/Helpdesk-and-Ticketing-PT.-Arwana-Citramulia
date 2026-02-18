@extends('layouts.helpdesk')
@section('title', 'Manajemen Kategori')

@section('css')
    @vite(['resources/css/category.css'])
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Manajemen Kategori</h1>
        <button class="btn-add" onclick="openModal()">
            <i class="fa-solid fa-plus"></i> Tambah Kategori
        </button>
    </div>

    <div class="table-container">
        <table class="cat-table">
            <thead>
                <tr>
                    <th style="width: 60px;">No.</th>
                    <th style="width: 25%;">Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <tr>
                    <td colspan="4" class="loading-row">
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

    {{-- Modal Form --}}
    <div id="catModal" class="modal-overlay">
        <div class="modal-box">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h3 style="font-size: 20px; font-weight:700;" id="modalTitle">Tambah Kategori</h3>
                <span onclick="closeModal()" style="cursor:pointer; font-size:24px; color:#999;">&times;</span>
            </div>

            <div id="errorMessage" class="text-error"
                style="background: #ffebee; padding: 10px; border-radius: 6px; margin-bottom: 15px; display: none;"></div>

            <form onsubmit="handleSave(event)">
                <div class="form-group">
                    <label class="form-label">Nama Kategori <span style="color:red">*</span></label>
                    <input type="text" id="catName" class="form-input" placeholder="" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea id="catDesc" class="form-textarea" rows="3" placeholder=""></textarea>
                </div>

                <div style="text-align: right; margin-top: 25px;">
                    <button type="button" onclick="closeModal()"
                        style="background:white; border:1px solid #ddd; padding:10px 25px; border-radius:8px; cursor:pointer; margin-right: 10px; font-weight:600; color:#555;">Batal</button>
                    <button type="submit" id="btnSave"
                        style="background:#1565c0; color:white; border:none; padding:10px 30px; border-radius:8px; cursor:pointer; font-weight:600;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/category.js') }}?v={{ time() }}"></script>
@endsection
