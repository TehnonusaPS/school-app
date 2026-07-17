<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudentBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'spp_tariff_id',
        'title',
        'amount',
        'paid_amount',
        'due_date',
        'status',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function sppTariff(): BelongsTo
    {
        return $this->belongsTo(SppTariff::class);
    }

    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(StudentPayment::class, 'student_payment_items')
                    ->withPivot('amount_paid')
                    ->withTimestamps();
    }
}
