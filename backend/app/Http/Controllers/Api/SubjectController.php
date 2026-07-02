<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\School;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    /**
     * Helper to get school scope for the current user.
     */
    private function getSchoolScope(Request $request, $user)
    {
        if ($user->isSuperAdmin()) {
            return $request->input('school_id');
        } elseif ($user->hasRole('admin_yayasan')) {
            $schoolId = $request->input('school_id');
            if ($schoolId) {
                $belongs = School::where('id', $schoolId)
                    ->where('foundation_id', $user->foundation_id)
                    ->exists();
                return $belongs ? $schoolId : -1;
            }
            return null;
        }
        return $user->school_id;
    }

    /**
     * Display a listing of subjects.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Subject::query();

        if ($user->isSuperAdmin()) {
            if ($request->has('school_id')) {
                $query->where('school_id', $request->input('school_id'));
            }
        } elseif ($user->hasRole('admin_yayasan')) {
            $query->whereHas('school', function ($q) use ($user) {
                $q->where('foundation_id', $user->foundation_id);
            });
            if ($request->has('school_id')) {
                $query->where('school_id', $request->input('school_id'));
            }
        } else {
            $query->where('school_id', $user->school_id);
        }

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $subjects = $query->latest()->get();

        return response()->json([
            'status' => 'success',
            'data'   => $subjects,
        ]);
    }

    /**
     * Store a newly created subject.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $schoolId = $this->getSchoolScope($request, $user);

        if ($schoolId === -1) {
            return response()->json([
                'status'  => 'error',
                'message' => 'The selected school does not belong to your foundation.',
            ], 403);
        }

        if (!$schoolId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'School ID is required.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'code'        => 'nullable|string|max:50',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $subject = Subject::create([
            'school_id'   => $schoolId,
            'code'        => $request->input('code'),
            'name'        => $request->input('name'),
            'description' => $request->input('description'),
            'is_active'   => $request->input('is_active', true),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Subject created successfully.',
            'data'    => $subject,
        ], 201);
    }

    /**
     * Display the specified subject.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Subject not found.',
            ], 404);
        }

        // Scope check
        if (!$user->isSuperAdmin()) {
            if ($user->hasRole('admin_yayasan')) {
                if ($subject->school->foundation_id != $user->foundation_id) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
                }
            } else {
                if ($subject->school_id != $user->school_id) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data'   => $subject,
        ]);
    }

    /**
     * Update the specified subject.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Subject not found.',
            ], 404);
        }

        // Scope check
        if (!$user->isSuperAdmin()) {
            if ($user->hasRole('admin_yayasan')) {
                if ($subject->school->foundation_id != $user->foundation_id) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
                }
            } else {
                if ($subject->school_id != $user->school_id) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
                }
            }
        }

        $validator = Validator::make($request->all(), [
            'code'        => 'nullable|string|max:50',
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $subject->update($request->only(['code', 'name', 'description', 'is_active']));

        return response()->json([
            'status'  => 'success',
            'message' => 'Subject updated successfully.',
            'data'    => $subject,
        ]);
    }

    /**
     * Remove the specified subject.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Subject not found.',
            ], 404);
        }

        // Scope check
        if (!$user->isSuperAdmin()) {
            if ($user->hasRole('admin_yayasan')) {
                if ($subject->school->foundation_id != $user->foundation_id) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
                }
            } else {
                if ($subject->school_id != $user->school_id) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
                }
            }
        }

        $subject->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Subject deleted successfully.',
        ]);
    }
}
