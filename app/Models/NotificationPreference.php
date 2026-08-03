<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $table = 'notification_preferences';

    protected $fillable = [
        'user_id',
        'business_instance_id',
        'sale_created',
        'sale_paid',
        'sale_cancelled',
        'order_confirmed',
        'order_ready',
        'order_shipped',
        'payment_received',
        'credit_overdue',
        'credit_abono',
        'stock_critical',
        'stock_restocked',
        'product_created',
        'shift_opened',
        'shift_closed',
        'cash_shortage',
        'daily_report',
        'ncff_expiring',
        'ecf_certificate_expiring',
        'backup_completed',
        'backup_failed',
        'user_registered',
    ];

    protected $casts = [
        'sale_created' => 'boolean',
        'sale_paid' => 'boolean',
        'sale_cancelled' => 'boolean',
        'order_confirmed' => 'boolean',
        'order_ready' => 'boolean',
        'order_shipped' => 'boolean',
        'payment_received' => 'boolean',
        'credit_overdue' => 'boolean',
        'credit_abono' => 'boolean',
        'stock_critical' => 'boolean',
        'stock_restocked' => 'boolean',
        'product_created' => 'boolean',
        'shift_opened' => 'boolean',
        'shift_closed' => 'boolean',
        'cash_shortage' => 'boolean',
        'daily_report' => 'boolean',
        'ncff_expiring' => 'boolean',
        'ecf_certificate_expiring' => 'boolean',
        'backup_completed' => 'boolean',
        'backup_failed' => 'boolean',
        'user_registered' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function businessInstance(): BelongsTo
    {
        return $this->belongsTo(BusinessInstance::class);
    }

    public static function forUser(User $user): self
    {
        return static::firstOrCreate(
            ['user_id' => $user->id, 'business_instance_id' => $user->business_instance_id],
            static::defaultPreferences()
        );
    }

    public static function defaultPreferences(): array
    {
        return [
            'sale_created' => true,
            'sale_paid' => true,
            'sale_cancelled' => true,
            'order_confirmed' => true,
            'order_ready' => true,
            'order_shipped' => true,
            'payment_received' => true,
            'credit_overdue' => true,
            'credit_abono' => true,
            'stock_critical' => true,
            'stock_restocked' => false,
            'product_created' => false,
            'shift_opened' => true,
            'shift_closed' => true,
            'cash_shortage' => true,
            'daily_report' => false,
            'ncff_expiring' => true,
            'ecf_certificate_expiring' => true,
            'backup_completed' => false,
            'backup_failed' => true,
            'user_registered' => true,
        ];
    }

    public function isEnabled(string $key): bool
    {
        return $this->{$key} ?? false;
    }
}
