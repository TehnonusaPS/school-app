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
use Illuminate\Support\Facades\Broadcast;
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

Route::get('/env-check', function () {
    return [
        'DB_CONNECTION' => env('DB_CONNECTION'),
        'DB_HOST' => env('DB_HOST'),
        'DB_PORT' => env('DB_PORT'),
        'DB_DATABASE' => env('DB_DATABASE'),
    ];
});

Route::get('/test-broadcast', function () {
    try {
        $broadcaster = config('broadcasting.default');
        $pusherConfig = config('broadcasting.connections.pusher');
        
        // Find a message to use, or create a mock one
        $message = \App\Models\Message::latest()->first();
        if (!$message) {
            $message = new \App\Models\Message();
            $message->sender_id = 1;
            $message->receiver_id = 2;
            $message->message = "Test Message";
        }
        
        $result = event(new \App\Events\MessageSent($message));
        
        return response()->json([
            'status' => 'success',
            'broadcaster' => $broadcaster,
            'pusher_app_id' => !empty($pusherConfig['app_id']) ? 'configured' : 'NOT configured',
            'pusher_key' => !empty($pusherConfig['key']) ? 'configured' : 'NOT configured',
            'pusher_secret' => !empty($pusherConfig['secret']) ? 'configured' : 'NOT configured',
            'pusher_cluster' => !empty($pusherConfig['options']['cluster']) ? $pusherConfig['options']['cluster'] : 'NOT configured',
            'event_dispatch_result' => $result,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
});

// Broadcasting auth via Sanctum token (for Laravel Echo with Bearer token)
Route::post('/broadcasting/auth', [\Illuminate\Broadcasting\BroadcastController::class, 'authenticate'])
    ->middleware('auth:sanctum')
    ->name('broadcasting.auth');

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Chat routes
    Route::get('/chat/contacts', [\App\Http\Controllers\Api\ChatController::class, 'getContacts']);
    Route::get('/chat/unread-count', [\App\Http\Controllers\Api\ChatController::class, 'getUnreadCount']);
    Route::get('/chat/messages/{recipient_id}', [\App\Http\Controllers\Api\ChatController::class, 'getMessages']);
    Route::post('/chat/messages', [\App\Http\Controllers\Api\ChatController::class, 'sendMessage']);
    Route::post('/chat/messages/{message_id}/read', [\App\Http\Controllers\Api\ChatController::class, 'markAsRead']);

    // Announcement routes
    Route::apiResource('/announcements', \App\Http\Controllers\Api\AnnouncementController::class);

    // Notification routes
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);

    // Feedback routes
    Route::apiResource('/feedbacks', \App\Http\Controllers\Api\FeedbackController::class);

    // Active Student Certificate routes (Persuratan Aktif)
    Route::apiResource('/komunikasi/surat-aktif', \App\Http\Controllers\Api\ActiveStudentCertificateController::class);
    Route::apiResource('/komunikasi/surat-dispensasi', \App\Http\Controllers\Api\StudentDispensationCertificateController::class);
    Route::apiResource('/komunikasi/surat-peringatan', \App\Http\Controllers\Api\StudentWarningCertificateController::class);
    Route::apiResource('/komunikasi/berita-kegiatan', \App\Http\Controllers\Api\ActivityNewsController::class);

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
        Route::get('/subscriptions/{subscription}', [FinanceController::class, 'subscriptionsShow']);
        Route::put('/subscriptions/{subscription}', [FinanceController::class, 'subscriptionsUpdate']);
        Route::delete('/subscriptions/{subscription}', [FinanceController::class, 'subscriptionsDestroy']);

        // Payments / Invoicing
        Route::get('/payments', [FinanceController::class, 'getPayments']);
        Route::post('/payments', [FinanceController::class, 'createInvoice']);
        Route::post('/payments/{id}/verify', [FinanceController::class, 'verifyPayment']);
    });

    // Management Data Routes (Yayasan, Sekolah & Pengguna)
    Route::middleware('role:superadmin,admin_yayasan,admin_sekolah,tata_usaha,kepala_sekolah')->prefix('management')->group(function () {
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

    // Akademik Routes (Guru, Wali Kelas, Admin Sekolah, Kepala Sekolah)
    Route::middleware('role:guru,wali_kelas,admin_sekolah,kepala_sekolah')
        ->prefix('akademik')
        ->group(function () {
            // Data lookup
            Route::get('/my-subjects', [\App\Http\Controllers\Api\AkademikDataController::class, 'getMySubjects']);
            Route::get('/subjects/{subjectId}/my-classrooms', [\App\Http\Controllers\Api\AkademikDataController::class, 'getMyClassrooms']);
            Route::get('/classrooms/{id}/students', [\App\Http\Controllers\Api\AkademikDataController::class, 'getStudentsByClassroom']);
            Route::get('/active-academic-year', [\App\Http\Controllers\Api\AkademikDataController::class, 'getActiveAcademicYear']);

            // Materi Pelajaran
            Route::apiResource('/materials', \App\Http\Controllers\Api\SubjectMaterialController::class);
            Route::get('/materials/{id}/download', [\App\Http\Controllers\Api\SubjectMaterialController::class, 'download']);
            Route::patch('/materials/{id}/toggle-status', [\App\Http\Controllers\Api\SubjectMaterialController::class, 'toggleStatus']);

            // Penilaian (Tugas & Ujian digabung)
            Route::apiResource('/assessments', \App\Http\Controllers\Api\AssessmentController::class);
            Route::patch('/assessments/{id}/toggle-status', [\App\Http\Controllers\Api\AssessmentController::class, 'toggleStatus']);
        });

    Route::middleware('role:siswa')
        ->prefix('siswa/akademik')
        ->group(function () {
            Route::get('/my-classrooms', [\App\Http\Controllers\Api\SiswaAkademikController::class, 'getMyClassrooms']);
            Route::get('/subjects', [\App\Http\Controllers\Api\SiswaAkademikController::class, 'getSubjects']);
            Route::get('/overview', [\App\Http\Controllers\Api\SiswaAkademikController::class, 'getSubjectOverview']);
            Route::get('/stats', [\App\Http\Controllers\Api\SiswaAkademikController::class, 'getGlobalStats']);
            Route::get('/materials/{id}/download', [\App\Http\Controllers\Api\SubjectMaterialController::class, 'download']);
        });

    // Student SPP & Finance routes
    Route::prefix('finance')->group(function () {
        Route::get('/spp/dashboard', [\App\Http\Controllers\Api\SppController::class, 'getDashboard']);
        Route::get('/spp/bills', [\App\Http\Controllers\Api\SppController::class, 'getBills']);
        Route::get('/spp/payments', [\App\Http\Controllers\Api\SppController::class, 'getPayments']);
        Route::get('/spp/payments/{id}', [\App\Http\Controllers\Api\SppController::class, 'getPaymentDetails']);
        Route::post('/spp/payments', [\App\Http\Controllers\Api\SppController::class, 'createPayment']);
        Route::post('/spp/payments/{id}/verify', [\App\Http\Controllers\Api\SppController::class, 'verifyPayment']);
        
        // Tariffs CRUD
        Route::get('/spp/tariffs', [\App\Http\Controllers\Api\SppController::class, 'getTariffs']);
        Route::post('/spp/tariffs', [\App\Http\Controllers\Api\SppController::class, 'storeTariff']);
        Route::put('/spp/tariffs/{id}', [\App\Http\Controllers\Api\SppController::class, 'updateTariff']);
        Route::delete('/spp/tariffs/{id}', [\App\Http\Controllers\Api\SppController::class, 'deleteTariff']);
    });
});

