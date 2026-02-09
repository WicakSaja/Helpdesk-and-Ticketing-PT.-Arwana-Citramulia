@extends('layouts.helpdesk')
@section('title', 'Semua Data Tiket')

@section('css')
    {{-- CSS Eksternal --}}
    @vite(['resources/css/helpdesk-all-tickets.css'])
@endsection

@section('content')
    <div class="page-header">
        <div class="header-left">
            <h2 class="page-title">Semua Data Tiket</h2>
            <p class="page-subtitle">Pantau seluruh tiket yang masuk ke sistem.</p>
        </div>

        <div class="header-right">
            <div class="search-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Cari tiket, subjek, atau user...">
            </div>
        </div>
    </div>

    <div class="table-container">
        {{-- Wrapper Table Responsive (Scroll Samping di HP) --}}
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>No. Tiket</th>
                        <th>Subjek / Pengaju</th>
                        <th>Departemen</th>
                        <th>Teknisi</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody id="ticketTableBody">
                    <tr>
                        <td colspan="6" class="loading-row">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Memuat data tiket...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pesan Data Kosong --}}
        <div id="noDataMessage" style="display: none; text-align: center; padding: 40px 20px; color: #888;">
            <div class="empty-img mb-2"><i class="fa-solid fa-folder-open fa-2x"></i></div>
            <p>Tidak ada data tiket ditemukan.</p>
        </div>

        {{-- Pagination --}}
        <div class="pagination-container" id="allTicketsPagination" style="display: none;">
            <div id="paginationInfo" class="pagination-info"></div>
            <div class="pagination-buttons" id="paginationControls"></div>
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    <div id="detailModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Detail Tiket <span id="mId" class="text-primary"></span></h3>
                <span class="close-modal" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="modalDetailContent">
                <div class="mb-4">
                    <h4 id="mSubject" style="font-size: 1.1rem; font-weight: 700; color: #333; margin-bottom: 5px;"></h4>
                    <div id="mDept" class="text-muted small"></div>
                </div>

                <div class="detail-group">
                    <label class="detail-label text-uppercase text-secondary"
                        style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        Riwayat Perjalanan
                    </label>
                    <div class="timeline" id="mTimeline"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- JS Helper untuk Sidebar --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/helpdesk-all-tickets.js') }}?v={{ time() }}"></script>

    <script>
        // Modal Logic Sederhana (Jika belum ada di file js eksternal)
        function closeModal() {
            document.getElementById('detailModal').style.display = 'none';
        }

        // Tutup modal jika klik di luar area
        window.onclick = function(event) {
            const modal = document.getElementById('detailModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
@endsection
