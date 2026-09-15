<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class StockTransfer extends Model
{
    use Auditable, HasFactory, TenantScope;

    protected $fillable = [
        'tenant_id',
        'sucursal_origen_id',
        'sucursal_destino_id',
        'codigo',
        'estado',
        'notas',
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($transfer) {
            if (empty($transfer->codigo)) {
                $transfer->codigo = 'ST-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    public function sucursal_origen(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_origen_id');
    }

    public function sucursal_destino(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_destino_id');
    }

    public function detalle(): HasMany
    {
        return $this->hasMany(StockTransferDetail::class, 'transfer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'borrador' => 'Borrador',
            'aprobada' => 'Aprobada',
            'enviada' => 'Enviada',
            'recibida' => 'Recibida',
            'cancelada' => 'Cancelada',
        ];
        return $labels[$this->estado ?? 'borrador'] ?? 'Borrador';
    }

    public function getEstadoBadgeColorAttribute(): string
    {
        $colors = [
            'borrador' => 'secondary',
            'aprobada' => 'warning',
            'enviada' => 'info',
            'recibida' => 'success',
            'cancelada' => 'danger',
        ];
        return $colors[$this->estado ?? 'borrador'] ?? 'secondary';
    }
}
