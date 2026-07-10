<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDispensationStudent extends Model
{
    protected $fillable = [
        'dispensation_id',
        'student_id',
        'nama',
        'nisn',
        'kelas',
    ];

    /**
     * Get the dispensation certificate this record belongs to.
     */
    public function certificate(): BelongsTo
    {
        return $this->belongsTo(StudentDispensationCertificate::class, 'dispensation_id');
    }

    /**
     * Get the student (user account) for this record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
