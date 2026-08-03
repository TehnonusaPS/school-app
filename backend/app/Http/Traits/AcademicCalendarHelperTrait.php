<?php

namespace App\Http\Traits;

use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait AcademicCalendarHelperTrait
{
    /**
     * Create or update academic year record from semester start & end dates.
     */
    public function generateAcademicYearFromDates(
        int $schoolId,
        string $oddStart,
        string $oddEnd,
        string $evenStart,
        string $evenEnd,
        bool $setActive = true
    ): array {
        $startYear = Carbon::parse($oddStart)->year;
        $endYear = Carbon::parse($evenEnd)->year;

        if ($startYear === $endYear) {
            $endYear = $startYear + 1;
        }

        $yearName = "{$startYear}/{$endYear}";

        DB::transaction(function () use ($schoolId, $yearName, $oddStart, $oddEnd, $evenStart, $evenEnd, $setActive) {
            if ($setActive) {
                AcademicYear::where('school_id', $schoolId)->update(['is_active' => false]);
            }

            // Create or update Odd semester
            AcademicYear::updateOrCreate(
                [
                    'school_id' => $schoolId,
                    'name'      => $yearName,
                    'semester'  => 'odd',
                ],
                [
                    'start_date'      => $oddStart,
                    'end_date'        => $oddEnd,
                    'is_active'       => $setActive,
                    'calendar_status' => 'draft',
                ]
            );

            // Create or update Even semester
            AcademicYear::updateOrCreate(
                [
                    'school_id' => $schoolId,
                    'name'      => $yearName,
                    'semester'  => 'even',
                ],
                [
                    'start_date'      => $evenStart,
                    'end_date'        => $evenEnd,
                    'is_active'       => false,
                    'calendar_status' => 'draft',
                ]
            );
        });

        $years = AcademicYear::where('school_id', $schoolId)->where('name', $yearName)->get();

        return [
            'name'  => $yearName,
            'years' => $years,
        ];
    }

    /**
     * Calculate month buffer range (-1 month before oddStart, +1 month after evenEnd).
     */
    public function calculateCalendarBufferRange(string $oddStart, string $evenEnd): array
    {
        $startDate = Carbon::parse($oddStart)->subMonth()->startOfMonth();
        $endDate = Carbon::parse($evenEnd)->addMonth()->endOfMonth();

        return [
            'display_start' => $startDate->format('Y-m-d'),
            'display_end'   => $endDate->format('Y-m-d'),
            'start_month'   => $startDate->format('F Y'),
            'end_month'     => $endDate->format('F Y'),
        ];
    }
}
