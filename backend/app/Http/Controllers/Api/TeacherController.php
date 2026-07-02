<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\TeacherProfile;
use App\Models\School;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    /**
     * Map gender string.
     */
    private function mapGenderToDb(?string $gender): ?string
    {
        if (!$gender) return null;
        return in_array(strtolower($gender), ['laki-laki', 'male', 'jk01']) ? 'male' : 'female';
    }

    private function mapGenderToFe(?string $gender): string
    {
        if ($gender === 'male') return 'Laki-laki';
        if ($gender === 'female') return 'Perempuan';
        return '';
    }

    /**
     * Helper to get school and foundation scope.
     */
    private function resolveTenantId(Request $request, $user)
    {
        $schoolId = null;
        $foundationId = null;

        // Frontend maps "unit_kerja" or "school_id"
        // E.g. "Y0001" or "S0001" or raw integer
        $unitVal = $request->input('unit_kerja') ?: $request->input('school_id');

        if ($unitVal) {
            if (is_string($unitVal) && str_starts_with(strtoupper($unitVal), 'S')) {
                $schoolId = (int) substr($unitVal, 1);
            } elseif (is_string($unitVal) && str_starts_with(strtoupper($unitVal), 'Y')) {
                $foundationId = (int) substr($unitVal, 1);
            } else {
                $schoolId = (int) $unitVal;
            }
        }

        if ($schoolId) {
            $school = School::find($schoolId);
            if ($school) {
                $foundationId = $school->foundation_id;
            }
        }

        // Scope verification
        if (!$user->isSuperAdmin()) {
            if ($user->hasRole('admin_yayasan')) {
                if ($schoolId) {
                    $school = School::find($schoolId);
                    if (!$school || $school->foundation_id != $user->foundation_id) {
                        return ['error' => 'Selected school does not belong to your foundation.'];
                    }
                } else {
                    $foundationId = $user->foundation_id;
                }
            } else { // admin_sekolah etc
                $schoolId = $user->school_id;
                $school = School::find($schoolId);
                if ($school) {
                    $foundationId = $school->foundation_id;
                }
            }
        }

        return [
            'school_id'     => $schoolId,
            'foundation_id' => $foundationId,
        ];
    }

    /**
     * Map position/jabatan to Role.
     */
    private function resolveRoleFromPosition(?string $position): string
    {
        if (!$position) return 'guru';
        
        switch (strtolower($position)) {
            case 'kepala yayasan':
            case 'staff yayasan':
            case 'j001':
            case 'j002':
                return 'admin_yayasan';
            case 'kepala sekolah':
            case 'j003':
                return 'kepala_sekolah';
            case 'staff sekolah':
            case 'j005':
                return 'tata_usaha';
            case 'guru':
            case 'j004':
            default:
                return 'guru';
        }
    }

    /**
     * Map jabatan value to frontend format.
     */
    private function mapJabatanToFe(?string $position): string
    {
        if (!$position) return '';
        switch (strtoupper($position)) {
            case 'J001': return 'Kepala Yayasan';
            case 'J002': return 'Staff Yayasan';
            case 'J003': return 'Kepala Sekolah';
            case 'J004': return 'Guru';
            case 'J005': return 'Staff Sekolah';
            default: return $position;
        }
    }

    /**
     * Map status kepegawaian value to frontend format.
     */
    private function mapStatusKepegawaianToFe(?string $status): string
    {
        if (!$status) return '';
        switch (strtoupper($status)) {
            case 'SK01': return 'Tetap';
            case 'SK02': return 'Kontrak';
            case 'SK03': return 'Honorer';
            default: return $status;
        }
    }

    /**
     * Display a listing of teachers & staff.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = TeacherProfile::with(['user.school', 'user.role']);

        if ($user->isSuperAdmin()) {
            if ($request->has('school_id')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('school_id', $request->input('school_id'));
                });
            }
        } elseif ($user->hasRole('admin_yayasan')) {
            $query->whereHas('user.school', function ($q) use ($user) {
                $q->where('foundation_id', $user->foundation_id);
            })->orWhereHas('user', function ($q) use ($user) {
                $q->where('foundation_id', $user->foundation_id)->whereNull('school_id');
            });

            if ($request->has('school_id')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('school_id', $request->input('school_id'));
                });
            }
        } else { // admin_sekolah etc.
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('school_id', $user->school_id);
            });
        }

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                })->orWhere('nip_nuptk', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            $statusVal = $request->input('status');
            // If SK01, SK02 etc are used
            $query->where(function ($q) use ($statusVal) {
                $q->where('employment_status', $statusVal)
                  ->orWhere('employment_status', $this->mapStatusKepegawaianToFe($statusVal));
            });
        }

        $teachers = $query->latest()->get();

        $formatted = $teachers->map(function ($item) {
            $u = $item->user;
            if (!$u) return null;

            $masaKerja = '-';
            if ($item->join_date) {
                $diff = $item->join_date->diff(now());
                $years = $diff->y;
                $months = $diff->m;
                $masaKerja = ($years > 0 ? "$years tahun " : '') . ($months > 0 ? "$months bulan" : ($years == 0 ? "0 bulan" : ''));
                if (empty(trim($masaKerja))) {
                    $masaKerja = '0 bulan';
                }
            }

            return [
                'id'                 => $u->id,
                'nama'               => $u->name,
                'nama_depan'         => explode(' ', $u->name)[0] ?? '',
                'nama_belakang'      => implode(' ', array_slice(explode(' ', $u->name), 1)) ?: '',
                'email'              => $u->email,
                'no_hp'              => $u->phone,
                'foto'               => $u->photo ?? 'https://i.pravatar.cc/300?img=60',
                'nik'                => $item->nik,
                'nip'                => $item->nip_nuptk,
                'nip_nuptk'          => $item->nip_nuptk,
                'tempat_lahir'       => $item->birth_place,
                'tanggal_lahir'      => $item->birth_date ? $item->birth_date->format('Y-m-d') : null,
                'jenis_kelamin'      => $this->mapGenderToFe($item->gender),
                'agama'              => $item->religion,
                'status_pernikahan'  => $item->marital_status,
                'pendidikan_terakhir'=> $item->last_education,
                'gelar_depan'        => $item->front_title,
                'gelar_belakang'     => $item->back_title,
                'alamat'             => $item->address,
                'jabatan'            => $this->mapJabatanToFe($item->position),
                'status_kepegawaian' => $this->mapStatusKepegawaianToFe($item->employment_status),
                'unit_kerja'         => $u->school ? $u->school->name : ($u->foundation ? $u->foundation->name : '-'),
                'unit_id'            => $u->school_id ? 'S' . str_pad($u->school_id, 4, '0', STR_PAD_LEFT) : ($u->foundation_id ? 'Y' . str_pad($u->foundation_id, 4, '0', STR_PAD_LEFT) : null),
                'status_aktif'       => $u->is_active ? 'Aktif' : 'Nonaktif',
                'emailLogin'         => $u->email,
                'noHpLogin'          => $u->phone,
                'masaKerja'          => $masaKerja,
            ];
        })->filter()->values();

        return response()->json([
            'status' => 'success',
            'data'   => $formatted,
        ]);
    }

    /**
     * Store a newly created teacher/staff user.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $tenant = $this->resolveTenantId($request, $user);

        if (isset($tenant['error'])) {
            return response()->json([
                'status'  => 'error',
                'message' => $tenant['error'],
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama_depan'         => 'required|string|max:100',
            'nama_belakang'      => 'nullable|string|max:100',
            'emailLogin'         => 'required|email|unique:users,email',
            'noHpLogin'          => 'nullable|string',
            'password'           => 'nullable|string|min:6',
            'nik'                => 'nullable|string|max:50',
            'nip_nuptk'          => 'nullable|string|max:50',
            'tempat_lahir'       => 'nullable|string',
            'tanggal_lahir'      => 'nullable|date',
            'jenis_kelamin'      => 'nullable|string',
            'agama'              => 'nullable|string',
            'status_pernikahan'  => 'nullable|string',
            'pendidikan_terakhir'=> 'nullable|string',
            'gelar_depan'        => 'nullable|string',
            'gelar_belakang'     => 'nullable|string',
            'alamat'             => 'nullable|string',
            'jabatan'            => 'nullable|string',
            'status_kepegawaian' => 'nullable|string',
            'status_aktif'       => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $roleName = $this->resolveRoleFromPosition($request->input('jabatan'));
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Configured role ' . $roleName . ' not found.',
            ], 500);
        }

        $passwordPlain = $request->input('password') ?: '123456';

        DB::beginTransaction();
        try {
            $fullName = trim($request->input('nama_depan') . ' ' . ($request->input('nama_belakang') ?: ''));
            
            $newUser = User::create([
                'name'          => $fullName,
                'email'         => $request->input('emailLogin'),
                'password'      => Hash::make($passwordPlain),
                'role_id'       => $role->id,
                'school_id'     => $tenant['school_id'],
                'foundation_id' => $tenant['foundation_id'],
                'phone'         => $request->input('noHpLogin') ?: $request->input('no_hp'),
                'is_active'     => in_array(strtolower($request->input('status_aktif', 'aktif')), ['aktif', 'active', 'true', '1']),
            ]);

            TeacherProfile::create([
                'user_id'           => $newUser->id,
                'nik'               => $request->input('nik'),
                'nip_nuptk'         => $request->input('nip_nuptk') ?: $request->input('nip'),
                'birth_place'       => $request->input('tempat_lahir'),
                'birth_date'        => $request->input('tanggal_lahir'),
                'gender'            => $this->mapGenderToDb($request->input('jenis_kelamin')),
                'religion'          => $request->input('agama'),
                'marital_status'    => $request->input('status_pernikahan'),
                'last_education'    => $request->input('pendidikan_terakhir'),
                'front_title'       => $request->input('gelar_depan'),
                'back_title'        => $request->input('gelar_belakang'),
                'address'           => $request->input('alamat'),
                'position'          => $request->input('jabatan'),
                'employment_status' => $request->input('status_kepegawaian'),
                'join_date'         => now(),
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Guru/Staff created successfully.',
                'data'    => [
                    'id'       => $newUser->id,
                    'email'    => $newUser->email,
                    'phone'    => $newUser->phone ?: '-',
                    'password' => $passwordPlain,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Transaction failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display details of a specific teacher.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $teacher = TeacherProfile::with(['user.school', 'user.role'])->where('user_id', $id)->first();

        if (!$teacher) {
            // Check if user exists but has no profile
            $u = User::with(['school', 'role'])->find($id);
            if ($u) {
                // Return dummy/empty profile
                $teacher = new TeacherProfile(['user_id' => $u->id]);
                $teacher->setRelation('user', $u);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Teacher not found.'], 404);
            }
        }

        // Scope check
        $u = $teacher->user;
        if (!$user->isSuperAdmin()) {
            if ($user->hasRole('admin_yayasan')) {
                if ($u->foundation_id != $user->foundation_id && ($u->school && $u->school->foundation_id != $user->foundation_id)) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
                }
            } else {
                if ($u->school_id != $user->school_id) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
                }
            }
        }

        $masaKerja = '-';
        if ($teacher->join_date) {
            $diff = $teacher->join_date->diff(now());
            $years = $diff->y;
            $months = $diff->m;
            $masaKerja = ($years > 0 ? "$years tahun " : '') . ($months > 0 ? "$months bulan" : ($years == 0 ? "0 bulan" : ''));
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'                 => $u->id,
                'nama'               => $u->name,
                'nama_depan'         => explode(' ', $u->name)[0] ?? '',
                'nama_belakang'      => implode(' ', array_slice(explode(' ', $u->name), 1)) ?: '',
                'email'              => $u->email,
                'no_hp'              => $u->phone,
                'foto'               => $u->photo ?? 'https://i.pravatar.cc/300?img=60',
                'nik'                => $teacher->nik,
                'nip'                => $teacher->nip_nuptk,
                'nip_nuptk'          => $teacher->nip_nuptk,
                'tempat_lahir'       => $teacher->birth_place,
                'tanggal_lahir'      => $teacher->birth_date ? $teacher->birth_date->format('Y-m-d') : null,
                'jenis_kelamin'      => $this->mapGenderToFe($teacher->gender),
                'agama'              => $teacher->religion,
                'status_pernikahan'  => $teacher->marital_status,
                'pendidikan_terakhir'=> $teacher->last_education,
                'gelar_depan'        => $teacher->front_title,
                'gelar_belakang'     => $teacher->back_title,
                'alamat'             => $teacher->address,
                'jabatan'            => $this->mapJabatanToFe($teacher->position),
                'status_kepegawaian' => $this->mapStatusKepegawaianToFe($teacher->employment_status),
                'unit_kerja'         => $u->school ? $u->school->name : ($u->foundation ? $u->foundation->name : '-'),
                'unit_id'            => $u->school_id ? 'S' . str_pad($u->school_id, 4, '0', STR_PAD_LEFT) : ($u->foundation_id ? 'Y' . str_pad($u->foundation_id, 4, '0', STR_PAD_LEFT) : null),
                'status_aktif'       => $u->is_active ? 'Aktif' : 'Nonaktif',
                'emailLogin'         => $u->email,
                'noHpLogin'          => $u->phone,
                'masaKerja'          => $masaKerja,
            ],
        ]);
    }

    /**
     * Update a teacher profile + user.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $currentUser = $request->user();
        $teacher = TeacherProfile::with('user')->where('user_id', $id)->first();
        $newUserCreated = false;

        if (!$teacher) {
            $u = User::find($id);
            if ($u) {
                $teacher = TeacherProfile::create(['user_id' => $u->id]);
                $teacher->setRelation('user', $u);
                $newUserCreated = true;
            } else {
                return response()->json(['status' => 'error', 'message' => 'Teacher not found.'], 404);
            }
        }

        $u = $teacher->user;

        // Scope check
        if (!$currentUser->isSuperAdmin()) {
            if ($currentUser->hasRole('admin_yayasan')) {
                if ($u->foundation_id != $currentUser->foundation_id && ($u->school && $u->school->foundation_id != $currentUser->foundation_id)) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
                }
            } else {
                if ($u->school_id != $currentUser->school_id) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
                }
            }
        }

        $validator = Validator::make($request->all(), [
            'nama_depan'         => 'sometimes|required|string|max:100',
            'nama_belakang'      => 'nullable|string|max:100',
            'emailLogin'         => 'sometimes|required|email|unique:users,email,' . $u->id,
            'noHpLogin'          => 'nullable|string',
            'password'           => 'nullable|string|min:6',
            'nik'                => 'nullable|string|max:50',
            'nip_nuptk'          => 'nullable|string|max:50',
            'tempat_lahir'       => 'nullable|string',
            'tanggal_lahir'      => 'nullable|date',
            'jenis_kelamin'      => 'nullable|string',
            'agama'              => 'nullable|string',
            'status_pernikahan'  => 'nullable|string',
            'pendidikan_terakhir'=> 'nullable|string',
            'gelar_depan'        => 'nullable|string',
            'gelar_belakang'     => 'nullable|string',
            'alamat'             => 'nullable|string',
            'jabatan'            => 'nullable|string',
            'status_kepegawaian' => 'nullable|string',
            'status_aktif'       => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update User fields
            if ($request->has('nama_depan')) {
                $fullName = trim($request->input('nama_depan') . ' ' . ($request->input('nama_belakang') ?: ''));
                $u->name = $fullName;
            }
            if ($request->has('emailLogin')) {
                $u->email = $request->input('emailLogin');
            }
            if ($request->has('noHpLogin')) {
                $u->phone = $request->input('noHpLogin');
            }
            if ($request->has('password') && $request->input('password')) {
                $u->password = Hash::make($request->input('password'));
            }
            if ($request->has('status_aktif')) {
                $u->is_active = in_array(strtolower($request->input('status_aktif')), ['aktif', 'active', 'true', '1']);
            }
            
            // If position/jabatan is changed, we update the role
            if ($request->has('jabatan')) {
                $roleName = $this->resolveRoleFromPosition($request->input('jabatan'));
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $u->role_id = $role->id;
                }
            }

            $u->save();

            // Update Teacher Profile fields
            $profileData = [];
            if ($request->has('nik')) $profileData['nik'] = $request->input('nik');
            if ($request->has('nip_nuptk')) $profileData['nip_nuptk'] = $request->input('nip_nuptk');
            if ($request->has('tempat_lahir')) $profileData['birth_place'] = $request->input('tempat_lahir');
            if ($request->has('tanggal_lahir')) $profileData['birth_date'] = $request->input('tanggal_lahir');
            if ($request->has('jenis_kelamin')) $profileData['gender'] = $this->mapGenderToDb($request->input('jenis_kelamin'));
            if ($request->has('agama')) $profileData['religion'] = $request->input('agama');
            if ($request->has('status_pernikahan')) $profileData['marital_status'] = $request->input('status_pernikahan');
            if ($request->has('pendidikan_terakhir')) $profileData['last_education'] = $request->input('pendidikan_terakhir');
            if ($request->has('gelar_depan')) $profileData['front_title'] = $request->input('gelar_depan');
            if ($request->has('gelar_belakang')) $profileData['back_title'] = $request->input('gelar_belakang');
            if ($request->has('alamat')) $profileData['address'] = $request->input('alamat');
            if ($request->has('jabatan')) $profileData['position'] = $request->input('jabatan');
            if ($request->has('status_kepegawaian')) $profileData['employment_status'] = $request->input('status_kepegawaian');

            $teacher->update($profileData);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Guru/Staff updated successfully.',
                'data'    => $teacher,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Transaction failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a teacher profile + user.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $currentUser = $request->user();
        $u = User::find($id);

        if (!$u) {
            return response()->json(['status' => 'error', 'message' => 'Teacher/Staff user not found.'], 404);
        }

        // Scope check
        if (!$currentUser->isSuperAdmin()) {
            if ($currentUser->hasRole('admin_yayasan')) {
                if ($u->foundation_id != $currentUser->foundation_id && ($u->school && $u->school->foundation_id != $currentUser->foundation_id)) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
                }
            } else {
                if ($u->school_id != $currentUser->school_id) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
                }
            }
        }

        DB::beginTransaction();
        try {
            // Delete TeacherProfile first
            TeacherProfile::where('user_id', $u->id)->delete();
            // Delete User
            $u->delete();

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Guru/Staff deleted successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Deletion failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
