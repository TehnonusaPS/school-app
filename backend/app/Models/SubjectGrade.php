<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectGrade extends Model
{
    protected $fillable = [
        'subject_id',
        'grade',
    ];

    protected function casts(): array
    {
        return [
            'grade' => 'integer',
        ];
    }

    /**
     * Get the subject this grade belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
