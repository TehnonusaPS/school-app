<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Announcement;
use App\Models\Notification;
use App\Models\User;
use App\Events\NotificationSent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the announcements.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('admin_yayasan')) {
            // Yayasan admin sees all announcements created for their foundation
            $announcements = Announcement::with('targetSchool:id,name')
                ->where('foundation_id', $user->foundation_id)
                ->latest()
                ->get();

            return response()->json([
                'status' => 'success',
                'data'   => $announcements,
            ]);
        }

        if ($user->hasAnyRole(['kepala_sekolah', 'admin_sekolah'])) {
            // Fetch the school first
            $school = $user->school;
            if (!$school) {
                return response()->json([
                    'status' => 'success',
                    'data'   => [],
                ]);
            }

            // School users see announcements matching their foundation and targeted to either their school or all schools (null)
            $announcements = Announcement::with('targetSchool:id,name')
                ->where('foundation_id', $school->foundation_id)
                ->where(function ($q) use ($school) {
                    $q->where('target_school_id', $school->id)
                      ->orWhereNull('target_school_id');
                })
                ->latest()
                ->get();

            return response()->json([
                'status' => 'success',
                'data'   => $announcements,
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Forbidden: You do not have the required access role.',
        ], 403);
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->hasRole('admin_yayasan')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Forbidden: Only admin yayasan can create announcements.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title'            => 'required|string|max:255',
            'content'          => 'required|string',
            'category'         => 'required|string|in:UMUM,AKADEMIK,KEUANGAN',
            'target_school_id' => 'nullable|exists:schools,id',
            'publish_date'     => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $announcement = Announcement::create([
            'foundation_id'    => $user->foundation_id,
            'created_by'       => $user->id,
            'title'            => $request->title,
            'content'          => $request->content,
            'category'         => $request->category,
            'target_school_id' => $request->target_school_id,
            'publish_date'     => $request->publish_date,
        ]);

        // Dispatch notifications to target recipients (kepala_sekolah and admin_sekolah)
        $recipientQuery = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['kepala_sekolah', 'admin_sekolah']);
        });

        if ($announcement->target_school_id) {
            $recipientQuery->where('school_id', $announcement->target_school_id);
        } else {
            $recipientQuery->whereHas('school', function ($q) use ($user) {
                $q->where('foundation_id', $user->foundation_id);
            });
        }

        $recipients = $recipientQuery->get();

        foreach ($recipients as $recipient) {
            $notification = Notification::create([
                'user_id' => $recipient->id,
                'title'   => 'Pengumuman Baru: ' . $announcement->title,
                'content' => Str::limit(strip_tags($announcement->content), 150),
                'type'    => 'ANNOUNCEMENT',
                'data'    => [
                    'announcement_id' => $announcement->id,
                    'category'        => $announcement->category,
                    'creator_name'    => $user->name,
                ],
            ]);

            // Broadcast realtime via Reverb
            event(new NotificationSent($notification));
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Announcement created and broadcasted successfully.',
            'data'    => $announcement->load('targetSchool:id,name'),
        ], 201);
    }

    /**
     * Display the specified announcement.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $announcement = Announcement::with('targetSchool:id,name', 'creator:id,name')->find($id);

        if (!$announcement) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Announcement not found.',
            ], 404);
        }

        // Validate access
        if ($user->hasRole('admin_yayasan') && $user->foundation_id == $announcement->foundation_id) {
            return response()->json([
                'status' => 'success',
                'data'   => $announcement,
            ]);
        }

        if ($user->hasAnyRole(['kepala_sekolah', 'admin_sekolah'])) {
            $school = $user->school;
            if ($school && $school->foundation_id == $announcement->foundation_id && 
                (is_null($announcement->target_school_id) || $announcement->target_school_id == $school->id)) {
                return response()->json([
                    'status' => 'success',
                    'data'   => $announcement,
                ]);
            }
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Forbidden: You do not have access to this announcement.',
        ], 403);
    }

    /**
     * Update the specified announcement in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Announcement not found.',
            ], 404);
        }

        if (!$user->hasRole('admin_yayasan') || $user->foundation_id != $announcement->foundation_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Forbidden: You do not have permissions to update this announcement.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title'            => 'required|string|max:255',
            'content'          => 'required|string',
            'category'         => 'required|string|in:UMUM,AKADEMIK,KEUANGAN',
            'target_school_id' => 'nullable|exists:schools,id',
            'publish_date'     => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $announcement->update([
            'title'            => $request->title,
            'content'          => $request->content,
            'category'         => $request->category,
            'target_school_id' => $request->target_school_id,
            'publish_date'     => $request->publish_date,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Announcement updated successfully.',
            'data'    => $announcement->load('targetSchool:id,name'),
        ]);
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Announcement not found.',
            ], 404);
        }

        if (!$user->hasRole('admin_yayasan') || $user->foundation_id != $announcement->foundation_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Forbidden: You do not have permissions to delete this announcement.',
            ], 403);
        }

        $announcement->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Announcement deleted successfully.',
        ]);
    }
}
