<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'classroom_id',
        'nisn',
        'birth_place',
        'birth_date',
        'gender',
        'address',
        'enrollment_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date'      => 'date',
            'enrollment_date' => 'date',
        ];
    }

    /**
     * Get the user account for this student profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the classroom this student is assigned to.
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Get all parents/guardians of this student.
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(
            ParentProfile::class,
            'parent_student',
            'student_profile_id',
            'parent_profile_id'
        )->withTimestamps();
    }
}
