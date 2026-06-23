<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Foundation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'established_date',
        'status',
        'address',
        'email',
        'phone',
        'website',
        'deed_number',
        'deed_date',
        'decree_number',
        'decree_date',
        'logo',
    ];

    protected function casts(): array
    {
        return [
            'established_date' => 'date',
            'deed_date'        => 'date',
            'decree_date'      => 'date',
        ];
    }

    /**
     * Get all schools under this foundation.
     */
    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }

    /**
     * Get all users assigned to this foundation (e.g. admin_yayasan).
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
