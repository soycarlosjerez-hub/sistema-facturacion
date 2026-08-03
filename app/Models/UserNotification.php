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

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
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
        array $extraData = []
    ): self {
        $defaults = [
            'icon' => 'bi-bell',
            'color' => '#3b82f6',
            'action_url' => null,
            'category_icon' => 'bi-bell',
            'category_label' => ucfirst($category),
        ];

        return static::create(array_merge([
            'user_id' => $userId,
            'type' => $type,
            'category' => $category,
            'title' => $title,
            'body' => $body,
            'data' => array_merge($defaults, $extraData),
        ], $extraData));
    }
}
