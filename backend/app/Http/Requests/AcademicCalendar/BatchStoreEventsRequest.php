<?php

namespace App\Http\Requests\AcademicCalendar;

use Illuminate\Foundation\Http\FormRequest;

class BatchStoreEventsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_year_id' => 'required|exists:academic_years,id',
            'events'           => 'present|array',
            'events.*.title'   => 'required|string',
            'events.*.startDate' => 'required|date',
            'events.*.endDate'   => 'required|date',
        ];
    }
}
