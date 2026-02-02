@extends('layouts.requester')
@section('title', 'Dashboard')

@section('css')
    @vite(['resources/css/dashboard-requester.css'])
@endsection

@section('content')
    <div class="header-welcome">
        <div class="user-info">
            <h2>Halo, {{ Auth::user() ? Auth::user()->name : 'Fadhli (Guest)' }}! 👋</h2>
            <p>Selamat datang di Arwana Helpdesk System</p>
        </div>
        <div class="user-avatar">
            {{ substr(Auth::user() ? Auth::user()->name : 'G', 0, 1) }}
        </div>
    </div>

    <div class="stats-grid">

        <div class="stat-card card-blue">
            <div class="stat-info">
                <p>Total Tiket Saya</p>
                <h3>12</h3>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-ticket"></i>
            </div>
        </div>

        <div class="stat-card card-orange">
            <div class="stat-info">
                <p>Sedang Diproses</p>
                <h3>5</h3>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-spinner"></i>
            </div>
        </div>

        <div class="stat-card card-green">
            <div class="stat-info">
                <p>Tiket Selesai</p>
                <h3>7</h3>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-check-double"></i>
            </div>
        </div>

    </div>

    <div class="empty-state">
        <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" alt="Empty">
        <p>Belum ada aktivitas terbaru hari ini.</p>
        <br>
        <a href="{{ route('tickets.create') }}">Buat Tiket Sekarang &rarr;</a>
    </div>
@endsection
