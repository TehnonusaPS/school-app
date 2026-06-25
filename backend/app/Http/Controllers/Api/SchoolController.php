<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            $query = School::with('foundation:id,name,code');

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('npsn', 'like', "%{$search}%");
                });
            }

            if ($request->has('foundation_id')) {
                $query->where('foundation_id', $request->input('foundation_id'));
            }

            if ($request->has('level')) {
                $query->where('level', $request->input('level'));
            }

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            return response()->json([
                'status' => 'success',
                'data'   => $query->latest()->paginate(15),
            ]);
        }

        if ($user->hasRole('admin_yayasan')) {
            $query = School::with('foundation:id,name,code')
                ->where('foundation_id', $user->foundation_id);

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('npsn', 'like', "%{$search}%");
                });
            }

            if ($request->has('level')) {
                $query->where('level', $request->input('level'));
            }

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            return response()->json([
                'status' => 'success',
                'data'   => $query->latest()->paginate(15),
            ]);
        }

        if ($user->hasRole('admin_sekolah')) {
            $school = School::with('foundation:id,name,code')->find($user->school_id);
            if (!$school) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'School not found for your account.',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'current_page' => 1,
                    'data' => [$school],
                    'total' => 1,
                ],
            ]);
        }

        return response()->json([
            'message' => 'Forbidden: You do not have the required access role.',
        ], 403);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $isSuperAdmin = $user->isSuperAdmin();
        $isAdminYayasan = $user->hasRole('admin_yayasan');

        if (!$isSuperAdmin && !$isAdminYayasan) {
            return response()->json([
                'message' => 'Forbidden: You do not have the required access role.',
            ], 403);
        }

        $rules = [
            'name'                 => 'required|string|max:255',
            'npsn'                 => 'nullable|string|unique:schools,npsn|max:50',
            'level'                => 'nullable|string|max:50',
            'established_date'     => 'nullable|date',
            'status'               => 'nullable|in:active,inactive,trial',
            'address'              => 'nullable|string',
            'email'                => 'nullable|email|max:255',
            'phone'                => 'nullable|string|max:50',
            'website'              => 'nullable|string|max:255',
            'instagram'            => 'nullable|string|max:255',
            'facebook'             => 'nullable|string|max:255',
            'decree_number'        => 'nullable|string|max:255',
            'decree_date'          => 'nullable|date',
            'permit_number'        => 'nullable|string|max:255',
            'permit_date'          => 'nullable|date',
            'accreditation'        => 'nullable|string|max:10',
            'accreditation_date'   => 'nullable|date',
            'accreditation_number' => 'nullable|string|max:255',
            'logo'                 => 'nullable|image|max:2048',
        ];

        if ($isSuperAdmin) {
            $rules['foundation_id'] = 'required|exists:foundations,id';
        } else {
            // Admin Yayasan cannot choose foundation, it is forced to their own
            $rules['foundation_id'] = 'nullable|in:' . $user->foundation_id;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $request->all();
        if (!$isSuperAdmin) {
            $data['foundation_id'] = $user->foundation_id;
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo'] = asset('storage/' . $path);
        } else {
            unset($data['logo']);
        }

        $school = School::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'School created successfully.',
            'data'    => $school,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $school = School::with('foundation:id,name,code')->find($id);

        if (!$school) {
            return response()->json([
                'status'  => 'error',
                'message' => 'School not found.',
            ], 404);
        }

        if ($user->isSuperAdmin()) {
            return response()->json([
                'status' => 'success',
                'data'   => $school,
            ]);
        }

        if ($user->hasRole('admin_yayasan') && $user->foundation_id == $school->foundation_id) {
            return response()->json([
                'status' => 'success',
                'data'   => $school,
            ]);
        }

        if ($user->hasRole('admin_sekolah') && $user->school_id == $school->id) {
            return response()->json([
                'status' => 'success',
                'data'   => $school,
            ]);
        }

        return response()->json([
            'message' => 'Forbidden: You do not have the required access role.',
        ], 403);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $school = School::find($id);

        if (!$school) {
            return response()->json([
                'status'  => 'error',
                'message' => 'School not found.',
            ], 404);
        }

        $isSuperAdmin = $user->isSuperAdmin();
        $isAdminYayasan = $user->hasRole('admin_yayasan') && $user->foundation_id == $school->foundation_id;
        $isAdminSekolah = $user->hasRole('admin_sekolah') && $user->school_id == $school->id;

        if (!$isSuperAdmin && !$isAdminYayasan && !$isAdminSekolah) {
            return response()->json([
                'message' => 'Forbidden: You do not have the required access role.',
            ], 403);
        }

        $rules = [
            'name'                 => 'required|string|max:255',
            'npsn'                 => 'nullable|string|max:50|unique:schools,npsn,' . $school->id,
            'level'                => 'nullable|string|max:50',
            'established_date'     => 'nullable|date',
            'address'              => 'nullable|string',
            'email'                => 'nullable|email|max:255',
            'phone'                => 'nullable|string|max:50',
            'website'              => 'nullable|string|max:255',
            'instagram'            => 'nullable|string|max:255',
            'facebook'             => 'nullable|string|max:255',
            'decree_number'        => 'nullable|string|max:255',
            'decree_date'          => 'nullable|date',
            'permit_number'        => 'nullable|string|max:255',
            'permit_date'          => 'nullable|date',
            'accreditation'        => 'nullable|string|max:10',
            'accreditation_date'   => 'nullable|date',
            'accreditation_number' => 'nullable|string|max:255',
            'logo'                 => 'nullable|image|max:2048',
        ];

        // Specific fields only editable by superadmin or admin yayasan
        if ($isSuperAdmin) {
            $rules['foundation_id'] = 'sometimes|required|exists:foundations,id';
            $rules['status']        = 'sometimes|required|in:active,inactive,trial';
        } elseif ($isAdminYayasan) {
            $rules['status']        = 'sometimes|required|in:active,inactive,trial';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $request->all();

        // Strip restricted fields based on role
        if ($isAdminYayasan) {
            unset($data['foundation_id']); // Cannot transfer school to another foundation
        } elseif ($isAdminSekolah) {
            unset($data['foundation_id']);
            unset($data['status']);
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo'] = asset('storage/' . $path);
        } else {
            unset($data['logo']);
        }

        $school->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'School updated successfully.',
            'data'    => $school,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $school = School::find($id);

        if (!$school) {
            return response()->json([
                'status'  => 'error',
                'message' => 'School not found.',
            ], 404);
        }

        $isSuperAdmin = $user->isSuperAdmin();
        $isAdminYayasan = $user->hasRole('admin_yayasan') && $user->foundation_id == $school->foundation_id;

        if (!$isSuperAdmin && !$isAdminYayasan) {
            return response()->json([
                'message' => 'Forbidden: You do not have the required access role.',
            ], 403);
        }

        $school->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'School deleted successfully.',
        ]);
    }
}
