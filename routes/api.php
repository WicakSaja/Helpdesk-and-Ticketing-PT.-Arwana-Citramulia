<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\UserManagementController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// ============================================================================
// AUTHENTICATION ROUTES
// ============================================================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// ============================================================================
// DASHBOARD ROUTES
// ============================================================================
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:master-admin|supervisor|helpdesk|technician|requester');
});

// ============================================================================
// TICKET MANAGEMENT ROUTES
// ============================================================================
Route::middleware('auth:sanctum')->group(function () {
    // Create & View Tickets
    Route::post('/tickets', [TicketController::class, 'store'])
        ->middleware('permission:ticket.create');
    
    Route::get('/tickets', [TicketController::class, 'index'])
        ->middleware('permission:ticket.view');
    
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
        ->middleware('permission:ticket.view');
    
    Route::get('/my-tickets', [TicketController::class, 'myTickets']);
    
    Route::get('/tickets/by-status/{status}', [TicketController::class, 'byStatus'])
        ->middleware('permission:ticket.view');
    
    Route::get('/tickets/{ticket}/completion-history', [TicketController::class, 'completionHistory'])
        ->middleware('permission:ticket.view');

    // Ticket Assignment (Supervisor/Admin/Helpdesk)
    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign'])
        ->middleware('permission:ticket.assign');

    // Technician Actions
    Route::post('/tickets/{ticket}/confirm', [TicketController::class, 'confirm'])
        ->middleware('permission:ticket.change_status');
    
    Route::post('/tickets/{ticket}/reject', [TicketController::class, 'reject'])
        ->middleware('permission:ticket.change_status');
    
    Route::post('/tickets/{ticket}/solve', [TicketController::class, 'solve'])
        ->middleware('permission:ticket.resolve');

    // Admin/Supervisor Actions
    Route::post('/tickets/{ticket}/unresolve', [TicketController::class, 'unresolve'])
        ->middleware('permission:ticket.assign');
    
    Route::post('/tickets/{ticket}/close', [TicketController::class, 'close'])
        ->middleware('permission:ticket.close');
});

// ============================================================================
// USER MANAGEMENT ROUTES
// ============================================================================
// GET Endpoints (Master Admin + Helpdesk)
Route::middleware('auth:sanctum', 'permission:user.view')->group(function () {
    Route::get('/users/by-role/{roleName}', [UserManagementController::class, 'getUsersByRole']);
    Route::get('/users/{user}', [UserManagementController::class, 'show'])
        ->whereNumber('user');
    Route::get('/users/{user}/resolved-tickets', [UserManagementController::class, 'resolvedTickets'])
        ->whereNumber('user');
    Route::get('/technicians/active', [UserManagementController::class, 'getActiveTechnicians']);
});

// POST/PUT/DELETE Endpoints (Master Admin Only)
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('/roles-summary', [UserManagementController::class, 'getRolesSummary'])
            ->middleware('permission:user.view-all');
        
        Route::get('/', [UserManagementController::class, 'index'])
            ->middleware('permission:user.view-all');
        
        Route::get('/available-roles', [UserManagementController::class, 'getAvailableRoles'])
            ->middleware('permission:user.view-all');
        
        Route::post('/', [UserManagementController::class, 'store'])
            ->middleware('permission:user.create');
        
        Route::put('/{user}', [UserManagementController::class, 'update'])
            ->middleware('permission:user.update');

        Route::patch('/{user}/status', [UserManagementController::class, 'updateStatus'])
            ->middleware('permission:user.update');
        
        Route::post('/{user}/reset-password', [UserManagementController::class, 'resetPassword'])
            ->middleware('permission:user.create');
    });
});

// ============================================================================
// DEPARTMENT MANAGEMENT ROUTES
// ============================================================================
// Public GET Endpoints
Route::get('/departments', [DepartmentController::class, 'index']);
Route::get('/departments/{department}', [DepartmentController::class, 'show']);

// Protected POST/PUT/PATCH/DELETE Endpoints (Master Admin & Helpdesk)
Route::middleware('auth:sanctum', 'role:master-admin|helpdesk')->group(function () {
    Route::post('/departments', [DepartmentController::class, 'store']);
    Route::put('/departments/{department}', [DepartmentController::class, 'update']);
    Route::patch('/departments/{department}', [DepartmentController::class, 'update']);
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy']);
});

// ============================================================================
// CATEGORY MANAGEMENT ROUTES
// ============================================================================
// Public GET Endpoints
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

// Protected POST/PUT/PATCH/DELETE Endpoints (Master Admin & Helpdesk)
Route::middleware('auth:sanctum', 'role:master-admin|helpdesk')->group(function () {
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::patch('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
});