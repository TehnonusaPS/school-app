<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    protected $fillable = [
        'school_id',
        'academic_year_id',
        'name',
        'grade',
        'homeroom_teacher_id',
        'capacity',
    ];

    protected function casts(): array
    {
        return [
            'grade'    => 'integer',
            'capacity' => 'integer',
        ];
    }

    /**
     * Get the school this classroom belongs to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the academic year this classroom belongs to.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the homeroom teacher (wali kelas) for this classroom.
     */
    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    /**
     * Get all students in this classroom.
     */
    public function students(): HasMany
    {
        return $this->hasMany(StudentProfile::class);
    }
}
