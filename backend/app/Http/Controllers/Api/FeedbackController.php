<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\User;
use App\Events\FeedbackCreated;
use App\Events\NotificationSent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the feedbacks.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('orang_tua')) {
            $feedbacks = Feedback::where('user_id', $user->id)
                ->latest()
                ->get();
        } elseif ($user->hasAnyRole(['kepala_sekolah', 'admin_sekolah'])) {
            $feedbacks = Feedback::where('school_id', $user->school_id)
                ->latest()
                ->get();
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Forbidden: You do not have access to this resource.',
            ], 403);
        }

        // Map to format required by the frontend
        $formatted = $feedbacks->map(function ($item) {
            return [
                'id'       => $item->id,
                'judul'    => $item->title,
                'pesan'    => $item->content,
                'kategori' => $item->category,
                'kelas'    => $item->classroom,
                'tanggal'  => $item->created_at->toISOString(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => $formatted,
        ]);
    }

    /**
     * Store a newly created feedback in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasRole('orang_tua')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Forbidden: Only parents can send feedback.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'judul'    => 'required|string|max:255',
            'pesan'    => 'required|string',
            'kategori' => 'required|string|in:AKADEMIK,FASILITAS,PELAYANAN,KEUANGAN',
            'kelas'    => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Resolve school_id and foundation_id for parent user
        $schoolId = $user->school_id;
        $foundationId = $user->foundation_id;

        if (!$schoolId || !$foundationId) {
            $parentProfile = $user->parentProfile;
            if ($parentProfile) {
                $firstChild = $parentProfile->children()->with('user.school')->first();
                if ($firstChild && $firstChild->user) {
                    $schoolId = $schoolId ?: $firstChild->user->school_id;
                    $foundationId = $foundationId ?: ($firstChild->user->school ? $firstChild->user->school->foundation_id : null);
                }
            }
        }

        if (!$schoolId || !$foundationId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menentukan unit sekolah atau yayasan untuk akun Anda.',
            ], 400);
        }

        $feedback = Feedback::create([
            'user_id'       => $user->id,
            'foundation_id' => $foundationId,
            'school_id'     => $schoolId,
            'title'         => $request->judul,
            'content'       => $request->pesan,
            'category'      => $request->kategori,
            'classroom'     => $request->kelas,
        ]);

        // 1. Broadcast realtime event for Feedback Table update
        event(new FeedbackCreated($feedback));

        // 2. Find kepala_sekolah and admin_sekolah recipients in the same school
        $recipients = User::where('school_id', $schoolId)
            ->whereHas('role', function ($q) {
                $q->whereIn('name', ['kepala_sekolah', 'admin_sekolah']);
            })
            ->get();

        foreach ($recipients as $recipient) {
            $notification = Notification::create([
                'user_id' => $recipient->id,
                'title'   => 'Aduan Baru: ' . $feedback->title,
                'content' => Str::limit(strip_tags($feedback->content), 150),
                'type'    => 'FEEDBACK',
                'data'    => [
                    'feedback_id'  => $feedback->id,
                    'category'     => $feedback->category,
                    'sender_class' => $feedback->classroom,
                ],
            ]);

            // Broadcast notification badge & toast realtime via Reverb/Pusher
            event(new NotificationSent($notification));
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Feedback sent and broadcasted successfully.',
            'data'    => [
                'id'       => $feedback->id,
                'judul'    => $feedback->title,
                'pesan'    => $feedback->content,
                'kategori' => $feedback->category,
                'kelas'    => $feedback->classroom,
                'tanggal'  => $feedback->created_at->toISOString(),
            ],
        ], 201);
    }
}
