<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tenant_id',
        'actor_id',
        'actor_name',
        'actor_avatar',
        'action',
        'type',
        'category',
        'title',
        'body',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(BusinessInstance::class, 'tenant_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeForInstance($query, ?int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public static function createNotification(
        int $userId,
        string $type,
        string $title,
        string $body,
        string $category = 'system',
        array $extraData = [],
        ?int $tenantId = null,
        ?int $actorId = null,
        ?string $actorName = null
    ): self {
        $defaults = [
            'icon' => 'bi-bell',
            'color' => '#3b82f6',
            'action_url' => null,
            'category_icon' => 'bi-bell',
            'category_label' => ucfirst($category),
            'verb' => null,
        ];

        return static::create([
            'user_id' => $userId,
            'tenant_id' => $tenantId,
            'actor_id' => $actorId,
            'actor_name' => $actorName,
            'action' => $extraData['verb'] ?? null,
            'type' => $type,
            'category' => $category,
            'title' => $title,
            'body' => $body,
            'data' => array_merge($defaults, $extraData),
        ]);
    }
}
