@extends('layouts.superadmin')
@section('title', 'Laporan Berkala')

@section('css')
    <style>
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

        .btn-export {
            background: #2e7d32;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* TAB STYLING */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 1px;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            color: #777;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: 0.3s;
        }

        .tab-btn:hover {
            color: #1565c0;
        }

        .tab-btn.active {
            color: #1565c0;
            border-bottom-color: #1565c0;
        }

        /* TABLE STYLING */
        .table-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
            display: none;
        }

        .table-container.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-size: 13px;
            font-weight: 700;
            color: #555;
            border-bottom: 2px solid #ddd;
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #333;
        }

        .data-table tr:hover {
            background: #fcfcfc;
        }

        /* SUMMARY ROW */
        .summary-row {
            background: #e3f2fd !important;
            font-weight: 700;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Rekapitulasi Laporan</h1>
        <button class="btn-export" onclick="alert('Fitur download excel akan segera aktif!')">
            <i class="fa-solid fa-file-excel"></i> Download Laporan
        </button>
    </div>

    <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('weekly')">Laporan Mingguan</button>
        <button class="tab-btn" onclick="switchTab('monthly')">Laporan Bulanan</button>
        <button class="tab-btn" onclick="switchTab('yearly')">Laporan Tahunan</button>
    </div>

    <div id="weekly" class="table-container active">
        <h3 style="margin-bottom: 15px; font-size: 16px;">📅 Data Minggu Ini (28 Jan - 03 Feb)</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Hari</th>
                    <th>Tiket Masuk</th>
                    <th>Selesai</th>
                    <th>Pending</th>
                    <th>Performa</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Senin</td>
                    <td>12</td>
                    <td>10</td>
                    <td>2</td>
                    <td>83%</td>
                </tr>
                <tr>
                    <td>Selasa</td>
                    <td>15</td>
                    <td>14</td>
                    <td>1</td>
                    <td>93%</td>
                </tr>
                <tr>
                    <td>Rabu</td>
                    <td>8</td>
                    <td>8</td>
                    <td>0</td>
                    <td>100%</td>
                </tr>
                <tr>
                    <td>Kamis</td>
                    <td>10</td>
                    <td>7</td>
                    <td>3</td>
                    <td>70%</td>
                </tr>
                <tr>
                    <td>Jumat</td>
                    <td>5</td>
                    <td>5</td>
                    <td>0</td>
                    <td>100%</td>
                </tr>
                <tr class="summary-row">
                    <td>TOTAL</td>
                    <td>50</td>
                    <td>44</td>
                    <td>6</td>
                    <td>88% (Avg)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="monthly" class="table-container">
        <h3 style="margin-bottom: 15px; font-size: 16px;">📅 Data Tahun 2026</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Total Tiket</th>
                    <th>Selesai Tepat Waktu</th>
                    <th>Terlambat (Overdue)</th>
                    <th>Kategori Terbanyak</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Januari</td>
                    <td>120</td>
                    <td>110</td>
                    <td>10</td>
                    <td>Jaringan</td>
                </tr>
                <tr>
                    <td>Februari</td>
                    <td>98</td>
                    <td>90</td>
                    <td>8</td>
                    <td>Hardware</td>
                </tr>
                <tr>
                    <td>Maret</td>
                    <td>--</td>
                    <td>--</td>
                    <td>--</td>
                    <td>--</td>
                </tr>
                <tr class="summary-row">
                    <td>TOTAL YTD</td>
                    <td>218</td>
                    <td>200</td>
                    <td>18</td>
                    <td>-</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="yearly" class="table-container">
        <h3 style="margin-bottom: 15px; font-size: 16px;">📅 Arsip Tahunan</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tahun</th>
                    <th>Total Tiket</th>
                    <th>Avg. Respon Time</th>
                    <th>Top Teknisi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2024</td>
                    <td>1,450</td>
                    <td>25 Menit</td>
                    <td>Andi Saputra</td>
                </tr>
                <tr>
                    <td>2025</td>
                    <td>1,200</td>
                    <td>18 Menit</td>
                    <td>Budi Doremi</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <script>
        function switchTab(tabId) {
            // 1. Hide semua tabel
            document.querySelectorAll('.table-container').forEach(el => el.classList.remove('active'));
            // 2. Remove active class dari semua tombol
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

            // 3. Show tabel yg dipilih
            document.getElementById(tabId).classList.add('active');
            // 4. Set tombol aktif (cara tricky: cari text tombol yg diklik atau gunakan event target jika mau lebih kompleks, tapi ini simple)
            event.target.classList.add('active');
        }
    </script>
@endsection
