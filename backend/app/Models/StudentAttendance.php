<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_profile_id',
        'date',
        'time_in',
        'time_out',
        'status', // 'H', 'T', 'I', 'S', 'A'
        'verification_method', // 'kamera', 'rfid', 'fingerprint'
        'photo_path',
    ];

    /**
     * Get the student profile associated with this attendance record.
     */
    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }
}
