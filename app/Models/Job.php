<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use SoftDeletes;

    protected $table = 'service_jobs';

    protected $fillable = [
        'customer_id',
        'quote_estimate_number',
        'job_number',
        'invoice_number',
        'po_number',
        'job_type',
        'status',
        'confirmed_date',
        'booked_date',
        'site_full_name',
        'site_telephone',
        'site_business',
        'site_title',
        'site_address',
        'site_city',
        'site_state',
        'site_postal_code',
        'billing_full_name',
        'billing_telephone',
        'billing_business',
        'billing_title',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'crew_route',
        'scheduled_day',
        'scheduled_date',
        'scheduled_time',
        'best_time',
        'source',
        'source_percent',
        'source_amount',
        'agent',
        'agent_percent',
        'agent_amount',
        'subtotal',
        'tax',
        'other_tax',
        'total',
        'notes',
        'invoice_total',
        'amount_paid',
        'balance',
        'payment_method',
        'payment_reference',
        'receipt_no',
        'transaction_date',
    ];

    protected $casts = [
        'confirmed_date' => 'date',
        'booked_date' => 'date',
        'scheduled_date' => 'date',
        'transaction_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(JobLineItem::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(JobNote::class);
    }

    public function technicians(): HasMany
    {
        return $this->hasMany(JobTechnician::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(JobPayment::class)->orderBy('transaction_date')->orderBy('id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'deleted');
    }
}
