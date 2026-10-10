<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPayment extends Model
{
    protected $fillable = [
        'job_id',
        'receipt_no',
        'payment_method',
        'amount',
        'transaction_date',
        'check_number',
        'check_date',
        'account_reference',
        'card_last_four',
        'card_expiry_month',
        'card_expiry_year',
        'explanation',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'check_date' => 'date',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
