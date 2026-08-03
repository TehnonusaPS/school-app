<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentDispensationCertificate extends Model
{
    protected $fillable = [
        'foundation_id',
        'school_id',
        'tanggal_dibuat',
        'tanggal_awal',
        'tanggal_akhir',
        'perihal',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_dibuat' => 'date',
            'tanggal_awal'   => 'date',
            'tanggal_akhir'  => 'date',
        ];
    }

    /**
     * Get the student records associated with this dispensation.
     */
    public function students(): HasMany
    {
        return $this->hasMany(StudentDispensationStudent::class, 'dispensation_id');
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
