<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SuperAdmin\FinanceController;
use App\Http\Controllers\Api\FoundationController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\ClassroomController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\ExtracurricularController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\AcademicYearController;
<<<<<<< Updated upstream
=======
use Illuminate\Support\Facades\Broadcast;
>>>>>>> Stashed changes
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
|
*/

// routes/api.php

use Illuminate\Support\Facades\DB;

Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();

        return response()->json([
            'status' => 'connected'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

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

    // Management Data Routes (Yayasan, Sekolah & Pengguna)
    Route::middleware('role:superadmin,admin_yayasan,admin_sekolah,kepala_sekolah,tata_usaha,wali_kelas')->prefix('management')->group(function () {
        Route::get('/roles', [UserController::class, 'getRoles']);
        Route::apiResource('/foundations', FoundationController::class);
        Route::apiResource('/schools', SchoolController::class);
        Route::apiResource('/users', UserController::class);
        Route::apiResource('/students', StudentController::class);
        Route::apiResource('/classrooms', ClassroomController::class);
        Route::apiResource('/teachers', TeacherController::class);
        Route::apiResource('/extracurriculars', ExtracurricularController::class);
        Route::apiResource('/subjects', SubjectController::class);
        Route::apiResource('/academic-years', AcademicYearController::class);
    });
});

