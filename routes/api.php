<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\UserManagementController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

Route::middleware('auth:sanctum')->group(function () {
    // requester
    Route::post('/tickets', [TicketController::class, 'store'])
        ->middleware('permission:ticket.create');

    // all roles (filtered by policy)
    Route::get('/tickets', [TicketController::class, 'index'])
        ->middleware('permission:ticket.view');

    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
        ->middleware('permission:ticket.view');

    // supervisor / admin (helpdesk can also assign)
    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign'])
        ->middleware('permission:ticket.assign');

    // technician - confirm or reject assigned ticket
    Route::post('/tickets/{ticket}/confirm', [TicketController::class, 'confirm'])
        ->middleware('permission:ticket.change_status');
    
    Route::post('/tickets/{ticket}/reject', [TicketController::class, 'reject'])
        ->middleware('permission:ticket.change_status');

    // technician - resolve ticket
    Route::post('/tickets/{ticket}/solve', [TicketController::class, 'solve'])
        ->middleware('permission:ticket.resolve');

    // helpdesk / admin - unresolve or close ticket
    Route::post('/tickets/{ticket}/unresolve', [TicketController::class, 'unresolve'])
        ->middleware('permission:ticket.assign');

    // admin / supervisor
    Route::post('/tickets/{ticket}/close', [TicketController::class, 'close'])
        ->middleware('permission:ticket.close');
});

// User Management - GET endpoints (Master Admin + Helpdesk)
Route::middleware('auth:sanctum', 'permission:user.view')->group(function () {
    Route::get('/users/available-roles', [UserManagementController::class, 'getAvailableRoles']);
    Route::get('/users/by-role/{roleName}', [UserManagementController::class, 'getUsersByRole']);
    Route::get('/users/roles-summary', [UserManagementController::class, 'getRolesSummary']);
    Route::get('/users', [UserManagementController::class, 'index']);
    Route::get('/users/{user}', [UserManagementController::class, 'show']);
});

// User Management - POST/PUT/DELETE endpoints (Master Admin Only)
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('users')->group(function () {
        Route::post('/', [UserManagementController::class, 'store'])
            ->middleware('permission:user.create');
        Route::put('/{user}', [UserManagementController::class, 'update'])
            ->middleware('permission:user.update');
        Route::post('/{user}/reset-password', [UserManagementController::class, 'resetPassword'])
            ->middleware('permission:user.create');
    });
});