<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
// Import Controller dari folder Web (Namespace Baru)
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\TicketController;
use App\Http\Controllers\Web\DepartmentController;

/*
|--------------------------------------------------------------------------
| Web Routes (Frontend Blade -> Proxy to API)
|--------------------------------------------------------------------------
*/

// 1. LANDING PAGE
Route::get('/', function () {
    return view('landing_page'); // Pastikan file resources/views/landing_page.blade.php ada
})->name('home');

// 2. GUEST ROUTES (Login & Register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// 3. AUTH ROUTES (Logout & Dashboard Pintar)
Route::group([], function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout']); // Fallback method GET

    // SMART DASHBOARD ROUTER
    Route::get('/dashboard', function () {
        
        // Cek Session
        if (!Session::has('user_data')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $roles = Session::get('user_roles', []);

        // Logic Redirect Sesuai Role
        if (in_array('Super Admin', $roles) || in_array('Super Admin', $roles)) {
            return redirect()->route('superadmin.dashboard');
        }
        if (in_array('Helpdesk', $roles)) {
            return redirect()->route('helpdesk.incoming');
        }
        if (in_array('Technician', $roles) || in_array('Teknisi', $roles)) {
            return redirect()->route('technician.dashboard');
        }

        // Default: Requester
        return view('dashboard.requester');

    })->name('dashboard');
});

// 4. GROUP SUPER ADMIN
Route::prefix('superadmin')->group(function () {
    Route::get('/dashboard', function () { return view('dashboard.superadmin'); })->name('superadmin.dashboard');
    Route::get('/users', function () { return view('superadmin.users.index'); })->name('superadmin.users');
    
    Route::get('/departments', [DepartmentController::class, 'index'])->name('superadmin.departments');
    Route::post('/departments/save', [DepartmentController::class, 'store'])->name('superadmin.departments.store');
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy'])->name('superadmin.departments.delete');

    Route::get('/reports', function () { return view('superadmin.reports.index'); })->name('superadmin.reports');
});

// 5. GROUP TEKNISI
Route::prefix('technician')->group(function () {
    Route::get('/dashboard', function () { return view('dashboard.technician'); })->name('technician.dashboard');
    Route::get('/tasks', function () { return view('technician.tasks'); })->name('technician.tasks');
    Route::get('/history', function () { return view('technician.history'); })->name('technician.history');
    Route::get('/profile', function () { return view('technician.profile'); })->name('technician.profile');
});

// 6. GROUP HELPDESK
Route::prefix('helpdesk')->group(function () {
    Route::get('/incoming', function () { return view('helpdesk.incoming'); })->name('helpdesk.incoming');
    Route::get('/technicians', function () { return view('helpdesk.technicians'); })->name('helpdesk.technicians');
    Route::get('/all-tickets', function () { return view('helpdesk.all_tickets'); })->name('helpdesk.all');
});

// 7. GROUP USER (Requester) & TICKETS
// Pastikan Middleware ini mengecek session 'api_token' nanti
Route::group([], function () {
    
    // Ticket Routes mengarah ke Web\TicketController
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');

    Route::get('/profile', function () { return view('profile.index'); })->name('profile');
});