<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\StudentProfile;
use App\Models\TeacherSubjectAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AkademikDataController extends Controller
{
    /**
     * Get active academic year for the logged-in teacher's school.
     */
    public function getActiveAcademicYear(Request $request): JsonResponse
    {
        $user = $request->user();
        $academicYear = AcademicYear::where('school_id', $user->school_id)
            ->where('is_active', true)
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => $academicYear,
        ]);
    }

    /**
     * Get subjects taught by the logged-in teacher.
     */
    public function getMySubjects(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get subjects from teacher_subject_assignments
        $assignments = TeacherSubjectAssignment::with('subject')
            ->where('teacher_id', $user->id)
            ->where('is_active', true)
            ->get();

        $subjects = $assignments->map(function ($assignment) {
            return $assignment->subject;
        })->filter()->unique('id')->values();

        return response()->json([
            'status' => 'success',
            'data' => $subjects,
        ]);
    }

    /**
     * Get classrooms taught by the logged-in teacher for a specific subject.
     */
    public function getMyClassrooms(Request $request, string $subjectId): JsonResponse
    {
        $user = $request->user();

        $assignments = TeacherSubjectAssignment::with('classroom')
            ->where('teacher_id', $user->id)
            ->where('subject_id', $subjectId)
            ->where('is_active', true)
            ->get();

        $classrooms = $assignments->map(function ($assignment) {
            return $assignment->classroom;
        })->filter()->unique('id')->values();

        return response()->json([
            'status' => 'success',
            'data' => $classrooms,
        ]);
    }

    /**
     * Get students in a classroom.
     */
    public function getStudentsByClassroom(Request $request, string $classroomId): JsonResponse
    {
        $students = StudentProfile::with('user:id,name')
            ->where('classroom_id', $classroomId)
            ->where('status', 'active')
            ->get();

        // Map data to make it consistent with frontend expected format
        $data = $students->map(function ($profile) {
            return [
                'id' => $profile->id,
                'user_id' => $profile->user_id,
                'name' => $profile->user ? $profile->user->name : 'No Name',
                'nisn' => $profile->nisn,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}
