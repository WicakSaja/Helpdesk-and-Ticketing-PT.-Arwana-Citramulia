<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Wev\TicketController;

// 1. LANDING PAGE (Halaman Depan)
Route::get('/', function () {
    return view('landing_page');
})->name('home');

// 2. Auth Routes - Fetch dari API
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth.session')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/api/user', [AuthController::class, 'getCurrentUser'])->name('api.user');
});

// ====================================================
// 3. DASHBOARD UTAMA (TANPA LOGIN / BYPASS)
// ====================================================
Route::get('/dashboard', function () {
    
    // GANTI VARIABEL INI MANUAL UNTUK CEK TAMPILAN DASHBOARD UTAMA
    $role = 'requester'; // Opsi: 'requester', 'helpdesk', 'technician'

    if ($role == 'helpdesk') {
        return view('dashboard.helpdesk');
    } 
    elseif ($role == 'requester') {
        return view('dashboard.requester');
    }
    elseif ($role == 'technician') {
        return view('dashboard.technician');
    }
    
    return view('dashboard.requester'); // Default

})->name('dashboard');


// ====================================================
// 4. AKSES LANGSUNG SEMUA HALAMAN (TANPA LOGIN)
// ====================================================

// Group Dashboard
Route::prefix('dashboard')->group(function () {
    
    // Akses: http://127.0.0.1:8000/dashboard/requester
    Route::get('/requester', function () {
        return view('dashboard.requester');
    });

    // Akses: http://127.0.0.1:8000/dashboard/helpdesk
    Route::get('/helpdesk', function () {
        return view('dashboard.helpdesk');
    });

    // Akses: http://127.0.0.1:8000/dashboard/technician
    Route::get('/technician', function () {
        return view('dashboard.technician');
    });
    Route::get('/supervisor', function () {
        return view('dashboard.supervisor');
    });
});

// Group Tiket (User Biasa)
Route::group([], function () {

    // 1. Tampilkan Form (GET)
    Route::get('/tickets/create', function () { 
        return view('tickets.create'); 
    })->name('tickets.create');

    // 2. Simpan Data (POST)
Route::post('/tickets', function () { 
    // Simpan pesan sukses ke session sementara
    session()->flash('success', 'Tiket berhasil dibuat! Teknisi akan segera mengecek.');
    
    return redirect()->route('tickets.index'); 
})->name('tickets.store');

    // 3. Tampilkan List (GET)
    Route::get('/tickets', function () { 
        return view('tickets.index'); 
    })->name('tickets.index');

    // 4. Detail (GET)
    Route::get('/tickets/{id}', function () { 
        return view('tickets.show'); 
    })->name('tickets.show');

    Route::get('/profile', function () { return view('profile.index'); })->name('profile');
});

// Group Helpdesk (Admin IT)
Route::prefix('helpdesk')->group(function () {
    // Akses: http://127.0.0.1:8000/helpdesk/incoming
    Route::get('/incoming', function () { return view('helpdesk.incoming'); })->name('helpdesk.incoming');
    
    // Akses: http://127.0.0.1:8000/helpdesk/technicians
    Route::get('/technicians', function () { return view('helpdesk.technicians'); })->name('helpdesk.technicians');
    
    // Akses: http://127.0.0.1:8000/helpdesk/all-tickets
    Route::get('/all-tickets', function () { return view('helpdesk.all_tickets'); })->name('helpdesk.all');
});


// Group Teknisi
Route::prefix('technician')->group(function () {
    
    // Dashboard Teknisi
    Route::get('/dashboard', function () {
        return view('dashboard.technician'); // Pastikan file dashboard.technician ada (copy dari helpdesk, ubah dikit)
    })->name('technician.dashboard');

    // Tugas Saya
    Route::get('/tasks', function () { 
        return view('technician.tasks'); 
    })->name('technician.tasks');

    // Riwayat
    Route::get('/history', function () { 
        return view('technician.history'); 
    })->name('technician.history');

    // Profil
    Route::get('/profile', function () { 
        return view('technician.profile'); 
    })->name('technician.profile');
});

// Group Super Admin
Route::prefix('superadmin')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard.superadmin');
    })->name('superadmin.dashboard');

    // Manajemen User
    Route::get('/users', function () { 
        return view('superadmin.users.index'); 
    })->name('superadmin.users');

    // Departemen (BARU)
    Route::get('/departments', function () { 
        return view('superadmin.departments.index'); 
    })->name('superadmin.departments');

    // Laporan Global (BARU)
    Route::get('/reports', function () { 
        return view('superadmin.reports.index'); 
    })->name('superadmin.reports');
});