<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstanceApiKey extends Model
{
    protected $fillable = [
        'business_instance_id',
        'name',
        'key',
        'last_used_at',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function instance(): BelongsTo
    {
        return $this->belongsTo(BusinessInstance::class, 'business_instance_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function mask(): string
    {
        $len = strlen($this->key);
        if ($len <= 12) {
            return substr($this->key, 0, 4) . str_repeat('*', $len - 4);
        }
        return substr($this->key, 0, 8) . str_repeat('*', $len - 12) . substr($this->key, -4);
    }
}
