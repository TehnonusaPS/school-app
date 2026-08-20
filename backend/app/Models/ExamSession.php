<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSession extends Model
{
    protected $fillable = [
        'school_id',
        'academic_calendar_event_id',
        'exam_date',
        'session_number',
        'start_time',
        'end_time',
        'notes',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'exam_date'      => 'date:Y-m-d',
            'session_number' => 'integer',
        ];
    }

    /**
     * Scope query to only include published exam sessions.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }

    /**
     * School this session belongs to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Academic Calendar Event this session belongs to.
     */
    public function calendarEvent(): BelongsTo
    {
        return $this->belongsTo(AcademicCalendarEvent::class, 'academic_calendar_event_id');
    }

    /**
     * User who created this session.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Subjects assigned to this session across grades.
     */
    public function sessionSubjects(): HasMany
    {
        return $this->hasMany(ExamSessionSubject::class, 'exam_session_id');
    }
}
