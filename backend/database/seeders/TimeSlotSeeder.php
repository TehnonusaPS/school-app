<?php

namespace Database\Seeders;

use App\Models\TimeSlot;
use App\Models\School;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $school = School::first();
        if (!$school) {
            return;
        }

        $slots = [
            ['slot_number' => 1, 'start_time' => '07:30:00', 'end_time' => '08:10:00', 'is_break' => false, 'label' => 'Jam 1'],
            ['slot_number' => 2, 'start_time' => '08:10:00', 'end_time' => '08:50:00', 'is_break' => false, 'label' => 'Jam 2'],
            ['slot_number' => 3, 'start_time' => '08:50:00', 'end_time' => '09:30:00', 'is_break' => false, 'label' => 'Jam 3'],
            ['slot_number' => 4, 'start_time' => '09:30:00', 'end_time' => '10:00:00', 'is_break' => true, 'label' => 'Istirahat 1'],
            ['slot_number' => 5, 'start_time' => '10:00:00', 'end_time' => '10:40:00', 'is_break' => false, 'label' => 'Jam 4'],
            ['slot_number' => 6, 'start_time' => '10:40:00', 'end_time' => '11:20:00', 'is_break' => false, 'label' => 'Jam 5'],
            ['slot_number' => 7, 'start_time' => '11:20:00', 'end_time' => '12:00:00', 'is_break' => false, 'label' => 'Jam 6'],
            ['slot_number' => 8, 'start_time' => '12:00:00', 'end_time' => '12:30:00', 'is_break' => true, 'label' => 'Istirahat 2'],
            ['slot_number' => 9, 'start_time' => '12:30:00', 'end_time' => '13:10:00', 'is_break' => false, 'label' => 'Jam 7'],
            ['slot_number' => 10, 'start_time' => '13:10:00', 'end_time' => '13:50:00', 'is_break' => false, 'label' => 'Jam 8'],
        ];

        foreach ($slots as $slot) {
            TimeSlot::updateOrCreate(
                [
                    'school_id'   => $school->id,
                    'slot_number' => $slot['slot_number']
                ],
                [
                    'start_time' => $slot['start_time'],
                    'end_time'   => $slot['end_time'],
                    'is_break'   => $slot['is_break'],
                    'label'      => $slot['label']
                ]
            );
        }
    }
}
