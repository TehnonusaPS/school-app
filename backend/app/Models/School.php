<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'foundation_id',
        'name',
        'npsn',
        'level',
        'established_date',
        'status',
        'address',
        'email',
        'phone',
        'website',
        'instagram',
        'facebook',
        'decree_number',
        'decree_date',
        'permit_number',
        'permit_date',
        'accreditation',
        'accreditation_date',
        'accreditation_number',
        'logo',
    ];

    protected function casts(): array
    {
        return [
            'established_date'    => 'date',
            'decree_date'         => 'date',
            'permit_date'         => 'date',
            'accreditation_date'  => 'date',
        ];
    }

    /**
     * Get the foundation this school belongs to.
     */
    public function foundation(): BelongsTo
    {
        return $this->belongsTo(Foundation::class);
    }

    /**
     * Get all users assigned to this school.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all academic years for this school.
     */
    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    /**
     * Get all classrooms in this school.
     */
    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    /**
     * Get all subjects in this school.
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
}
