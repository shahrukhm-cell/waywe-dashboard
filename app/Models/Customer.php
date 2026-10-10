<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'business',
        'title',
        'address',
        'city',
        'state',
        'postal_code',
    ];

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')) ?: 'Unknown Customer';
    }
}
