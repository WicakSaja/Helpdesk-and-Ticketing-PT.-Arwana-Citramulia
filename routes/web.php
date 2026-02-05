<?php

use Illuminate\Support\Facades\Route;

// ====================================================
// ROUTING BLADE VIEW - CLIENT-SIDE AUTHENTICATION
// ====================================================
// Semua authentication ditangani via API dan session storage
// Token dan role dari API response disimpan di client-side
// Routing ini hanya menampilkan halaman blade

// 1. LANDING PAGE
Route::get('/', function () {
    return view('landing_page');
})->name('home');

// 2. AUTH PAGES (Login & Register Form)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Logout Route
Route::post('/logout', function () {
    // Client-side akan handle clear sessionStorage
    // Route ini hanya untuk redirect
    return redirect()->route('login')->with('message', 'Anda telah logout');
})->name('logout');

Route::get('/logout', function () {
    // Jika ada yang akses via GET, redirect ke login
    return redirect()->route('login');
});

// 3. DASHBOARD ROUTES (Per Role)
Route::prefix('dashboard')->name('dashboard.')->group(function () {
    
    // Dashboard Requester
    Route::get('/requester', function () {
        return view('dashboard.requester');
    })->name('requester');

    // Dashboard Helpdesk
    Route::get('/helpdesk', function () {
        return view('dashboard.helpdesk');
    })->name('helpdesk');

    // Dashboard Technician
    Route::get('/technician', function () {
        return view('dashboard.technician');
    })->name('technician');
    
    // Dashboard Super Admin
    Route::get('/superadmin', function () {
        return view('dashboard.superadmin');
    })->name('superadmin');
});

// 4. TICKET ROUTES
Route::prefix('tickets')->name('tickets.')->group(function () {
    
    // List Tickets
    Route::get('/', function () { 
        return view('tickets.index'); 
    })->name('index');
    
    // Create Ticket Form
    Route::get('/create', function () { 
        return view('tickets.create'); 
    })->name('create');
    
    // Ticket Detail
    // Route::get('/{id}', function ($id) { 
    //     return view('tickets.show', ['ticketId' => $id]); 
    // })->name('show');
    Route::get('/{id}', function ($id) { 
        return view('tickets.detail', ['ticket_id' => $id]); 
    })->name('detail');
});

// 5. HELPDESK ROUTES
Route::prefix('helpdesk')->name('helpdesk.')->group(function () {
    
    // Incoming Tickets
    Route::get('/incoming', function () { 
        return view('helpdesk.incoming'); 
    })->name('incoming');
    
    // Technicians Management
    Route::get('/technicians', function () { 
        return view('helpdesk.technicians'); 
    })->name('technicians');
    
    // All Tickets
    Route::get('/all-tickets', function () { 
        return view('helpdesk.all_tickets'); 
    })->name('all');

    // Halaman untuk melihat detail tiket (tanpa tombol aksi close/reject)
    Route::get('/tickets/{id}', function ($id) {
        return view('helpdesk.detail', ['ticket_id' => $id]); 
    })->name('tickets.detail');

    // Halaman gabungan untuk aksi Reject & Close tiket
    Route::get('/actions', function () {
        return view('helpdesk.actions');
    })->name('actions');
});

// 6. TECHNICIAN ROUTES
Route::prefix('technician')->name('technician.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard.technician');
    })->name('dashboard');

    // Tasks
    Route::get('/tasks', function () { 
        return view('technician.tasks'); 
    })->name('tasks');

    // History
    Route::get('/history', function () { 
        return view('technician.history'); 
    })->name('history');

    // Profile
    Route::get('/profile', function () { 
        return view('technician.profile'); 
    })->name('profile');

    Route::get('/tickets/{id}', function ($id) {
        return view('technician.detail', ['ticket_id' => $id]);
    })->name('ticket.detail');
});

// 7. SUPERADMIN ROUTES
Route::prefix('superadmin')->name('superadmin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard.superadmin');
    })->name('dashboard');

    // User Management
    Route::get('/users', function () { 
        return view('superadmin.users.index'); 
    })->name('users');

    // Departments
    Route::get('/departments', function () { 
        return view('superadmin.departments.index'); 
    })->name('departments');

    // Reports
    Route::get('/reports', function () { 
        return view('superadmin.reports.index'); 
    })->name('reports');

    Route::get('/tickets/{id}', function ($id) {
        return view('superadmin.detail', ['ticket_id' => $id]);
    })->name('ticket.detail');
});

// 8. PROFILE ROUTE
Route::get('/profile', function () { 
    return view('tickets.profile'); 
})->name('profile');
