<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketController;
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

    // supervisor / admin
    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign'])
        ->middleware('permission:ticket.assign');

    // technician
    Route::post('/tickets/{ticket}/solve', [TicketController::class, 'solve'])
        ->middleware('permission:ticket.solve');

    // admin / supervisor
    Route::post('/tickets/{ticket}/close', [TicketController::class, 'close'])
        ->middleware('permission:ticket.close');
});