<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\School;
use App\Models\Subject;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Seeder;

class AcademicYear2025Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $school = School::first();
        if (!$school) {
            $this->command->warn('No school found to seed Academic Year 2025/2026.');
            return;
        }

        // 1. Deactivate other academic years
        AcademicYear::where('school_id', $school->id)->update(['is_active' => false]);

        // 2. Create/Update 2025/2026 Odd Semester (Ganjil) - Active
        $ayGanjil = AcademicYear::updateOrCreate(
            [
                'school_id' => $school->id,
                'name'      => '2025/2026',
                'semester'  => 'odd'
            ],
            [
                'start_date' => '2025-07-14',
                'end_date'   => '2025-12-19',
                'is_active'  => true
            ]
        );

        // 3. Create/Update 2025/2026 Even Semester (Genap) - Inactive
        $ayGenap = AcademicYear::updateOrCreate(
            [
                'school_id' => $school->id,
                'name'      => '2025/2026',
                'semester'  => 'even'
            ],
            [
                'start_date' => '2026-01-05',
                'end_date'   => '2026-06-26',
                'is_active'  => false
            ]
        );

        $this->command->info('Academic Years 2025/2026 (Odd & Even) seeded successfully.');

        // 4. Create classrooms for 2025/2026 Ganjil & Genap
        $waliKelas = User::where('email', 'walikelas@mail.com')->first();
        $waliKelas2 = User::where('email', 'walikelas2@mail.com')->first();

        // 2025/2026 Ganjil Classrooms
        $classroomGanjil1 = Classroom::updateOrCreate(
            [
                'school_id'        => $school->id,
                'academic_year_id' => $ayGanjil->id,
                'name'             => '2-D',
            ],
            [
                'grade'               => 2,
                'major'               => 'MIPA',
                'room'                => 'R. 101',
                'status'              => 'active',
                'homeroom_teacher_id' => $waliKelas ? $waliKelas->id : null,
                'capacity'            => 30,
            ]
        );

        $classroomGanjil2 = Classroom::updateOrCreate(
            [
                'school_id'        => $school->id,
                'academic_year_id' => $ayGanjil->id,
                'name'             => '2-E',
            ],
            [
                'grade'               => 2,
                'major'               => 'IPS',
                'room'                => 'R. 102',
                'status'              => 'active',
                'homeroom_teacher_id' => $waliKelas2 ? $waliKelas2->id : null,
                'capacity'            => 30,
            ]
        );

        // 2025/2026 Genap Classrooms
        $classroomGenap1 = Classroom::updateOrCreate(
            [
                'school_id'        => $school->id,
                'academic_year_id' => $ayGenap->id,
                'name'             => '2-D',
            ],
            [
                'grade'               => 2,
                'major'               => 'MIPA',
                'room'                => 'R. 101',
                'status'              => 'active',
                'homeroom_teacher_id' => $waliKelas ? $waliKelas->id : null,
                'capacity'            => 30,
            ]
        );

        $classroomGenap2 = Classroom::updateOrCreate(
            [
                'school_id'        => $school->id,
                'academic_year_id' => $ayGenap->id,
                'name'             => '2-E',
            ],
            [
                'grade'               => 2,
                'major'               => 'IPS',
                'room'                => 'R. 102',
                'status'              => 'active',
                'homeroom_teacher_id' => $waliKelas2 ? $waliKelas2->id : null,
                'capacity'            => 30,
            ]
        );

        $this->command->info('Classrooms for Academic Year 2025/2026 (2-D & 2-E) created successfully.');

        // 5. Fetch related data for schedules
        $guru = User::where('email', 'guru@mail.com')->first();
        if (!$guru) {
            $this->command->warn('User guru@mail.com not found. Skipping schedule seeding.');
            return;
        }

        $mtk = Subject::where('school_id', $school->id)->where('code', 'MTK')->first() ?: Subject::where('school_id', $school->id)->first();
        $bin = Subject::where('school_id', $school->id)->where('code', 'BIN')->first() ?: Subject::where('school_id', $school->id)->first();
        
        if (!$mtk || !$bin) {
            $this->command->warn('Subjects (MTK / BIN) not found. Skipping schedule seeding.');
            return;
        }

        $timeSlots = TimeSlot::where('school_id', $school->id)->where('is_break', false)->orderBy('slot_number')->get();
        if ($timeSlots->isEmpty()) {
            $this->command->warn('No time slots found. Running TimeSlotSeeder first is recommended.');
            return;
        }

        $slot1 = $timeSlots->firstWhere('slot_number', 1);
        $slot2 = $timeSlots->firstWhere('slot_number', 2);
        $slot3 = $timeSlots->firstWhere('slot_number', 3);
        $slot4 = $timeSlots->firstWhere('slot_number', 5); // slot 4 is break, 5 is 4th study period

        if (!$slot1 || !$slot2 || !$slot3) {
            $this->command->warn('Study time slots not found. Skipping schedule seeding.');
            return;
        }

        // 6. Seed Ganjil (Odd) schedules for guru@mail.com in Classroom 2-D
        // Monday (1)
        Schedule::updateOrCreate(
            [
                'academic_year_id' => $ayGanjil->id,
                'classroom_id'     => $classroomGanjil1->id,
                'time_slot_id'     => $slot1->id,
                'day_of_week'      => 1
            ],
            [
                'school_id'  => $school->id,
                'subject_id' => $mtk->id,
                'teacher_id' => $guru->id
            ]
        );
        Schedule::updateOrCreate(
            [
                'academic_year_id' => $ayGanjil->id,
                'classroom_id'     => $classroomGanjil1->id,
                'time_slot_id'     => $slot2->id,
                'day_of_week'      => 1
            ],
            [
                'school_id'  => $school->id,
                'subject_id' => $mtk->id,
                'teacher_id' => $guru->id
            ]
        );

        // Tuesday (2)
        Schedule::updateOrCreate(
            [
                'academic_year_id' => $ayGanjil->id,
                'classroom_id'     => $classroomGanjil1->id,
                'time_slot_id'     => $slot1->id,
                'day_of_week'      => 2
            ],
            [
                'school_id'  => $school->id,
                'subject_id' => $bin->id,
                'teacher_id' => $guru->id
            ]
        );
        Schedule::updateOrCreate(
            [
                'academic_year_id' => $ayGanjil->id,
                'classroom_id'     => $classroomGanjil1->id,
                'time_slot_id'     => $slot2->id,
                'day_of_week'      => 2
            ],
            [
                'school_id'  => $school->id,
                'subject_id' => $bin->id,
                'teacher_id' => $guru->id
            ]
        );

        // 7. Seed Genap (Even) schedules for guru@mail.com in Classroom 2-D
        // Monday (1)
        Schedule::updateOrCreate(
            [
                'academic_year_id' => $ayGenap->id,
                'classroom_id'     => $classroomGenap1->id,
                'time_slot_id'     => $slot1->id,
                'day_of_week'      => 1
            ],
            [
                'school_id'  => $school->id,
                'subject_id' => $mtk->id,
                'teacher_id' => $guru->id
            ]
        );
        Schedule::updateOrCreate(
            [
                'academic_year_id' => $ayGenap->id,
                'classroom_id'     => $classroomGenap1->id,
                'time_slot_id'     => $slot2->id,
                'day_of_week'      => 1
            ],
            [
                'school_id'  => $school->id,
                'subject_id' => $mtk->id,
                'teacher_id' => $guru->id
            ]
        );

        $this->command->info('Jadwal Pelajaran guru@mail.com for Classroom 2-D (2025/2026 Ganjil & Genap) seeded successfully.');

        // 8. Assign seeded students to the new classrooms of 2025/2026 Ganjil
        $students = User::whereHas('role', function ($q) {
            $q->where('name', 'siswa');
        })->get();

        foreach ($students as $idx => $s) {
            if ($s->studentProfile) {
                // Alternating between 2-D and 2-E
                $targetClass = ($idx % 2 === 0) ? $classroomGanjil1 : $classroomGanjil2;
                $s->studentProfile->update([
                    'classroom_id' => $targetClass->id
                ]);
            }
        }

        $this->command->info('Updated student classroom assignments to 2025/2026 academic year classrooms.');
    }
}
