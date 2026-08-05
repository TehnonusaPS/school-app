<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\StudentAttendance;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\StaffAttendance;
use App\Models\StudentPayment;
use App\Models\AssessmentScore;
use App\Models\Classroom;

class ReportSchoolController extends Controller
{
    public function attendance(Request $request): JsonResponse
    {
        $schoolId = $request->user()->school_id;
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $attendances = StudentAttendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereHas('studentProfile.user', function($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->with(['studentProfile.user', 'studentProfile.classroom'])
            ->orderBy('date', 'desc')
            ->get();

        $data = $attendances->map(function ($att) {
            $student = $att->studentProfile;
            return [
                'id' => $att->id,
                'tanggal' => Carbon::parse($att->date)->translatedFormat('d M Y'),
                'nama' => $student->user->name ?? '-',
                'kelas' => $student->classroom->name ?? '-',
                'jamMasuk' => $att->time_in ?? '-',
                'jamKeluar' => $att->time_out ?? '-',
                'status' => strtolower($att->status) === 'h' ? 'hadir' : 
                           (strtolower($att->status) === 't' ? 'terlambat' : 
                           (strtolower($att->status) === 'i' ? 'izin' : 
                           (strtolower($att->status) === 's' ? 'sakit' : 'alpa'))),
            ];
        });

        return response()->json($data);
    }

    public function academic(Request $request): JsonResponse
    {
        $schoolId = $request->user()->school_id;
        $classrooms = Classroom::where('school_id', $schoolId)->with('students')->get();

        $kelasData = [];
        foreach ($classrooms as $classroom) {
            $studentsCount = $classroom->students->count();
            // Mock kelulusan logic (since we don't have full grading system here yet)
            $lulus = (int) ($studentsCount * 0.9);
            $tidakLulus = $studentsCount - $lulus;

            $kelasData[] = [
                'id' => $classroom->id,
                'kelas' => $classroom->name,
                'waliKelas' => 'Wali Kelas ' . $classroom->name, // Should be from teacher assignment
                'totalSiswa' => $studentsCount,
                'lulus' => $lulus,
                'tidakLulus' => $tidakLulus,
                'rataNilai' => rand(75, 90),
                'kehadiran' => rand(85, 100),
            ];
        }

        // Mapel Mock Data (for chart) since Subject model might lack complete AssessmentScore data right now
        $mapelData = [
            ['mapel' => 'Matematika', 'avg' => 79.2, 'tertinggi' => 98, 'terendah' => 55, 'tuntas' => 78],
            ['mapel' => 'Fisika', 'avg' => 77.8, 'tertinggi' => 95, 'terendah' => 52, 'tuntas' => 72],
            ['mapel' => 'Kimia', 'avg' => 80.1, 'tertinggi' => 97, 'terendah' => 58, 'tuntas' => 81],
        ];

        return response()->json([
            'kelas' => $kelasData,
            'mapel' => $mapelData,
        ]);
    }

    public function finance(Request $request): JsonResponse
    {
        $schoolId = $request->user()->school_id;
        
        $payments = StudentPayment::whereHas('studentProfile.user', function($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })->with('studentProfile.user')->latest()->take(10)->get();

        $pemasukan = $payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'tanggal' => Carbon::parse($payment->created_at)->translatedFormat('d M Y'),
                'keterangan' => 'Pembayaran SPP - ' . ($payment->studentProfile->user->name ?? ''),
                'kategori' => 'SPP',
                'jumlah' => $payment->amount ?? 0,
                'status' => strtolower($payment->status) === 'verified' ? 'lunas' : 'sebagian',
            ];
        });

        // Pengeluaran mockup (if expense model doesn't exist yet)
        $pengeluaran = [
            ['id' => 1, 'tanggal' => '3 Jan 2026', 'keterangan' => 'Gaji Guru & Staf', 'kategori' => 'Gaji', 'jumlah' => 38000000, 'status' => 'dibayar'],
            ['id' => 2, 'tanggal' => '5 Jan 2026', 'keterangan' => 'Operasional', 'kategori' => 'Operasional', 'jumlah' => 3200000, 'status' => 'dibayar'],
        ];

        return response()->json([
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
        ]);
    }

    public function grades(Request $request): JsonResponse
    {
        $schoolId = $request->user()->school_id;
        $students = StudentProfile::whereHas('user', function($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })->with(['user', 'classroom', 'attendances'])->take(20)->get();

        $data = $students->map(function ($student) {
            $attendances = $student->attendances;
            $hadir = $attendances->whereIn('status', ['H', 'T'])->count();
            $terlambat = $attendances->where('status', 'T')->count();
            $izin = $attendances->where('status', 'I')->count();
            $sakit = $attendances->where('status', 'S')->count();
            $alpa = $attendances->where('status', 'A')->count();

            return [
                'id' => $student->id,
                'nisn' => $student->nisn ?? '-',
                'nama' => $student->user->name ?? '-',
                'kelas' => $student->classroom->name ?? '-',
                'waliKelas' => '-',
                'tp' => '2025/2026',
                'semester' => '1',
                'nilai' => [
                    'Matematika' => rand(75, 95),
                    'Bahasa Indonesia' => rand(80, 95),
                    'Bahasa Inggris' => rand(70, 90),
                ],
                'kehadiran' => [
                    'hadir' => $hadir,
                    'terlambat' => $terlambat,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alpa' => $alpa,
                ],
                'catatan' => 'Siswa telah menyelesaikan pembelajaran semester ini dengan baik.',
            ];
        });

        return response()->json($data);
    }

    public function studentDevelopment(Request $request): JsonResponse
    {
        $schoolId = $request->user()->school_id;
        $students = StudentProfile::whereHas('user', function($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })->with(['user', 'classroom'])->take(15)->get();

        $siswaList = [];
        $data = [];

        foreach ($students as $student) {
            $siswaList[] = [
                'id' => (string) $student->id,
                'nama' => $student->user->name ?? '-',
                'nisn' => $student->nisn ?? '-',
                'kelas' => $student->classroom->name ?? '-'
            ];

            $attendances = StudentAttendance::where('student_profile_id', $student->id)->get();
            $hadir = $attendances->whereIn('status', ['H', 'T'])->count();
            $izin = $attendances->where('status', 'I')->count();
            $sakit = $attendances->where('status', 'S')->count();
            $alpa = $attendances->where('status', 'A')->count();

            $data[(string) $student->id] = [
                'akademik' => [
                    ['bulan' => 'Jul', 'nilai' => rand(75, 90)],
                    ['bulan' => 'Agu', 'nilai' => rand(75, 95)],
                    ['bulan' => 'Sep', 'nilai' => rand(80, 95)],
                ],
                'kehadiran' => [
                    'hadir' => $hadir,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alpa' => $alpa
                ],
                'sikap' => [
                    'spiritual' => 'Baik', 'sosial' => 'Baik', 'catatan' => 'Siswa menunjukkan perkembangan yang positif.'
                ],
                'prestasi' => []
            ];
        }

        return response()->json([
            'siswaList' => $siswaList,
            'data' => $data,
        ]);
    }


    public function staff(Request $request): JsonResponse
    {
        $schoolId = $request->user()->school_id;
        $teachers = TeacherProfile::whereHas('user', function($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })->with('user')->get();

        $data = $teachers->map(function ($teacher) {
            // Using StaffAttendance model if available
            $attendances = StaffAttendance::where('user_id', $teacher->user_id)->get();
            $hadir = $attendances->where('status', 'H')->count();
            $terlambat = $attendances->where('status', 'T')->count();
            $izin = $attendances->where('status', 'I')->count();
            $sakit = $attendances->where('status', 'S')->count();
            $alpa = $attendances->where('status', 'A')->count();

            return [
                'id' => $teacher->id,
                'nik' => $teacher->nip ?? 'GTK' . str_pad($teacher->id, 3, '0', STR_PAD_LEFT),
                'nama' => $teacher->user->name ?? '-',
                'jabatan' => 'Guru',
                'status' => 'Tetap', // Can be mapped from Employment status
                'hadir' => $hadir > 0 ? $hadir : rand(18, 22), // Mock if no data
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
            ];
        });

        return response()->json($data);
    }
}