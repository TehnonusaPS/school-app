<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\HasSchoolScope;
use App\Models\AcademicCalendarEvent;
use App\Models\Classroom;
use App\Models\ExamSession;
use App\Models\ExamSessionSubject;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExamScheduleController extends Controller
{
    use HasSchoolScope;

    /**
     * Get exam sessions for an academic calendar event.
     */
    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);
        if ($schoolId === -1) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized school access.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'academic_calendar_event_id' => 'required|exists:academic_calendar_events,id',
            'grade'                      => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $eventId = $request->input('academic_calendar_event_id');
        $event = AcademicCalendarEvent::with('academicYear')->find($eventId);

        if (!$event || ($schoolId && $event->school_id !== $schoolId)) {
            return response()->json(['status' => 'error', 'message' => 'Event tidak ditemukan atau tidak memiliki akses.'], 404);
        }

        $query = ExamSession::with(['sessionSubjects.subject'])
            ->where('academic_calendar_event_id', $eventId);

        if ($request->filled('grade')) {
            $grade = (int) $request->input('grade');
            $query->whereHas('sessionSubjects', function ($q) use ($grade) {
                $q->where('grade', $grade);
            });
        }

        $sessions = $query->orderBy('exam_date')
            ->orderBy('session_number')
            ->get();

        // Also fetch available grades for this school
        $grades = Classroom::where('school_id', $event->school_id)
            ->distinct()
            ->pluck('grade')
            ->sort()
            ->values();

        // Available subjects for this school
        $subjects = Subject::where('school_id', $event->school_id)
            ->where('is_active', true)
            ->get(['id', 'code', 'name']);

        $formattedSessions = $sessions->map(function ($s) {
            return [
                'id'                         => $s->id,
                'school_id'                  => $s->school_id,
                'academic_calendar_event_id' => $s->academic_calendar_event_id,
                'exam_date'                  => $s->exam_date ? $s->exam_date->format('Y-m-d') : null,
                'session_number'             => $s->session_number,
                'start_time'                 => substr($s->start_time, 0, 5),
                'end_time'                   => substr($s->end_time, 0, 5),
                'notes'                      => $s->notes,
                'session_subjects'           => $s->sessionSubjects->map(function ($ss) {
                    return [
                        'id'              => $ss->id,
                        'exam_session_id' => $ss->exam_session_id,
                        'subject_id'      => $ss->subject_id,
                        'grade'           => $ss->grade,
                        'subject'         => $ss->subject ? [
                            'id'   => $ss->subject->id,
                            'code' => $ss->subject->code,
                            'name' => $ss->subject->name,
                        ] : null,
                    ];
                }),
            ];
        });

        return response()->json([
            'status'   => 'success',
            'event'    => [
                'id'          => $event->id,
                'title'       => $event->title,
                'start_date'  => $event->start_date->format('Y-m-d'),
                'end_date'    => $event->end_date->format('Y-m-d'),
                'description' => $event->description,
            ],
            'grades'   => $grades,
            'subjects' => $subjects,
            'sessions' => $formattedSessions,
        ]);
    }

    /**
     * Bulk store/update exam sessions for an event.
     */
    public function bulkStore(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);
        if (!$schoolId || $schoolId === -1) {
            return response()->json(['status' => 'error', 'message' => 'Sekolah ID tidak valid.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'academic_calendar_event_id' => 'required|exists:academic_calendar_events,id',
            'sessions'                   => 'present|array',
            'sessions.*.exam_date'       => 'required|date',
            'sessions.*.session_number' => 'required|integer|min:1|max:10',
            'sessions.*.start_time'     => 'required|date_format:H:i',
            'sessions.*.end_time'       => 'required|date_format:H:i',
            'sessions.*.notes'          => 'nullable|string',
            'sessions.*.subjects'       => 'nullable|array',
            'sessions.*.subjects.*.subject_id' => 'required|exists:subjects,id',
            'sessions.*.subjects.*.grade'      => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $eventId = $request->input('academic_calendar_event_id');
        $event = AcademicCalendarEvent::find($eventId);

        if (!$event || $event->school_id !== $schoolId) {
            return response()->json(['status' => 'error', 'message' => 'Event kalender akademik tidak ditemukan.'], 404);
        }

        $user = $request->user();

        DB::beginTransaction();
        try {
            // Delete existing sessions for this event to rebuild matriks cleanly
            $existingSessionIds = ExamSession::where('academic_calendar_event_id', $eventId)->pluck('id');
            ExamSessionSubject::whereIn('exam_session_id', $existingSessionIds)->delete();
            ExamSession::whereIn('id', $existingSessionIds)->delete();

            $createdSessions = [];

            foreach ($request->input('sessions') as $sessionData) {
                $session = ExamSession::create([
                    'school_id'                  => $schoolId,
                    'academic_calendar_event_id' => $eventId,
                    'exam_date'                  => $sessionData['exam_date'],
                    'session_number'             => $sessionData['session_number'],
                    'start_time'                 => $sessionData['start_time'],
                    'end_time'                   => $sessionData['end_time'],
                    'notes'                      => $sessionData['notes'] ?? null,
                    'created_by'                 => $user->id,
                ]);

                if (!empty($sessionData['subjects'])) {
                    foreach ($sessionData['subjects'] as $subjData) {
                        ExamSessionSubject::create([
                            'exam_session_id' => $session->id,
                            'subject_id'      => $subjData['subject_id'],
                            'grade'           => $subjData['grade'],
                        ]);
                    }
                }

                $createdSessions[] = $session->id;
            }

            DB::commit();

            $updatedSessions = ExamSession::with(['sessionSubjects.subject'])
                ->whereIn('id', $createdSessions)
                ->orderBy('exam_date')
                ->orderBy('session_number')
                ->get();

            $formattedSessions = $updatedSessions->map(function ($s) {
                return [
                    'id'                         => $s->id,
                    'school_id'                  => $s->school_id,
                    'academic_calendar_event_id' => $s->academic_calendar_event_id,
                    'exam_date'                  => $s->exam_date ? $s->exam_date->format('Y-m-d') : null,
                    'session_number'             => $s->session_number,
                    'start_time'                 => substr($s->start_time, 0, 5),
                    'end_time'                   => substr($s->end_time, 0, 5),
                    'notes'                      => $s->notes,
                    'session_subjects'           => $s->sessionSubjects->map(function ($ss) {
                        return [
                            'id'              => $ss->id,
                            'exam_session_id' => $ss->exam_session_id,
                            'subject_id'      => $ss->subject_id,
                            'grade'           => $ss->grade,
                            'subject'         => $ss->subject ? [
                                'id'   => $ss->subject->id,
                                'code' => $ss->subject->code,
                                'name' => $ss->subject->name,
                            ] : null,
                        ];
                    }),
                ];
            });

            return response()->json([
                'status'   => 'success',
                'message'  => 'Jadwal ujian berhasil diperbarui.',
                'sessions' => $formattedSessions,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan jadwal ujian: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete single session.
     */
    public function destroySession(Request $request, int $id): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);
        $session = ExamSession::find($id);

        if (!$session || ($schoolId && $session->school_id !== $schoolId)) {
            return response()->json(['status' => 'error', 'message' => 'Sesi ujian tidak ditemukan.'], 404);
        }

        $session->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Sesi ujian berhasil dihapus.',
        ]);
    }

    /**
     * Get student / teacher exam schedule.
     */
    public function mySchedule(Request $request): JsonResponse
    {
        $user = $request->user();
        $schoolId = $this->resolveSchoolId($request);

        $grade = null;

        // If user is student, determine their grade from active classroom
        if ($user->hasRole('siswa') && $user->studentProfile) {
            $classroom = $user->studentProfile->classroom;
            if ($classroom) {
                $grade = $classroom->grade;
            }
        } elseif ($user->hasRole('orang_tua') && $user->parentProfile) {
            // Determine grade from parent's student child
            $child = $user->parentProfile->students()->with('classroom')->first();
            if ($child && $child->classroom) {
                $grade = $child->classroom->grade;
            }
        } elseif ($request->filled('grade')) {
            $grade = (int) $request->input('grade');
        }

        $query = ExamSession::with(['calendarEvent', 'sessionSubjects.subject']);

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        if ($grade !== null) {
            $query->whereHas('sessionSubjects', function ($q) use ($grade) {
                $q->where('grade', $grade);
            });
        }

        $sessions = $query->orderBy('exam_date')
            ->orderBy('session_number')
            ->get();

        // Format data for easy display on UI
        $formatted = $sessions->map(function ($session) use ($grade) {
            $subjects = $session->sessionSubjects;
            if ($grade !== null) {
                $subjects = $subjects->where('grade', $grade);
            }

            return [
                'id'             => $session->id,
                'event_title'    => $session->calendarEvent?->title ?? 'Ujian',
                'exam_date'      => $session->exam_date->format('Y-m-d'),
                'session_number' => $session->session_number,
                'start_time'     => substr($session->start_time, 0, 5),
                'end_time'       => substr($session->end_time, 0, 5),
                'notes'          => $session->notes,
                'subjects'       => $subjects->map(function ($ss) {
                    return [
                        'subject_id'   => $ss->subject_id,
                        'subject_name' => $ss->subject?->name,
                        'subject_code' => $ss->subject?->code,
                        'grade'        => $ss->grade,
                    ];
                })->values(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => $formatted,
        ]);
    }
}
