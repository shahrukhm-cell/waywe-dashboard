<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobTechnician extends Model
{
    protected $fillable = ['job_id', 'technician_name', 'time', 'amount'];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
