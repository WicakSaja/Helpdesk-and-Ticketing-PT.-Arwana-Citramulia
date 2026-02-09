@extends('layouts.technician')
@section('title', 'Tugas Saya')

@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/technician-tasks.css'])
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title" id="taskTitle">Daftar Tugas</h1>
    </div>

    <div id="taskList">
        <div class="task-card">
            <div class="task-body">
                <h3>Loading...</h3>
                <p>Sedang memuat daftar tugas.</p>
            </div>
        </div>
    </div>

    <div id="modalUpdate" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Selesaikan Tiket</h3>
                <button class="btn-close" onclick="closeModal('modalUpdate')">&times;</button>
            </div>

            <form id="updateForm">
                <div
                    style="background: #e8f5e9; padding: 10px 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c8e6c9;">
                    <strong style="color: #2e7d32; font-size: 13px;">Tiket: <span id="uSubject">...</span></strong>
                </div>

                <input type="hidden" id="resolveTicketId">

                <div class="form-group">
                    <label class="form-label">Tanggal & Waktu Selesai</label>
                    <input type="datetime-local" class="form-input" id="resolvedAt" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Solusi / Tindakan Perbaikan</label>
                    <textarea class="form-textarea" id="solutionText"
                        placeholder="Contoh: Sudah dicek kabel power ternyata tidak terpasang dengan baik. Sudah dipasang kembali dengan benar dan komputer sudah bisa menyala normal."
                        required></textarea>
                </div>

                <div style="text-align: right; margin-top: 10px;">
                    <button type="button" onclick="closeModal('modalUpdate')"
                        style="background:white; border:1px solid #ddd; padding:10px 20px; border-radius:8px; cursor:pointer; margin-right: 10px;">Batal</button>
                    <button type="submit"
                        style="background:#2e7d32; color:white; border:none; padding:10px 25px; border-radius:8px; cursor:pointer; font-weight:600;">
                        <i class="fa-solid fa-check-circle"></i> Selesaikan Tiket
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/technician-tasks.js') }}?v={{ time() }}"></script>
@endsection
