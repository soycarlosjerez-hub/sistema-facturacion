<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstanceErrorLog extends Model
{
    protected $fillable = [
        'tenant_id',
        'level',
        'title',
        'message',
        'context',
        'source',
        'user_id',
        'file',
        'line',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'context' => 'array',
        'line' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(BusinessInstance::class, 'tenant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOfTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeOfLevel(Builder $query, string $level): Builder
    {
        return $query->where('level', $level);
    }

    public function scopeOfSource(Builder $query, string $source): Builder
    {
        return $query->where('source', $source);
    }

    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function getLevelColorAttribute(): string
    {
        return match ($this->level) {
            'critical' => 'danger',
            'error' => 'danger',
            'warning' => 'warning',
            'info' => 'info',
            default => 'secondary',
        };
    }

    public function getSourceIconAttribute(): string
    {
        return match ($this->source) {
            'exception' => 'bi-bug-fill',
            'log' => 'bi-terminal',
            'ecf' => 'bi-file-earmark-text',
            'dgii' => 'bi-cloud',
            'print' => 'bi-printer',
            'email' => 'bi-envelope',
            'validation' => 'bi-exclamation-triangle',
            default => 'bi-info-circle',
        };
    }
}
