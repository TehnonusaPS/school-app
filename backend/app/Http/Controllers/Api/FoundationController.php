<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Foundation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FoundationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            $query = Foundation::query();

            // Search by name or code
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            $foundations = $query->latest()->paginate(15);

            return response()->json([
                'status' => 'success',
                'data'   => $foundations,
            ]);
        }

        if ($user->hasRole('admin_yayasan')) {
            $foundation = Foundation::find($user->foundation_id);
            if (!$foundation) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Foundation not found for your account.',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'current_page' => 1,
                    'data' => [$foundation],
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

        if (!$user->isSuperAdmin()) {
            return response()->json([
                'message' => 'Forbidden: You do not have the required access role.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|max:255',
            'code'             => 'required|string|unique:foundations,code|max:255',
            'established_date' => 'nullable|date',
            'status'           => 'nullable|in:active,inactive,trial',
            'address'          => 'nullable|string',
            'email'            => 'nullable|email|max:255',
            'phone'            => 'nullable|string|max:50',
            'website'          => 'nullable|string|max:255',
            'deed_number'      => 'nullable|string|max:255',
            'deed_date'        => 'nullable|date',
            'decree_number'    => 'nullable|string|max:255',
            'decree_date'      => 'nullable|date',
            'logo'             => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $request->all();
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo'] = asset('storage/' . $path);
        } else {
            unset($data['logo']);
        }

        $foundation = Foundation::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Foundation created successfully.',
            'data'    => $foundation,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $foundation = Foundation::find($id);

        if (!$foundation) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Foundation not found.',
            ], 404);
        }

        if ($user->isSuperAdmin()) {
            return response()->json([
                'status' => 'success',
                'data'   => $foundation,
            ]);
        }

        if ($user->hasRole('admin_yayasan') && $user->foundation_id == $foundation->id) {
            return response()->json([
                'status' => 'success',
                'data'   => $foundation,
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
        $foundation = Foundation::find($id);

        if (!$foundation) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Foundation not found.',
            ], 404);
        }

        $isSuperAdmin = $user->isSuperAdmin();
        $isAdminYayasan = $user->hasRole('admin_yayasan') && $user->foundation_id == $foundation->id;

        if (!$isSuperAdmin && !$isAdminYayasan) {
            return response()->json([
                'message' => 'Forbidden: You do not have the required access role.',
            ], 403);
        }

        $rules = [
            'name'             => 'required|string|max:255',
            'established_date' => 'nullable|date',
            'address'          => 'nullable|string',
            'email'            => 'nullable|email|max:255',
            'phone'            => 'nullable|string|max:50',
            'website'          => 'nullable|string|max:255',
            'deed_number'      => 'nullable|string|max:255',
            'deed_date'        => 'nullable|date',
            'decree_number'    => 'nullable|string|max:255',
            'decree_date'      => 'nullable|date',
            'logo'             => 'nullable|image|max:2048',
        ];

        // Only superadmin can change code and status
        if ($isSuperAdmin) {
            $rules['code']   = 'required|string|max:255|unique:foundations,code,' . $foundation->id;
            $rules['status'] = 'required|in:active,inactive,trial';
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

        // Strip code and status if admin_yayasan
        if (!$isSuperAdmin) {
            unset($data['code']);
            unset($data['status']);
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo'] = asset('storage/' . $path);
        } else {
            unset($data['logo']);
        }

        $foundation->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Foundation updated successfully.',
            'data'    => $foundation,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = $request->user();

        if (!$user->isSuperAdmin()) {
            return response()->json([
                'message' => 'Forbidden: You do not have the required access role.',
            ], 403);
        }

        $foundation = Foundation::find($id);

        if (!$foundation) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Foundation not found.',
            ], 404);
        }

        $foundation->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Foundation deleted successfully.',
        ]);
    }
}
