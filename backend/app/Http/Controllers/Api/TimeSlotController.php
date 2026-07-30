<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use App\Models\Schedule;
use App\Http\Traits\HasSchoolScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TimeSlotController extends Controller
{
    use HasSchoolScope;

    /**
     * Display a listing of the time slots.
     */
    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);

        if ($schoolId === -1) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized school access.'], 403);
        }

        $query = TimeSlot::query();
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $slots = $query->orderBy('slot_number')->get()->map(function ($slot) {
            return [
                'id'          => $slot->id,
                'school_id'   => $slot->school_id,
                'slot_number' => (int) $slot->slot_number,
                'start_time'  => $slot->start_time,
                'end_time'    => $slot->end_time,
                'is_break'    => (bool) $slot->is_break,
                'label'       => $slot->label,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => $slots
        ]);
    }

    /**
     * Bulk store or update time slots.
     */
    public function bulkStore(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);

        if (!$schoolId || $schoolId === -1) {
            return response()->json(['status' => 'error', 'message' => 'School ID is required and must be valid.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'slots'               => 'required|array',
            'slots.*.slot_number' => 'required|integer',
            'slots.*.start_time'  => 'required|string',
            'slots.*.end_time'    => 'required|string',
            'slots.*.is_break'    => 'required',
            'slots.*.label'       => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $slotsData = $request->input('slots');

        DB::beginTransaction();
        try {
            $currentSlotNumbers = collect($slotsData)->pluck('slot_number')->toArray();

            // Delete slots not included in request if they don't have schedules
            TimeSlot::where('school_id', $schoolId)
                ->whereNotIn('slot_number', $currentSlotNumbers)
                ->whereDoesntHave('schedules')
                ->delete();

            $upserted = [];
            foreach ($slotsData as $slot) {
                $isBreak = filter_var($slot['is_break'] ?? false, FILTER_VALIDATE_BOOLEAN);

                $timeSlotModel = TimeSlot::updateOrCreate(
                    [
                        'school_id'   => $schoolId,
                        'slot_number' => $slot['slot_number']
                    ],
                    [
                        'start_time' => $slot['start_time'],
                        'end_time'   => $slot['end_time'],
                        'is_break'   => $isBreak,
                        'label'      => $slot['label'] ?? null
                    ]
                );

                if ($isBreak) {
                    // Remove any schedule assigned to this slot if converted to break
                    Schedule::where('school_id', $schoolId)
                        ->where('time_slot_id', $timeSlotModel->id)
                        ->delete();
                }

                $upserted[] = [
                    'id'          => $timeSlotModel->id,
                    'school_id'   => $timeSlotModel->school_id,
                    'slot_number' => (int) $timeSlotModel->slot_number,
                    'start_time'  => $timeSlotModel->start_time,
                    'end_time'    => $timeSlotModel->end_time,
                    'is_break'    => (bool) $timeSlotModel->is_break,
                    'label'       => $timeSlotModel->label,
                ];
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Time slots updated successfully.',
                'data'    => $upserted
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to save time slots: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified time slot.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);
        $slot = TimeSlot::find($id);

        if (!$slot) {
            return response()->json(['status' => 'error', 'message' => 'Time slot not found.'], 404);
        }

        if ($schoolId && $slot->school_id !== $schoolId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
        }

        if ($slot->schedules()->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot delete time slot because it is already used in a schedule.'
            ], 400);
        }

        $slot->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Time slot deleted successfully.'
        ]);
    }
}
