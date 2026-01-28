@extends('layouts.superadmin')
@section('title', 'Dashboard Admin')

@section('css')
<style>
    .welcome-banner { background: white; padding: 30px; border-radius: 16px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); display: flex; justify-content: space-between; align-items: center; }
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
    
    .stat-card { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 20px; transition: 0.3s; }
    .stat-card:hover { transform: translateY(-5px); }
    
    .stat-icon { width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
    .stat-info h3 { font-size: 28px; font-weight: 700; color: #333; margin: 0; }
    .stat-info p { font-size: 13px; color: #777; margin: 0; }

    /* Warna Icon */
    .bg-blue { background: #e3f2fd; color: #1565c0; }
    .bg-green { background: #e8f5e9; color: #2e7d32; }
    .bg-orange { background: #fff3e0; color: #f57c00; }
    .bg-red { background: #ffebee; color: #d62828; }
</style>
@endsection

@section('content')
    <div class="welcome-banner">
        <div>
            <h2 style="font-size: 24px; color: #333;">Selamat Datang, Administrator! 👑</h2>
            <p style="color: #666;">Pantau seluruh aktivitas sistem Helpdesk Arwana dari sini.</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-blue"><i class="fa-solid fa-users"></i></div>
            <div class="stat-info">
                <h3>120</h3>
                <p>Total User</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-green"><i class="fa-solid fa-screwdriver-wrench"></i></div>
            <div class="stat-info">
                <h3>8</h3>
                <p>Teknisi Aktif</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-orange"><i class="fa-solid fa-ticket"></i></div>
            <div class="stat-info">
                <h3>450</h3>
                <p>Tiket Bulan Ini</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-red"><i class="fa-solid fa-building"></i></div>
            <div class="stat-info">
                <h3>12</h3>
                <p>Departemen</p>
            </div>
        </div>
    </div>
@endsection