<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ActiveStudentCertificate;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActiveStudentCertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $schoolId = $user->school_id;
        $foundationId = $user->foundation_id;

        if ($schoolId && !$foundationId) {
            $schoolObj = \App\Models\School::find($schoolId);
            if ($schoolObj) {
                $foundationId = $schoolObj->foundation_id;
            }
        }

        $query = ActiveStudentCertificate::query();

        // Scope to user's school & foundation
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }
        if ($foundationId) {
            $query->where('foundation_id', $foundationId);
        }

        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%");
            });
        }

        if ($request->has('tanggalDibuat') && !empty($request->tanggalDibuat)) {
            $query->whereDate('tanggal_dibuat', $request->tanggalDibuat);
        }

        $certificates = $query->orderBy('created_at', 'desc')->get();

        // Map database naming to frontend expected camelCase
        $mapped = $certificates->map(function ($item) {
            return [
                'id'            => $item->id,
                'studentId'     => $item->student_id,
                'academicYearId'=> $item->academic_year_id,
                'nama'          => $item->nama,
                'nisn'          => $item->nisn,
                'kelas'         => $item->kelas,
                'tanggalLahir'  => $item->tanggal_lahir ? $item->tanggal_lahir->format('Y-m-d') : '',
                'alamat'        => $item->alamat,
                'tahunAkademik' => $item->academicYear ? $item->academicYear->name : '',
                'semester'      => $item->semester,
                'status'        => $item->status,
                'tanggalDibuat' => $item->tanggal_dibuat ? $item->tanggal_dibuat->format('Y-m-d') : '',
            ];
        });

        return response()->json($mapped);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $schoolId = $user->school_id;
        $foundationId = $user->foundation_id;

        if ($schoolId && !$foundationId) {
            $schoolObj = \App\Models\School::find($schoolId);
            if ($schoolObj) {
                $foundationId = $schoolObj->foundation_id;
            }
        }

        if (!$schoolId || !$foundationId) {
            return response()->json([
                'message' => 'Anda tidak memiliki otorisasi sekolah/yayasan yang valid.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'student_id'       => 'required|exists:users,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester'         => 'required|string',
            'kelas'            => 'required|string',
            'tanggal_lahir'    => 'required|date',
            'alamat'           => 'required|string',
            'status'           => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify the student belongs to the same school
        $studentUser = User::with('studentProfile')->find($request->student_id);
        if ((int) $studentUser->school_id !== (int) $schoolId) {
            return response()->json([
                'message' => 'Siswa yang dipilih berada di luar sekolah Anda.'
            ], 403);
        }

        // Verify Academic Year belongs to the same school
        $academicYear = AcademicYear::find($request->academic_year_id);
        if ((int) $academicYear->school_id !== (int) $schoolId) {
            return response()->json([
                'message' => 'Tahun akademik yang dipilih berada di luar sekolah Anda.'
            ], 403);
        }

        $certificate = ActiveStudentCertificate::create([
            'foundation_id'    => $foundationId,
            'school_id'        => $schoolId,
            'student_id'       => $request->student_id,
            'academic_year_id' => $request->academic_year_id,
            'semester'         => $request->semester,
            'nama'             => $studentUser->name,
            'nisn'             => $studentUser->studentProfile->nisn ?? '',
            'kelas'            => $request->kelas,
            'tanggal_lahir'    => $request->tanggal_lahir,
            'alamat'           => $request->alamat,
            'status'           => $request->status ?? 'Selesai',
            'tanggal_dibuat'   => now()->format('Y-m-d'),
        ]);

        try {
            broadcast(new \App\Events\ActiveStudentCertificateCreated($certificate))->toOthers();
        } catch (\Exception $e) {
            \Log::warning('Realtime ActiveStudentCertificateCreated broadcast failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Surat keterangan aktif berhasil dibuat.',
            'data'    => [
                'id'            => $certificate->id,
                'studentId'     => $certificate->student_id,
                'academicYearId'=> $certificate->academic_year_id,
                'nama'          => $certificate->nama,
                'nisn'          => $certificate->nisn,
                'kelas'         => $certificate->kelas,
                'tanggalLahir'  => $certificate->tanggal_lahir ? $certificate->tanggal_lahir->format('Y-m-d') : '',
                'alamat'        => $certificate->alamat,
                'tahunAkademik' => $academicYear->name,
                'semester'      => $certificate->semester,
                'status'        => $certificate->status,
                'tanggalDibuat' => $certificate->tanggal_dibuat ? $certificate->tanggal_dibuat->format('Y-m-d') : '',
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $certificate = ActiveStudentCertificate::with('academicYear')->find($id);

        if (!$certificate) {
            return response()->json(['message' => 'Surat tidak ditemukan.'], 404);
        }

        // Security check
        if ($user->school_id && (int) $certificate->school_id !== (int) $user->school_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'id'            => $certificate->id,
            'studentId'     => $certificate->student_id,
            'academicYearId'=> $certificate->academic_year_id,
            'nama'          => $certificate->nama,
            'nisn'          => $certificate->nisn,
            'kelas'         => $certificate->kelas,
            'tanggalLahir'  => $certificate->tanggal_lahir ? $certificate->tanggal_lahir->format('Y-m-d') : '',
            'alamat'        => $certificate->alamat,
            'tahunAkademik' => $certificate->academicYear ? $certificate->academicYear->name : '',
            'semester'      => $certificate->semester,
            'status'        => $certificate->status,
            'tanggalDibuat' => $certificate->tanggal_dibuat ? $certificate->tanggal_dibuat->format('Y-m-d') : '',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $certificate = ActiveStudentCertificate::find($id);

        if (!$certificate) {
            return response()->json(['message' => 'Surat tidak ditemukan.'], 404);
        }

        // Security check
        if ($user->school_id && (int) $certificate->school_id !== (int) $user->school_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester'         => 'required|string',
            'kelas'            => 'required|string',
            'tanggal_lahir'    => 'required|date',
            'alamat'           => 'required|string',
            'status'           => 'nullable|string',
            'student_id'       => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify Academic Year belongs to the same school
        $academicYear = AcademicYear::find($request->academic_year_id);
        if ((int) $academicYear->school_id !== (int) $certificate->school_id) {
            return response()->json([
                'message' => 'Tahun akademik yang dipilih berada di luar sekolah Anda.'
            ], 403);
        }

        $updateData = [
            'academic_year_id' => $request->academic_year_id,
            'semester'         => $request->semester,
            'kelas'            => $request->kelas,
            'tanggal_lahir'    => $request->tanggal_lahir,
            'alamat'           => $request->alamat,
            'status'           => $request->status ?? $certificate->status,
        ];

        // Jika student_id dikirim dan berbeda, update data siswa dari User
        if ($request->has('student_id') && $request->student_id) {
            $studentUser = User::with('studentProfile')->find($request->student_id);
            if ($studentUser && (int) $studentUser->school_id === (int) $certificate->school_id) {
                $updateData['student_id'] = $studentUser->id;
                $updateData['nama']       = $studentUser->name;
                $updateData['nisn']       = $studentUser->studentProfile->nisn ?? $certificate->nisn;
            }
        }

        $certificate->update($updateData);

        return response()->json([
            'message' => 'Surat keterangan aktif berhasil diperbarui.',
            'data'    => [
                'id'            => $certificate->id,
                'studentId'     => $certificate->student_id,
                'academicYearId'=> $certificate->academic_year_id,
                'nama'          => $certificate->nama,
                'nisn'          => $certificate->nisn,
                'kelas'         => $certificate->kelas,
                'tanggalLahir'  => $certificate->tanggal_lahir ? $certificate->tanggal_lahir->format('Y-m-d') : '',
                'alamat'        => $certificate->alamat,
                'tahunAkademik' => $academicYear->name,
                'semester'      => $certificate->semester,
                'status'        => $certificate->status,
                'tanggalDibuat' => $certificate->tanggal_dibuat ? $certificate->tanggal_dibuat->format('Y-m-d') : '',
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $certificate = ActiveStudentCertificate::find($id);

        if (!$certificate) {
            return response()->json(['message' => 'Surat tidak ditemukan.'], 404);
        }

        // Security check
        if ($user->school_id && (int) $certificate->school_id !== (int) $user->school_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $certificate->delete();

        return response()->json([
            'message' => 'Surat keterangan aktif berhasil dihapus.'
        ]);
    }
}
