<?php

namespace App\Http\Requests\AcademicCalendar;

use Illuminate\Foundation\Http\FormRequest;

class SetupYearDatesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'odd_start_date'  => 'required|date',
            'odd_end_date'    => 'required|date|after_or_equal:odd_start_date',
            'even_start_date' => 'required|date|after_or_equal:odd_end_date',
            'even_end_date'   => 'required|date|after_or_equal:even_start_date',
        ];
    }
}
