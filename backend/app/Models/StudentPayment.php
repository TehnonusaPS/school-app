<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'payment_method',
        'amount',
        'reference_number',
        'status',
        'payment_date',
        'verified_by',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function bills(): BelongsToMany
    {
        return $this->belongsToMany(StudentBill::class, 'student_payment_items')
                    ->withPivot('amount_paid')
                    ->withTimestamps();
    }
}
