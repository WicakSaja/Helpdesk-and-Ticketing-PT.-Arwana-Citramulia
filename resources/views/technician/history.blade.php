@extends('layouts.technician')
@section('title', 'Riwayat Selesai')

@section('css')
<style>
    .page-header { margin-bottom: 30px; }
    .table-container { background: white; padding: 30px; border-radius: 16px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); overflow-x: auto; }
    .history-table { width: 100%; border-collapse: collapse; }
    .history-table th { text-align: left; color: #888; font-size: 12px; padding: 15px; border-bottom: 1px solid #eee; text-transform: uppercase; }
    .history-table td { padding: 20px 15px; border-bottom: 1px solid #f9f9f9; font-size: 14px; color: #333; }
    .badge-done { background: #e8f5e9; color: #2e7d32; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
</style>
@endsection

@section('content')
    <div class="page-header">
        <h1 style="font-size:24px; font-weight:700; color:#333;">Riwayat Pekerjaan</h1>
    </div>

    <div class="table-container">
        <table class="history-table">
            <thead>
                <tr>
                    <th>ID Tiket</th>
                    <th>Tanggal Selesai</th>
                    <th>Judul Masalah</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><b>#TKT-005</b></td>
                    <td>27 Jan 2026</td>
                    <td>Ganti Mouse HRD</td>
                    <td>HRD</td>
                    <td><span class="badge-done">Selesai</span></td>
                </tr>
                <tr>
                    <td><b>#TKT-003</b></td>
                    <td>25 Jan 2026</td>
                    <td>Cek Kabel LAN</td>
                    <td>Meeting Room</td>
                    <td><span class="badge-done">Selesai</span></td>
                </tr>
                <tr>
                    <td><b>#TKT-001</b></td>
                    <td>20 Jan 2026</td>
                    <td>Install Ulang Windows</td>
                    <td>Marketing</td>
                    <td><span class="badge-done">Selesai</span></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection