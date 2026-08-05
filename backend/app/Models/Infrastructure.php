<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Infrastructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'type',
        'name',
        'quantity',
        'condition',
        'year_acquired',
        'location',
        'notes',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
