<?php

namespace Database\Seeders;

use App\Models\StudentDispensationCertificate;
use App\Models\StudentDispensationStudent;
use App\Models\StudentProfile;
use Illuminate\Database\Seeder;

class StudentDispensationCertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get student profiles from the database
        $students = StudentProfile::with(['user', 'classroom'])->take(6)->get();

        if ($students->isEmpty()) {
            return;
        }

        // Mock certificate items
        $mockData = [
            [
                'perihal' => 'Mengikuti Olimpiade Sains Tingkat Provinsi',
                'days_ago' => 5,
                'duration' => 2,
                'student_count' => 2,
            ],
            [
                'perihal' => 'Kegiatan Pramuka di luar sekolah',
                'days_ago' => 10,
                'duration' => 1,
                'student_count' => 1,
            ],
            [
                'perihal' => 'Mengikuti Turnamen Futsal Antar Sekolah',
                'days_ago' => 15,
                'duration' => 3,
                'student_count' => 3,
            ],
        ];

        $studentIndex = 0;

        foreach ($mockData as $item) {
            $firstStudent = $students[$studentIndex % $students->count()];
            
            // Create certificate
            $certificate = StudentDispensationCertificate::create([
                'foundation_id'  => $firstStudent->user->foundation_id ?? 1,
                'school_id'      => $firstStudent->user->school_id ?? 1,
                'tanggal_dibuat' => now()->subDays($item['days_ago'])->format('Y-m-d'),
                'tanggal_awal'   => now()->subDays($item['days_ago'])->format('Y-m-d'),
                'tanggal_akhir'  => now()->subDays($item['days_ago'] - $item['duration'])->format('Y-m-d'),
                'perihal'        => $item['perihal'],
                'status'         => 'Selesai',
            ]);

            // Add students to this certificate
            for ($i = 0; $i < $item['student_count']; $i++) {
                $student = $students[$studentIndex % $students->count()];
                
                StudentDispensationStudent::create([
                    'dispensation_id' => $certificate->id,
                    'student_id'      => $student->user_id,
                    'nama'            => $student->user->name,
                    'nisn'            => $student->nisn,
                    'kelas'           => $student->classroom->name ?? 'VI A',
                ]);

                $studentIndex++;
            }
        }
    }
}
