<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoundationPayment extends Model
{
    protected $fillable = [
        'foundation_id',
        'subscription_plan_id',
        'invoice_number',
        'amount',
        'status',
        'payment_method',
        'payment_date',
        'payment_receipt',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'datetime',
            'amount'       => 'decimal:2',
        ];
    }

    /**
     * Get the foundation associated with this payment.
     */
    public function foundation(): BelongsTo
    {
        return $this->belongsTo(Foundation::class);
    }

    /**
     * Get the subscription plan associated with this payment.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
