<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActiveStudentCertificate extends Model
{
    protected $fillable = [
        'foundation_id',
        'school_id',
        'student_id',
        'academic_year_id',
        'semester',
        'nama',
        'nisn',
        'kelas',
        'tanggal_lahir',
        'alamat',
        'status',
        'tanggal_dibuat',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir'  => 'date',
            'tanggal_dibuat' => 'date',
        ];
    }

    /**
     * Get the student (user account) for this certificate.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the academic year for this certificate.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    /**
     * Get the school this certificate belongs to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the foundation this certificate belongs to.
     */
    public function foundation(): BelongsTo
    {
        return $this->belongsTo(Foundation::class);
    }
}
