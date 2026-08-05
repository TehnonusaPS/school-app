<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Curriculum;
use App\Models\CurriculumSubject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CurriculumController extends Controller
{
    /**
     * Display a listing of curriculums.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Curriculum::withCount('curriculumSubjects');

        if ($request->has('level') && !empty($request->level)) {
            $query->where(function ($q) use ($request) {
                $q->where('level', $request->level)
                  ->orWhere('level', 'ALL');
            });
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $curriculums = $query->orderBy('id')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $curriculums,
        ]);
    }

    /**
     * Display the specified curriculum details.
     */
    public function show(string $id): JsonResponse
    {
        $curriculum = Curriculum::with('curriculumSubjects')->find($id);

        if (!$curriculum) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kurikulum tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $curriculum,
        ]);
    }

    /**
     * Store a newly created curriculum.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code'        => 'required|string|max:50|unique:curriculums,code',
            'name'        => 'required|string|max:255',
            'level'       => 'required|in:SD,SMP,ALL',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
            'subjects'    => 'nullable|array',
            'subjects.*.code' => 'required|string',
            'subjects.*.name' => 'required|string',
            'subjects.*.level' => 'required|in:SD,SMP',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $curriculum = Curriculum::create([
            'code'        => $request->code,
            'name'        => $request->name,
            'level'       => $request->level,
            'description' => $request->description,
            'is_active'   => $request->input('is_active', true),
        ]);

        if ($request->has('subjects') && is_array($request->subjects)) {
            foreach ($request->subjects as $idx => $subj) {
                CurriculumSubject::create([
                    'curriculum_id' => $curriculum->id,
                    'code'          => $subj['code'],
                    'name'          => $subj['name'],
                    'level'         => $subj['level'],
                    'phase'         => $subj['phase'] ?? null,
                    'is_mandatory'  => $subj['is_mandatory'] ?? true,
                    'sort_order'    => $idx + 1,
                ]);
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Jenis kurikulum berhasil ditambahkan.',
            'data'    => $curriculum->load('curriculumSubjects'),
        ], 201);
    }

    /**
     * Update specified curriculum.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $curriculum = Curriculum::find($id);

        if (!$curriculum) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kurikulum tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'code'        => 'sometimes|required|string|max:50|unique:curriculums,code,' . $id,
            'name'        => 'sometimes|required|string|max:255',
            'level'       => 'sometimes|required|in:SD,SMP,ALL',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $curriculum->update($request->only(['code', 'name', 'level', 'description', 'is_active']));

        return response()->json([
            'status'  => 'success',
            'message' => 'Jenis kurikulum berhasil diperbarui.',
            'data'    => $curriculum->load('curriculumSubjects'),
        ]);
    }

    /**
     * Delete curriculum.
     */
    public function destroy(string $id): JsonResponse
    {
        $curriculum = Curriculum::find($id);

        if (!$curriculum) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kurikulum tidak ditemukan.',
            ], 404);
        }

        $curriculum->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Kurikulum berhasil dihapus.',
        ]);
    }
}
