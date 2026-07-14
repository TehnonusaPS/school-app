<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPaymentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_payment_id',
        'student_bill_id',
        'amount_paid',
    ];

    public function studentPayment(): BelongsTo
    {
        return $this->belongsTo(StudentPayment::class);
    }

    public function studentBill(): BelongsTo
    {
        return $this->belongsTo(StudentBill::class);
    }
}
