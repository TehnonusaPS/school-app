<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SuperAdmin\FinanceController;
use Illuminate\Support\Facades\Route;

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

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Super Admin Finance Routes
    Route::middleware('role:superadmin')->prefix('superadmin/finance')->group(function () {
        Route::get('/dashboard', [FinanceController::class, 'indexDashboard']);
        Route::get('/foundations', [FinanceController::class, 'getFoundations']);
        
        // Plans CRUD
        Route::get('/plans', [FinanceController::class, 'plansIndex']);
        Route::post('/plans', [FinanceController::class, 'plansStore']);
        Route::get('/plans/{plan}', [FinanceController::class, 'plansShow']);
        Route::put('/plans/{plan}', [FinanceController::class, 'plansUpdate']);
        Route::delete('/plans/{plan}', [FinanceController::class, 'plansDestroy']);

        // Subscriptions
        Route::get('/subscriptions', [FinanceController::class, 'getSubscriptions']);
        Route::delete('/subscriptions/{subscription}', [FinanceController::class, 'subscriptionsDestroy']);

        // Payments / Invoicing
        Route::get('/payments', [FinanceController::class, 'getPayments']);
        Route::post('/payments', [FinanceController::class, 'createInvoice']);
        Route::post('/payments/{id}/verify', [FinanceController::class, 'verifyPayment']);
    });
});
