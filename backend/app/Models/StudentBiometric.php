<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentBiometric extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_profile_id',
        'embedding',
        'angle_label',
        'face_photo_path',
    ];

    protected $casts = [
        'embedding' => 'array',
    ];

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }
}
