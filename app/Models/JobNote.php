<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobNote extends Model
{
    protected $table = 'job_notes';

    protected $fillable = ['job_id', 'note'];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
