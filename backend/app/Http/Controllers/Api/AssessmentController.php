<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssessmentRequest;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\TeacherSubjectAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    /**
     * Display a listing of assessments.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Assessment::with(['classroom:id,name', 'subject:id,name,code', 'academicYear:id,name,semester'])
            ->where('school_id', $user->school_id);

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->has('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }
        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->latest()->get(),
        ]);
    }

    /**
     * Store a newly created assessment in storage.
     */
    public function store(StoreAssessmentRequest $request): JsonResponse
    {
        $user = $request->user();

        // 1. Verify that teacher is assigned to this subject and classroom
        $assigned = TeacherSubjectAssignment::where('teacher_id', $user->id)
            ->where('subject_id', $request->subject_id)
            ->where('classroom_id', $request->classroom_id)
            ->where('is_active', true)
            ->exists();

        if (!$assigned) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki hak akses untuk mengajar mata pelajaran ini di kelas yang dipilih.',
            ], 403);
        }

        $label = $request->category === 'tugas' ? 'Tugas' : 'Ujian';

        try {
            DB::beginTransaction();

            // 2. Create Assessment metadata
            $assessment = Assessment::create([
                'school_id'        => $user->school_id,
                'subject_id'       => $request->subject_id,
                'classroom_id'     => $request->classroom_id,
                'academic_year_id' => $request->academic_year_id,
                'created_by'       => $user->id,
                'category'         => $request->category,
                'type'             => $request->type,
                'title'            => $request->title,
                'uploaded_by_name' => $user->name,
                'is_active'        => true,
            ]);

            // 3. Save student scores
            $scoresData = [];
            foreach ($request->scores as $scoreItem) {
                $scoresData[] = [
                    'assessment_id' => $assessment->id,
                    'student_id'    => $scoreItem['student_id'],
                    'score'         => $scoreItem['score'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }

            AssessmentScore::insert($scoresData);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "{$label} baru berhasil disimpan.",
                'data' => $assessment->load('scores'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => "Gagal menyimpan {$label}: " . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified assessment.
     */
    public function show(string $id): JsonResponse
    {
        $assessment = Assessment::with([
            'classroom',
            'subject',
            'academicYear',
            'scores.student.user:id,name'
        ])->find($id);

        if (!$assessment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Penilaian tidak ditemukan.',
            ], 404);
        }

        // Remap scores to make it easy for frontend
        $scoresMapped = $assessment->scores->map(function ($s) {
            return [
                'student_id' => $s->student_id,
                'name'       => $s->student && $s->student->user ? $s->student->user->name : 'No Name',
                'score'      => $s->score,
            ];
        });

        $data = $assessment->toArray();
        $data['scores_list'] = $scoresMapped;

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * Update the specified assessment in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $assessment = Assessment::find($id);

        if (!$assessment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Penilaian tidak ditemukan.',
            ], 404);
        }

        // Verify school access
        if ($assessment->school_id !== $user->school_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak.',
            ], 403);
        }

        $request->validate([
            'title'        => 'required|string|max:255',
            'type'         => 'required|in:tugas_sekolah,tugas_rumah,ujian_harian,uts,uas',
            'is_active'    => 'nullable|boolean',
            'scores'       => 'required|array|min:1',
            'scores.*.student_id' => 'required|exists:student_profiles,id',
            'scores.*.score'      => 'required|numeric|min:0|max:100',
        ]);

        $label = $assessment->category === 'tugas' ? 'Tugas' : 'Ujian';

        try {
            DB::beginTransaction();

            // 1. Update Assessment metadata
            $updateData = [
                'title' => $request->title,
                'type'  => $request->type,
            ];
            if ($request->has('is_active')) {
                $updateData['is_active'] = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);
            }
            $assessment->update($updateData);

            // 2. Upsert scores
            foreach ($request->scores as $scoreItem) {
                AssessmentScore::updateOrCreate(
                    [
                        'assessment_id' => $assessment->id,
                        'student_id'    => $scoreItem['student_id']
                    ],
                    [
                        'score' => $scoreItem['score']
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "{$label} berhasil diupdate.",
                'data' => $assessment->load('scores'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => "Gagal mengupdate {$label}: " . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified assessment from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $assessment = Assessment::find($id);

        if (!$assessment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Penilaian tidak ditemukan.',
            ], 404);
        }

        $label = $assessment->category === 'tugas' ? 'Tugas' : 'Ujian';

        // Delete assessment (scores will be cascade deleted due to constrained foreign key)
        $assessment->delete();

        return response()->json([
            'status' => 'success',
            'message' => "{$label} berhasil dihapus.",
        ]);
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $assessment = Assessment::find($id);

        if (!$assessment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Penilaian tidak ditemukan.',
            ], 404);
        }

        $label = $assessment->category === 'tugas' ? 'Tugas' : 'Ujian';

        $assessment->is_active = !$assessment->is_active;
        $assessment->save();

        return response()->json([
            'status' => 'success',
            'message' => "Status {$label} berhasil diubah.",
            'data' => [
                'is_active' => $assessment->is_active,
            ],
        ]);
    }
}
