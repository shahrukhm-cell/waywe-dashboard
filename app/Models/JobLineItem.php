<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobLineItem extends Model
{
    protected $table = 'job_line_items';

    protected $fillable = [
        'job_id',
        'area_item',
        'length',
        'width',
        'description',
        'unit',
        'quantity',
        'time',
        'amount',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
