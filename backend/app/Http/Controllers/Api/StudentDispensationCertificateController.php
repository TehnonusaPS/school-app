<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentDispensationCertificate;
use App\Models\StudentDispensationStudent;
use App\Models\User;
use App\Events\StudentDispensationCertificateCreated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class StudentDispensationCertificateController extends Controller
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

        $query = StudentDispensationCertificate::with('students');

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
                // Search inside relation student names
                $q->whereHas('students', function ($sq) use ($search) {
                    $sq->where('nama', 'like', "%{$search}%")
                      ->orWhere('nisn', 'like', "%{$search}%");
                })->orWhere('perihal', 'like', "%{$search}%");
            });
        }

        if ($request->has('tanggalDibuat') && !empty($request->tanggalDibuat)) {
            $query->whereDate('tanggal_dibuat', $request->tanggalDibuat);
        }

        $certificates = $query->orderBy('created_at', 'desc')->get();

        // Map to expected camelCase keys in frontend
        $mapped = $certificates->map(function ($item) {
            return [
                'id'            => $item->id,
                'tanggalDibuat' => $item->tanggal_dibuat ? $item->tanggal_dibuat->format('Y-m-d') : '',
                'tanggalAwal'   => $item->tanggal_awal ? $item->tanggal_awal->format('Y-m-d') : '',
                'tanggalAkhir'  => $item->tanggal_akhir ? $item->tanggal_akhir->format('Y-m-d') : '',
                'perihal'       => $item->perihal,
                'status'        => $item->status,
                'siswa'         => $item->students->map(function ($s) {
                    return [
                        'nama'  => $s->nama,
                        'nisn'  => $s->nisn,
                        'kelas' => $s->kelas,
                    ];
                })->toArray(),
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
            'tanggalAwal'   => 'required|date',
            'tanggalAkhir'  => 'required|date',
            'perihal'       => 'required|string',
            'siswa'         => 'required|array|min:1',
            'siswa.*.student_id' => 'required|exists:users,id',
            'status'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $certificate = StudentDispensationCertificate::create([
            'foundation_id'  => $foundationId,
            'school_id'      => $schoolId,
            'tanggal_dibuat' => now()->format('Y-m-d'),
            'tanggal_awal'   => $request->tanggalAwal,
            'tanggal_akhir'  => $request->tanggalAkhir,
            'perihal'        => $request->perihal,
            'status'         => $request->status ?? 'Selesai',
        ]);

        // Create detail dispensation students
        foreach ($request->siswa as $s) {
            $studentUser = User::with('studentProfile')->find($s['student_id']);
            
            if ($studentUser && (int) $studentUser->school_id === (int) $schoolId) {
                StudentDispensationStudent::create([
                    'dispensation_id' => $certificate->id,
                    'student_id'      => $studentUser->id,
                    'nama'            => $studentUser->name,
                    'nisn'            => $studentUser->studentProfile->nisn ?? $s['nisn'] ?? '',
                    'kelas'           => $s['kelas'] ?? '',
                ]);
            }
        }

        // Trigger Broadcast
        try {
            $certificate->load('students');
            broadcast(new StudentDispensationCertificateCreated($certificate))->toOthers();
        } catch (\Exception $e) {
            Log::warning('Realtime StudentDispensationCertificateCreated broadcast failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Surat dispensasi berhasil dibuat.',
            'data'    => [
                'id'            => $certificate->id,
                'tanggalDibuat' => $certificate->tanggal_dibuat ? $certificate->tanggal_dibuat->format('Y-m-d') : '',
                'tanggalAwal'   => $certificate->tanggal_awal ? $certificate->tanggal_awal->format('Y-m-d') : '',
                'tanggalAkhir'  => $certificate->tanggal_akhir ? $certificate->tanggal_akhir->format('Y-m-d') : '',
                'perihal'       => $certificate->perihal,
                'status'        => $certificate->status,
                'siswa'         => $certificate->students->map(function ($s) {
                    return [
                        'nama'  => $s->nama,
                        'nisn'  => $s->nisn,
                        'kelas' => $s->kelas,
                    ];
                })->toArray(),
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $certificate = StudentDispensationCertificate::with('students')->find($id);

        if (!$certificate) {
            return response()->json(['message' => 'Surat tidak ditemukan.'], 404);
        }

        // Security check
        if ($user->school_id && (int) $certificate->school_id !== (int) $user->school_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'id'            => $certificate->id,
            'tanggalDibuat' => $certificate->tanggal_dibuat ? $certificate->tanggal_dibuat->format('Y-m-d') : '',
            'tanggalAwal'   => $certificate->tanggal_awal ? $certificate->tanggal_awal->format('Y-m-d') : '',
            'tanggalAkhir'  => $certificate->tanggal_akhir ? $certificate->tanggal_akhir->format('Y-m-d') : '',
            'perihal'       => $certificate->perihal,
            'status'        => $certificate->status,
            'siswa'         => $certificate->students->map(function ($s) {
                return [
                    'nama'  => $s->nama,
                    'nisn'  => $s->nisn,
                    'kelas' => $s->kelas,
                ];
            })->toArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $certificate = StudentDispensationCertificate::find($id);

        if (!$certificate) {
            return response()->json(['message' => 'Surat tidak ditemukan.'], 404);
        }

        // Security check
        if ($user->school_id && (int) $certificate->school_id !== (int) $user->school_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'tanggalAwal'   => 'required|date',
            'tanggalAkhir'  => 'required|date',
            'perihal'       => 'required|string',
            'siswa'         => 'required|array|min:1',
            'siswa.*.student_id' => 'required|exists:users,id',
            'status'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $certificate->update([
            'tanggal_awal'   => $request->tanggalAwal,
            'tanggal_akhir'  => $request->tanggalAkhir,
            'perihal'        => $request->perihal,
            'status'         => $request->status ?? $certificate->status,
        ]);

        // Recreate dispensation students
        StudentDispensationStudent::where('dispensation_id', $certificate->id)->delete();
        
        foreach ($request->siswa as $s) {
            $studentUser = User::with('studentProfile')->find($s['student_id']);
            if ($studentUser && (int) $studentUser->school_id === (int) $certificate->school_id) {
                StudentDispensationStudent::create([
                    'dispensation_id' => $certificate->id,
                    'student_id'      => $studentUser->id,
                    'nama'            => $studentUser->name,
                    'nisn'            => $studentUser->studentProfile->nisn ?? $s['nisn'] ?? '',
                    'kelas'           => $s['kelas'] ?? '',
                ]);
            }
        }

        $certificate->load('students');

        return response()->json([
            'message' => 'Surat dispensasi berhasil diperbarui.',
            'data'    => [
                'id'            => $certificate->id,
                'tanggalDibuat' => $certificate->tanggal_dibuat ? $certificate->tanggal_dibuat->format('Y-m-d') : '',
                'tanggalAwal'   => $certificate->tanggal_awal ? $certificate->tanggal_awal->format('Y-m-d') : '',
                'tanggalAkhir'  => $certificate->tanggal_akhir ? $certificate->tanggal_akhir->format('Y-m-d') : '',
                'perihal'       => $certificate->perihal,
                'status'        => $certificate->status,
                'siswa'         => $certificate->students->map(function ($s) {
                    return [
                        'nama'  => $s->nama,
                        'nisn'  => $s->nisn,
                        'kelas' => $s->kelas,
                    ];
                })->toArray(),
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $certificate = StudentDispensationCertificate::find($id);

        if (!$certificate) {
            return response()->json(['message' => 'Surat tidak ditemukan.'], 404);
        }

        // Security check
        if ($user->school_id && (int) $certificate->school_id !== (int) $user->school_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $certificate->delete();

        return response()->json([
            'message' => 'Surat dispensasi berhasil dihapus.'
        ]);
    }
}
