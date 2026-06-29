<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SuperAdmin\FinanceController;
use App\Http\Controllers\Api\FoundationController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\UserController;
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
    Route::middleware('role:superadmin,admin_yayasan,admin_sekolah')->prefix('management')->group(function () {
        Route::get('/roles', [UserController::class, 'getRoles']);
        Route::apiResource('/foundations', FoundationController::class);
        Route::apiResource('/schools', SchoolController::class);
        Route::apiResource('/users', UserController::class);
    });
});

