@extends('layouts.requester')
@section('title', 'Riwayat Tiket Saya')

@section('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/ticket-style.css'])
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Riwayat Tiket</h1>
            <p class="page-subtitle">Pantau status laporan kendala Anda disini.</p>
        </div>
        <a href="{{ route('tickets.create') }}" class="btn-create">
            <i class="fa-solid fa-plus-circle"></i> Buat Tiket Baru
        </a>
    </div>

    {{-- Loading state awal sebelum JS jalan --}}
    <div class="table-container">
        <table class="ticket-table">
            <thead>
                <tr>
                    <th width="30%">Subjek</th>
                    <th width="20%">Kategori</th>
                    <th width="15%">Status</th>
                    <th width="20%">Update Terakhir</th>
                    <th width="15%" class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="ticketTableBody">
                <tr>
                    <td colspan="5" class="loading-cell">
                        <i class="fa-solid fa-circle-notch fa-spin loading-icon"></i>
                        <p>Memuat riwayat tiket Anda...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Pagination Controls --}}
    <div id="ticketPagination" class="pagination-container" style="display: none;"></div>
@endsection

@section('scripts')
    <script src="{{ asset('js/auth-token-manager.js') }}"></script>
    <script src="{{ asset('js/tickets-index.js') }}?v={{ time() }}"></script>
@endsection
