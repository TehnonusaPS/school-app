<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherProfile extends Model
{
    protected $fillable = [
        'user_id',
        'nik',
        'nip_nuptk',
        'birth_place',
        'birth_date',
        'gender',
        'religion',
        'marital_status',
        'last_education',
        'front_title',
        'back_title',
        'address',
        'position',
        'employment_status',
        'join_date',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'join_date'  => 'date',
        ];
    }

    /**
     * Get the user account for this teacher profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
