<?php

namespace App\Services;

use App\Models\AcademicCalendarEvent;
use App\Models\AcademicYear;
use App\Http\Traits\AcademicCalendarHelperTrait;
use Illuminate\Support\Facades\DB;

class AcademicCalendarService
{
    use AcademicCalendarHelperTrait;

    /**
     * Setup academic year dates (Odd & Even semesters).
     */
    public function setupYearDates(int $schoolId, string $oddStart, string $oddEnd, string $evenStart, string $evenEnd): array
    {
        $yearResult = $this->generateAcademicYearFromDates(
            $schoolId,
            $oddStart,
            $oddEnd,
            $evenStart,
            $evenEnd,
            true
        );

        $bufferBounds = $this->calculateCalendarBufferRange($oddStart, $evenEnd);

        return [
            'yearResult'   => $yearResult,
            'bufferBounds' => $bufferBounds,
        ];
    }

    /**
     * Batch store events for an academic year atomically using DB transaction.
     */
    public function batchStoreEvents(int $schoolId, int $academicYearId, array $eventsData, int $userId): void
    {
        $academicYear = AcademicYear::where('id', $academicYearId)
            ->where('school_id', $schoolId)
            ->firstOrFail();

        if ($academicYear->calendar_status === 'pending') {
            throw new \Exception('Kalender akademik sedang dalam pengajuan persetujuan Kepala Sekolah dan tidak dapat diubah.', 422);
        }

        $ayIds = AcademicYear::where('school_id', $schoolId)
            ->where('name', $academicYear->name)
            ->pluck('id');

        DB::transaction(function() use ($schoolId, $academicYearId, $ayIds, $eventsData, $userId) {
            // Delete all existing events for this academic year to avoid duplicate entries
            AcademicCalendarEvent::whereIn('academic_year_id', $ayIds)
                ->where('school_id', $schoolId)
                ->delete();

            foreach ($eventsData as $item) {
                AcademicCalendarEvent::create([
                    'school_id'        => $schoolId,
                    'academic_year_id' => $academicYearId,
                    'classroom_id'     => isset($item['classroom_id']) && $item['classroom_id'] !== 'all' ? $item['classroom_id'] : null,
                    'title'            => $item['title'],
                    'start_date'       => $item['startDate'] ?? $item['start_date'],
                    'end_date'         => $item['endDate'] ?? $item['end_date'],
                    'type'             => $item['type'],
                    'description'      => $item['description'] ?? null,
                    'created_by'       => $userId,
                ]);
            }
        });
    }

    /**
     * Submit calendar for Kepsek review.
     */
    public function submitCalendar(int $schoolId, int $academicYearId): AcademicYear
    {
        $academicYear = AcademicYear::where('id', $academicYearId)
            ->where('school_id', $schoolId)
            ->firstOrFail();

        $ayIds = AcademicYear::where('school_id', $schoolId)
            ->where('name', $academicYear->name)
            ->pluck('id');

        AcademicYear::whereIn('id', $ayIds)->update([
            'calendar_status' => 'pending',
            'calendar_rejected_reason' => null
        ]);

        $academicYear->refresh();
        return $academicYear;
    }

    /**
     * Approve calendar.
     */
    public function approveCalendar(int $schoolId, int $academicYearId): AcademicYear
    {
        $academicYear = AcademicYear::where('id', $academicYearId)
            ->where('school_id', $schoolId)
            ->firstOrFail();

        $ayIds = AcademicYear::where('school_id', $schoolId)
            ->where('name', $academicYear->name)
            ->pluck('id');

        AcademicYear::whereIn('id', $ayIds)->update([
            'calendar_status' => 'approved',
            'calendar_rejected_reason' => null
        ]);

        $academicYear->refresh();
        return $academicYear;
    }

    /**
     * Reject calendar.
     */
    public function rejectCalendar(int $schoolId, int $academicYearId, string $reason): AcademicYear
    {
        $academicYear = AcademicYear::where('id', $academicYearId)
            ->where('school_id', $schoolId)
            ->firstOrFail();

        $ayIds = AcademicYear::where('school_id', $schoolId)
            ->where('name', $academicYear->name)
            ->pluck('id');

        AcademicYear::whereIn('id', $ayIds)->update([
            'calendar_status' => 'rejected',
            'calendar_rejected_reason' => $reason
        ]);

        $academicYear->refresh();
        return $academicYear;
    }

    /**
     * Completely reset/delete academic calendar and associated events.
     */
    public function resetCalendar(int $schoolId, int $academicYearId): void
    {
        $academicYear = AcademicYear::where('id', $academicYearId)
            ->where('school_id', $schoolId)
            ->firstOrFail();

        $ayIds = AcademicYear::where('school_id', $schoolId)
            ->where('name', $academicYear->name)
            ->pluck('id');

        DB::transaction(function() use ($schoolId, $ayIds) {
            AcademicCalendarEvent::whereIn('academic_year_id', $ayIds)
                ->where('school_id', $schoolId)
                ->delete();

            AcademicYear::whereIn('id', $ayIds)
                ->where('school_id', $schoolId)
                ->whereDoesntHave('schedules')
                ->whereDoesntHave('classrooms')
                ->delete();
        });
    }
}
