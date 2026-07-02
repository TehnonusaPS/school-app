<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'subject_id'       => 'required|exists:subjects,id',
            'classroom_id'     => 'required|exists:classrooms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'category'         => 'required|in:tugas,ujian',
            'type'             => 'required|in:tugas_sekolah,tugas_rumah,ujian_harian,uts,uas',
            'title'            => 'required|string|max:255',
            'scores'           => 'required|array|min:1',
            'scores.*.student_id' => 'required|exists:student_profiles,id',
            'scores.*.score'      => 'required|numeric|min:0|max:100',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $category = $this->input('category');
            $type = $this->input('type');

            $validCombinations = [
                'tugas' => ['tugas_sekolah', 'tugas_rumah'],
                'ujian' => ['ujian_harian', 'uts', 'uas'],
            ];

            if ($category && $type && isset($validCombinations[$category])) {
                if (!in_array($type, $validCombinations[$category])) {
                    $validator->errors()->add('type', "Tipe '{$type}' tidak valid untuk kategori '{$category}'.");
                }
            }
        });
    }
}
