<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeSlot extends Model
{
    protected $fillable = [
        'school_id',
        'slot_number',
        'start_time',
        'end_time',
        'is_break',
        'label',
    ];

    protected function casts(): array
    {
        return [
            'slot_number' => 'integer',
            'is_break'    => 'boolean',
        ];
    }

    /**
     * Get the school this time slot belongs to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the schedules assigned to this time slot.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
