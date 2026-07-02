<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'foundation_id',
        'created_by',
        'title',
        'content',
        'category',
        'target_school_id',
        'publish_date',
    ];

    protected $casts = [
        'publish_date' => 'date',
    ];

    /**
     * Get the foundation that owns the announcement.
     */
    public function foundation(): BelongsTo
    {
        return $this->belongsTo(Foundation::class);
    }

    /**
     * Get the target school for the announcement.
     */
    public function targetSchool(): BelongsTo
    {
        return $this->belongsTo(School::class, 'target_school_id');
    }

    /**
     * Get the user who created the announcement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
