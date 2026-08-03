<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurriculumSubject extends Model
{
    protected $fillable = [
        'curriculum_id',
        'code',
        'name',
        'level',
        'phase',
        'default_grades',
        'is_mandatory',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'default_grades' => 'array',
            'is_mandatory'   => 'boolean',
            'sort_order'     => 'integer',
        ];
    }

    /**
     * Get the curriculum this subject belongs to.
     */
    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }
}
