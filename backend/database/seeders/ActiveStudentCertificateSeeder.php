<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ActiveStudentCertificate;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActiveStudentCertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Query the default school and academic year from DatabaseSeeder
        $academicYear = AcademicYear::first();
        if (!$academicYear) {
            return;
        }

        // Get student profiles from the same school
        $students = StudentProfile::with(['user', 'classroom'])
            ->whereHas('user', function ($q) use ($academicYear) {
                $q->where('school_id', $academicYear->school_id);
            })->take(10)->get();

        if ($students->isEmpty()) {
            return;
        }

        $semesters = ['Ganjil', 'Genap'];
        $statuses = ['Selesai', 'Tercetak'];

        foreach ($students as $index => $student) {
            ActiveStudentCertificate::create([
                'foundation_id'    => $student->user->foundation_id ?? 1,
                'school_id'        => $student->user->school_id,
                'student_id'       => $student->user_id,
                'academic_year_id' => $academicYear->id,
                'semester'         => $semesters[$index % 2],
                'nama'             => $student->user->name,
                'nisn'             => $student->nisn,
                'kelas'            => $student->classroom->name ?? 'VI A',
                'tanggal_lahir'    => $student->birth_date ?? '2016-08-15',
                'alamat'           => $student->address ?? 'Jl. Mawar No. 10, Bekasi',
                'status'           => $statuses[$index % 2],
                'tanggal_dibuat'   => now()->subDays($index * 2)->format('Y-m-d'),
            ]);
        }
    }
}
