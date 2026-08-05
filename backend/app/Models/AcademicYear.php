<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $fillable = [
        'school_id',
        'name',
        'semester',
        'start_date',
        'end_date',
        'is_active',
        'calendar_status',
        'calendar_rejected_reason',
        'calendar_submitted_at',
        'calendar_reviewed_at',
        'calendar_reviewed_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
            'is_active'  => 'boolean',
            'calendar_submitted_at' => 'datetime',
            'calendar_reviewed_at'  => 'datetime',
            'calendar_reviewed_by'  => 'integer',
        ];
    }

    /**
     * Get the school this academic year belongs to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get all classrooms in this academic year.
     */
    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    /**
     * Get all schedules in this academic year.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Get all calendar events in this academic year.
     */
    public function calendarEvents(): HasMany
    {
        return $this->hasMany(AcademicCalendarEvent::class);
    }

    /**
     * Get the user who reviewed this calendar.
     */
    public function calendarReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calendar_reviewed_by');
    }
}
