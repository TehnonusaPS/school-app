<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Foundation;
use App\Models\User;
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
            $query = Foundation::withCount('schools');

            // Search by name or code
            if ($request->filled('search')) {
                $search = $request->input('search');
                $likeOperator = \DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
                $query->where(function ($q) use ($search, $likeOperator) {
                    $q->where('name', $likeOperator, "%{$search}%")
                      ->orWhere('code', $likeOperator, "%{$search}%");
                });
            }

            // Filter by status
            if ($request->filled('status') && $request->input('status') !== 'all') {
                $query->where('status', $request->input('status'));
            }

            $foundations = $query->latest()->paginate($request->input('per_page', 15));

            $foundations->getCollection()->transform(function ($f) {
                $schoolIds = $f->schools()->pluck('id');
                $f->users_count = \App\Models\User::where('foundation_id', $f->id)
                    ->orWhereIn('school_id', $schoolIds)
                    ->count();
                return $f;
            });

            $total = Foundation::count();
            $active = Foundation::where('status', 'active')->count();
            $trial = Foundation::where('status', 'trial')->count();
            $inactive = Foundation::where('status', 'inactive')->count();

            return response()->json([
                'status' => 'success',
                'data'   => $foundations,
                'stats'  => [
                    'total' => $total,
                    'active' => $active,
                    'trial' => $trial,
                    'inactive' => $inactive,
                ]
            ]);
        }

        if ($user->hasRole('admin_yayasan')) {
            $foundation = Foundation::withCount('schools')->find($user->foundation_id);
            if (!$foundation) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Foundation not found for your account.',
                ], 404);
            }

            $schoolIds = $foundation->schools()->pluck('id');
            $usersCount = \App\Models\User::where('foundation_id', $foundation->id)
                ->orWhereIn('school_id', $schoolIds)
                ->count();

            $foundation->users_count = $usersCount;

            $stats = [
                'total' => 1,
                'active' => $foundation->status === 'active' ? 1 : 0,
                'trial' => $foundation->status === 'trial' ? 1 : 0,
                'inactive' => $foundation->status === 'inactive' ? 1 : 0,
            ];

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'current_page' => 1,
                    'data' => [$foundation],
                    'total' => 1,
                ],
                'stats' => $stats,
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
            'curriculum_id'     => 'nullable|exists:curriculums,id',
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
            $data['logo'] = $path;
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

        if ($user->isSuperAdmin() || ($user->hasRole('admin_yayasan') && $user->foundation_id == $foundation->id)) {
            $foundation->load([
                'users' => function ($q) {
                    $q->whereIn('role_id', function ($sq) {
                        $sq->select('id')->from('roles')->where('name', 'admin_yayasan');
                    });
                },
                'activeSubscription.plan'
            ]);

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
            $data['logo'] = $path;
        } else {
            unset($data['logo']);
        }

        $foundation->update($data);

        // Update administrator user details if provided in request
        $adminUser = User::where('foundation_id', $foundation->id)
            ->whereIn('role_id', function ($sq) {
                $sq->select('id')->from('roles')->where('name', 'admin_yayasan');
            })->first();

        if ($adminUser) {
            $userUpdateData = [];
            if ($request->has('emailLogin') && $request->filled('emailLogin')) {
                $userUpdateData['email'] = $request->input('emailLogin');
            }
            if ($request->has('noHpLogin') && $request->filled('noHpLogin')) {
                $userUpdateData['phone'] = $request->input('noHpLogin');
            }
            if ($request->has('name') && $request->filled('name')) {
                $userUpdateData['name'] = 'Admin ' . $request->input('name');
            }

            if (!empty($userUpdateData)) {
                // Validate email and phone if they are being updated
                $userRules = [];
                if (isset($userUpdateData['email'])) {
                    $userRules['email'] = 'required|email|unique:users,email,' . $adminUser->id;
                }
                if (isset($userUpdateData['phone'])) {
                    $userRules['phone'] = 'required|string|max:50|unique:users,phone,' . $adminUser->id;
                }

                if (!empty($userRules)) {
                    $userValidator = Validator::make($userUpdateData, $userRules);
                    if ($userValidator->fails()) {
                        return response()->json([
                            'status'  => 'error',
                            'message' => 'Validation error on administrator account.',
                            'errors'  => $userValidator->errors(),
                        ], 422);
                    }
                }

                $adminUser->update($userUpdateData);
            }
        }

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
