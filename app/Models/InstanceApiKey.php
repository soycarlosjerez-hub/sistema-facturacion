<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstanceApiKey extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_instance_id',
        'name',
        'key',
        'key_raw',
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

    public function scopeSearch($query, ?string $search): \Illuminate\Database\Eloquent\Builder
    {
        if (blank($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhereHas('creator', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        });
    }

    public function scopeByStatus($query, ?string $status): \Illuminate\Database\Eloquent\Builder
    {
        return match ($status) {
            'active' => $query->where('is_active', true),
            'inactive' => $query->where('is_active', false),
            default => $query,
        };
    }

    public function mask(): string
    {
        if (empty($this->key_raw)) {
            return $this->key ? substr($this->key, 0, 8).'****'.substr($this->key, -4) : '';
        }

        $key = $this->key_raw;
        $len = strlen($key);
        if ($len <= 12) {
            return substr($key, 0, 4).str_repeat('*', $len - 4);
        }

        return substr($key, 0, 8).str_repeat('*', $len - 12).substr($key, -4);
    }

    public function getDisplayKeyAttribute(): ?string
    {
        if (! empty($this->key_raw)) {
            return $this->key_raw;
        }

        if (! empty($this->key)) {
            return $this->key;
        }

        return null;
    }

    public function resolveRouteBinding($value, $field = null): ?static
    {
        return $this->withTrashed()->where($field ?? 'id', $value)->first();
    }
}
