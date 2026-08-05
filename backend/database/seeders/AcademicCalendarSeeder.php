<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\AcademicCalendarEvent;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\User;

class AcademicCalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            $this->command->info('No active academic year found to seed calendar events.');
            return;
        }

        $schoolId = $activeYear->school_id;

        // Get admin user
        $admin = User::whereHas('role', function($q) {
            $q->where('name', 'admin_sekolah');
        })->where('school_id', $schoolId)->first() 
            ?: User::where('school_id', $schoolId)->first() 
            ?: User::first();

        if (!$admin) {
            $this->command->info('No user found to set as creator of calendar events.');
            return;
        }

        // Get a classroom to test class-specific event
        $classroom = Classroom::where('academic_year_id', $activeYear->id)
            ->where('school_id', $schoolId)
            ->first();

        // Clear existing events for this academic year to avoid duplicates
        AcademicCalendarEvent::where('academic_year_id', $activeYear->id)->delete();

        // Let's create some dates within the 2025/2026 year.
        // Assuming 2025/2026 starts in July 2025. Let's base on $activeYear->start_date.
        $startDate = $activeYear->start_date ?: now();
        $startYear = $startDate->format('Y');

        $events = [
            [
                'title' => 'Masa Pengenalan Lingkungan Sekolah (MPLS)',
                'start_date' => $startYear . '-07-14',
                'end_date' => $startYear . '-07-16',
                'type' => 'kegiatan',
                'description' => 'Masa orientasi bagi siswa baru.',
                'classroom_id' => null,
            ],
            [
                'title' => 'Hari Libur Nasional Proklamasi Kemerdekaan RI',
                'start_date' => $startYear . '-08-17',
                'end_date' => $startYear . '-08-17',
                'type' => 'libur_nasional',
                'description' => 'Upacara bendera peringatan HUT RI.',
                'classroom_id' => null,
            ],
            [
                'title' => 'Asesmen Tengah Semester (ATS) Ganjil',
                'start_date' => $startYear . '-09-22',
                'end_date' => $startYear . '-09-26',
                'type' => 'ujian',
                'description' => 'Pelaksanaan ATS tertulis online menggunakan LMS.',
                'classroom_id' => null,
            ],
            [
                'title' => 'Libur Semester Ganjil',
                'start_date' => $startYear . '-12-22',
                'end_date' => $startYear . '-12-31',
                'type' => 'libur_nasional',
                'description' => 'Libur akhir semester ganjil.',
                'classroom_id' => null,
            ],
        ];

        // Add classroom-specific event if classroom exists
        if ($classroom) {
            $events[] = [
                'title' => 'Rapat Kelas Khusus & Bimbingan Konseling Kelas ' . $classroom->name,
                'start_date' => $startYear . '-10-10',
                'end_date' => $startYear . '-10-10',
                'type' => 'kegiatan',
                'description' => 'Rapat koordinasi walimurid khusus.',
                'classroom_id' => $classroom->id,
            ];
        }

        foreach ($events as $eventData) {
            AcademicCalendarEvent::create(array_merge($eventData, [
                'school_id' => $schoolId,
                'academic_year_id' => $activeYear->id,
                'created_by' => $admin->id,
            ]));
        }

        // Set calendar status to approved for active year so it's readily accessible
        $activeYear->update([
            'calendar_status' => 'approved',
            'calendar_submitted_at' => now(),
            'calendar_reviewed_at' => now(),
            'calendar_reviewed_by' => $admin->id,
        ]);

        $this->command->info('Academic calendar events seeded and calendar status set to approved.');
    }
}
