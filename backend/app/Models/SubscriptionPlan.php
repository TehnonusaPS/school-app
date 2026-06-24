<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'code',
        'price',
        'billing_cycle',
        'features',
        'max_schools',
        'max_students',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'features'     => 'array',
            'price'        => 'decimal:2',
            'max_schools'  => 'integer',
            'max_students' => 'integer',
            'is_active'    => 'boolean',
        ];
    }

    /**
     * Get subscriptions associated with this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(FoundationSubscription::class);
    }

    /**
     * Get payments associated with this plan.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(FoundationPayment::class);
    }
}
