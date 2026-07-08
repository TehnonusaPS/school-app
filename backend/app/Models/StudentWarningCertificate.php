<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentWarningCertificate extends Model
{
    protected $fillable = [
        'foundation_id',
        'school_id',
        'student_id',
        'tanggal_dibuat',
        'jenis_surat',
        'nama',
        'nisn',
        'kelas',
        'tanggal',
        'perihal_pelanggaran',
        'jumlah_tunggakan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_dibuat' => 'date',
            'tanggal'        => 'date',
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
