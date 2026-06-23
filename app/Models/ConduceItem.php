<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\TenantScope;

class ConduceItem extends Model
{
    use TenantScope;

    protected $fillable = [
        'conduce_id', 'producto_id', 'codigo', 'nombre', 'unidad',
        'descripcion', 'cantidad', 'cantidad_recibida', 'peso', 'orden', 'tenant_id',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'cantidad_recibida' => 'decimal:2',
        'peso' => 'decimal:3',
        'orden' => 'integer',
    ];

    public function conduce(): BelongsTo
    {
        return $this->belongsTo(Conduce::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function getEntregadoCompletoAttribute(): bool
    {
        return $this->cantidad_recibida !== null 
            && (float) $this->cantidad_recibida >= (float) $this->cantidad;
    }

    public function getPendienteAttribute(): float
    {
        if ($this->cantidad_recibida === null) return (float) $this->cantidad;
        return max(0, (float) $this->cantidad - (float) $this->cantidad_recibida);
    }

    public function getPorcentajeEntregadoAttribute(): float
    {
        if (!$this->cantidad) return 0;
        $recibido = (float) ($this->cantidad_recibida ?? 0);
        return min(100, ($recibido / (float) $this->cantidad) * 100);
    }
}
