<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curriculum extends Model
{
    protected $table = 'curriculums';

    protected $fillable = [
        'code',
        'name',
        'level',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get subjects configured for this curriculum.
     */
    public function curriculumSubjects(): HasMany
    {
        return $this->hasMany(CurriculumSubject::class, 'curriculum_id')->orderBy('sort_order');
    }

    /**
     * Get foundations associated with this curriculum.
     */
    public function foundations(): HasMany
    {
        return $this->hasMany(Foundation::class);
    }

    /**
     * Get schools associated with this curriculum.
     */
    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }
}
