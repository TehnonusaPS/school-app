<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectMaterialRequest extends FormRequest
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
            'title'            => 'required|string|max:255',
            'file'             => 'required|file|mimes:pdf,ppt,pptx|max:15360', // max 15MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'file.max'   => 'Ukuran file tidak boleh lebih dari 15MB.',
            'file.mimes' => 'File harus berformat PDF, PPT, atau PPTX.',
            'title.required' => 'Nama materi wajib diisi.',
        ];
    }
}
