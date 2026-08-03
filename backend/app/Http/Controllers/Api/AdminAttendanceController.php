<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\School;
use App\Models\Notification;
use App\Events\NotificationSent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminAttendanceController extends Controller
{
    /**
     * Update school attendance late threshold settings.
     */
    public function updateThreshold(Request $request): JsonResponse
    {
        $admin = $request->user();
        if (!$admin->school_id) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terikat dengan sekolah manapun.'
            ], 403);
        }

        $request->validate([
            'attendance_late_threshold' => 'required|date_format:H:i',
        ]);

        $school = School::findOrFail($admin->school_id);
        
        // Append seconds to H:i format
        $school->attendance_late_threshold = $request->attendance_late_threshold . ':00';
        $school->save();

        return response()->json([
            'success' => true,
            'message' => 'Batas waktu jam masuk berhasil diperbarui.',
            'data'    => [
                'attendance_late_threshold' => substr($school->attendance_late_threshold, 0, 5)
            ]
        ]);
    }

    /**
     * Get school threshold settings.
     */
    public function getSettings(Request $request): JsonResponse
    {
        $admin = $request->user();
        if (!$admin->school_id) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terikat dengan sekolah manapun.'
            ], 403);
        }

        $school = School::findOrFail($admin->school_id);

        return response()->json([
            'success' => true,
            'data'    => [
                'attendance_late_threshold' => substr($school->attendance_late_threshold ?? '07:30:00', 0, 5)
            ]
        ]);
    }

    /**
     * Get all pending leave requests for the admin's school.
     */
    public function getPendingLeaves(Request $request): JsonResponse
    {
        $admin = $request->user();
        if (!$admin->school_id) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terikat dengan sekolah manapun.'
            ], 403);
        }

        $leaves = LeaveRequest::where('school_id', $admin->school_id)
            ->with(['user.role', 'user.teacherProfile'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $leaves
        ]);
    }

    /**
     * Approve or reject a leave request.
     */
    public function approveRejectLeave(Request $request, $id): JsonResponse
    {
        $admin = $request->user();
        if (!$admin->school_id) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terikat dengan sekolah manapun.'
            ], 403);
        }

        $request->validate([
            'status'           => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000',
        ]);

        $leave = LeaveRequest::where('school_id', $admin->school_id)
            ->findOrFail($id);

        $leave->status = $request->status;
        $leave->approved_by = $admin->id;
        
        if ($request->status === 'rejected') {
            $leave->rejection_reason = $request->rejection_reason;
        } else {
            $leave->rejection_reason = null;
        }
        
        $leave->save();

        // Create notification for the requester
        $statusLabel = $request->status === 'approved' ? 'DISETUJUI' : 'DITOLAK';
        $title = "Pengajuan {$leave->type} Anda {$statusLabel}";
        $content = "Pengajuan {$leave->type} Anda untuk tanggal " . 
            CarbonFormat($leave->start_date) . " s/d " . CarbonFormat($leave->end_date) . 
            ($request->status === 'rejected' ? " ditolak dengan alasan: {$request->rejection_reason}" : " telah disetujui oleh Admin Sekolah.");

        $notification = Notification::create([
            'user_id' => $leave->user_id,
            'title'   => $title,
            'content' => $content,
            'type'    => 'INTERNAL',
            'data'    => [
                'leave_id' => $leave->id,
                'status'   => $leave->status,
            ],
        ]);

        // Broadcast realtime notification
        if (class_exists(\App\Events\NotificationSent::class)) {
            event(new NotificationSent($notification));
        }

        return response()->json([
            'success' => true,
            'message' => "Pengajuan {$leave->type} berhasil " . ($request->status === 'approved' ? 'disetujui' : 'ditolak') . ".",
            'data'    => $leave
        ]);
    }
}

// Simple helper inside namespace if global helper doesn't exist
function CarbonFormat($date) {
    return \Carbon\Carbon::parse($date)->translatedFormat('d M Y');
}
