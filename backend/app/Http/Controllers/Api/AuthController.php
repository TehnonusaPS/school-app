<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handle user login.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // 1. Validate request parameters
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors()
            ], 422);
        }

        // 2. Normalize and retrieve user with role relationship
        $email = strtolower(trim($request->email));
        $user = User::with('role')->where('email', $email)->first();

        // 3. Verify credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah.'
            ], 401);
        }

        // 4. Verify account status
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Akun Anda dinonaktifkan.'
            ], 403);
        }

        // 5. Generate Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        // 6. Map response to match frontend expectations
        $mappedUser = [
            'id'            => $user->id,
            'name'          => $user->name,
            'email'         => $user->email,
            'role'          => $user->role ? $user->role->name : null,
            'roleLabel'     => $user->role ? $user->role->label : null,
            'phone'         => $user->phone,
            'photo'         => $user->photo,
            'is_active'     => $user->is_active,
            'foundation_id' => $user->foundation_id,
            'school_id'     => $user->school_id,
        ];

        return response()->json([
            'message'      => 'Login berhasil.',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $mappedUser
        ]);
    }

    /**
     * Get authenticated user profile.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user()->load('role');

        $mappedUser = [
            'id'            => $user->id,
            'name'          => $user->name,
            'email'         => $user->email,
            'role'          => $user->role ? $user->role->name : null,
            'roleLabel'     => $user->role ? $user->role->label : null,
            'phone'         => $user->phone,
            'photo'         => $user->photo,
            'is_active'     => $user->is_active,
            'foundation_id' => $user->foundation_id,
            'school_id'     => $user->school_id,
        ];

        return response()->json([
            'user' => $mappedUser
        ]);
    }

    /**
     * Handle user logout.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke current user access token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Berhasil logout.'
        ]);
    }
}
