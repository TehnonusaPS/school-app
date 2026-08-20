<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamSessionSubject extends Model
{
    protected $fillable = [
        'exam_session_id',
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
     * Exam Session this subject belongs to.
     */
    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    /**
     * Subject model.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
