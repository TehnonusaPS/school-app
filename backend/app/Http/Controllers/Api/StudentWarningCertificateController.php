<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentWarningCertificate;
use App\Models\User;
use App\Events\StudentWarningCertificateCreated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class StudentWarningCertificateController extends Controller
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

        $query = StudentWarningCertificate::query();

        // Scope to school & foundation
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
                  ->orWhere('jenis_surat', 'like', "%{$search}%");
            });
        }

        $certificates = $query->orderBy('created_at', 'desc')->get();

        // Map to expected camelCase keys in frontend
        $mapped = $certificates->map(function ($item) {
            return [
                'id'                 => $item->id,
                'tanggalDibuat'      => $item->tanggal_dibuat ? $item->tanggal_dibuat->format('Y-m-d') : '',
                'jenisSurat'         => $item->jenis_surat,
                'studentId'          => $item->student_id,
                'namaSiswa'          => $item->nama,
                'nisn'               => $item->nisn,
                'kelas'              => $item->kelas,
                'tanggal'            => $item->tanggal ? $item->tanggal->format('Y-m-d') : '',
                'perihalPelanggaran' => $item->perihal_pelanggaran,
                'jumlahTunggakan'    => $item->jumlah_tunggakan,
                'status'             => $item->status,
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
            'jenisSurat'         => 'required|string|in:Surat Pelanggaran,Surat Tunggakan',
            'studentId'          => 'required|exists:users,id',
            'tanggal'            => 'nullable|required_if:jenisSurat,Surat Pelanggaran|date',
            'perihalPelanggaran' => 'nullable|required_if:jenisSurat,Surat Pelanggaran|string',
            'jumlahTunggakan'    => 'nullable|required_if:jenisSurat,Surat Tunggakan|string',
            'status'             => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify student school
        $studentUser = User::with('studentProfile')->find($request->studentId);
        if ((int) $studentUser->school_id !== (int) $schoolId) {
            return response()->json([
                'message' => 'Siswa yang dipilih berada di luar sekolah Anda.'
            ], 403);
        }

        $certificate = StudentWarningCertificate::create([
            'foundation_id'       => $foundationId,
            'school_id'           => $schoolId,
            'student_id'          => $studentUser->id,
            'tanggal_dibuat'      => now()->format('Y-m-d'),
            'jenis_surat'         => $request->jenisSurat,
            'nama'                => $studentUser->name,
            'nisn'                => $studentUser->studentProfile->nisn ?? '',
            'kelas'               => $request->kelas ?? $studentUser->studentProfile->classroom->name ?? '',
            'tanggal'             => $request->jenisSurat === 'Surat Pelanggaran' ? $request->tanggal : null,
            'perihal_pelanggaran' => $request->jenisSurat === 'Surat Pelanggaran' ? $request->perihalPelanggaran : null,
            'jumlah_tunggakan'    => $request->jenisSurat === 'Surat Tunggakan' ? $request->jumlahTunggakan : null,
            'status'              => $request->status ?? 'Selesai',
        ]);

        // Broadcast event
        try {
            broadcast(new StudentWarningCertificateCreated($certificate))->toOthers();
        } catch (\Exception $e) {
            Log::warning('Realtime StudentWarningCertificateCreated broadcast failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Surat peringatan berhasil dibuat.',
            'data'    => [
                'id'                 => $certificate->id,
                'tanggalDibuat'      => $certificate->tanggal_dibuat ? $certificate->tanggal_dibuat->format('Y-m-d') : '',
                'jenisSurat'         => $certificate->jenis_surat,
                'studentId'          => $certificate->student_id,
                'namaSiswa'          => $certificate->nama,
                'nisn'               => $certificate->nisn,
                'kelas'              => $certificate->kelas,
                'tanggal'            => $certificate->tanggal ? $certificate->tanggal->format('Y-m-d') : '',
                'perihalPelanggaran' => $certificate->perihal_pelanggaran,
                'jumlahTunggakan'    => $certificate->jumlah_tunggakan,
                'status'             => $certificate->status,
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $certificate = StudentWarningCertificate::find($id);

        if (!$certificate) {
            return response()->json(['message' => 'Surat tidak ditemukan.'], 404);
        }

        // Security check
        if ($user->school_id && (int) $certificate->school_id !== (int) $user->school_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'id'                 => $certificate->id,
            'tanggalDibuat'      => $certificate->tanggal_dibuat ? $certificate->tanggal_dibuat->format('Y-m-d') : '',
            'jenisSurat'         => $certificate->jenis_surat,
            'studentId'          => $certificate->student_id,
            'namaSiswa'          => $certificate->nama,
            'nisn'               => $certificate->nisn,
            'kelas'              => $certificate->kelas,
            'tanggal'            => $certificate->tanggal ? $certificate->tanggal->format('Y-m-d') : '',
            'perihalPelanggaran' => $certificate->perihal_pelanggaran,
            'jumlahTunggakan'    => $certificate->jumlah_tunggakan,
            'status'             => $certificate->status,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $certificate = StudentWarningCertificate::find($id);

        if (!$certificate) {
            return response()->json(['message' => 'Surat tidak ditemukan.'], 404);
        }

        // Security check
        if ($user->school_id && (int) $certificate->school_id !== (int) $user->school_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'jenisSurat'         => 'required|string|in:Surat Pelanggaran,Surat Tunggakan',
            'studentId'          => 'required|exists:users,id',
            'tanggal'            => 'nullable|required_if:jenisSurat,Surat Pelanggaran|date',
            'perihalPelanggaran' => 'nullable|required_if:jenisSurat,Surat Pelanggaran|string',
            'jumlahTunggakan'    => 'nullable|required_if:jenisSurat,Surat Tunggakan|string',
            'status'             => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [
            'jenis_surat'         => $request->jenisSurat,
            'status'              => $request->status ?? $certificate->status,
            'tanggal'             => $request->jenisSurat === 'Surat Pelanggaran' ? $request->tanggal : null,
            'perihal_pelanggaran' => $request->jenisSurat === 'Surat Pelanggaran' ? $request->perihalPelanggaran : null,
            'jumlah_tunggakan'    => $request->jenisSurat === 'Surat Tunggakan' ? $request->jumlahTunggakan : null,
        ];

        // Update student snapshot if changed
        if ((int) $certificate->student_id !== (int) $request->studentId) {
            $studentUser = User::with('studentProfile')->find($request->studentId);
            if ($studentUser && (int) $studentUser->school_id === (int) $certificate->school_id) {
                $updateData['student_id'] = $studentUser->id;
                $updateData['nama']       = $studentUser->name;
                $updateData['nisn']       = $studentUser->studentProfile->nisn ?? '';
                $updateData['kelas']      = $request->kelas ?? $studentUser->studentProfile->classroom->name ?? '';
            }
        } else {
            // Even if student ID is same, update class/snapshot if passed
            if ($request->has('kelas')) {
                $updateData['kelas'] = $request->kelas;
            }
        }

        $certificate->update($updateData);

        return response()->json([
            'message' => 'Surat peringatan berhasil diperbarui.',
            'data'    => [
                'id'                 => $certificate->id,
                'tanggalDibuat'      => $certificate->tanggal_dibuat ? $certificate->tanggal_dibuat->format('Y-m-d') : '',
                'jenisSurat'         => $certificate->jenis_surat,
                'studentId'          => $certificate->student_id,
                'namaSiswa'          => $certificate->nama,
                'nisn'               => $certificate->nisn,
                'kelas'              => $certificate->kelas,
                'tanggal'            => $certificate->tanggal ? $certificate->tanggal->format('Y-m-d') : '',
                'perihalPelanggaran' => $certificate->perihal_pelanggaran,
                'jumlahTunggakan'    => $certificate->jumlah_tunggakan,
                'status'             => $certificate->status,
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $certificate = StudentWarningCertificate::find($id);

        if (!$certificate) {
            return response()->json(['message' => 'Surat tidak ditemukan.'], 404);
        }

        // Security check
        if ($user->school_id && (int) $certificate->school_id !== (int) $user->school_id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $certificate->delete();

        return response()->json([
            'message' => 'Surat peringatan berhasil dihapus.'
        ]);
    }
}
