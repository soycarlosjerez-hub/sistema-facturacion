<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\TenantScope;

class Promocion extends Model
{
    use TenantScope;

    protected $table = 'promocions';

    protected $fillable = [
        'tenant_id',
        'codigo',
        'nombre',
        'descripcion',
        'tipo',
        'valor',
        'aplica_a',
        'aplica_a_id',
        'valido_desde',
        'valido_hasta',
        'minimo_compra',
        'uso_maximo',
        'uso_actual',
        'activa',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'minimo_compra' => 'decimal:2',
        'uso_maximo' => 'integer',
        'uso_actual' => 'integer',
        'activa' => 'boolean',
        'valido_desde' => 'date',
        'valido_hasta' => 'date',
    ];

    public function promocionUsos(): HasMany
    {
        return $this->hasMany(PromocionUso::class);
    }

    public function aplicaA(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'aplica_a_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true)
            ->where(function ($q) {
                $q->whereNull('valido_desde')
                  ->orWhere('valido_desde', '<=', now()->toDateString());
            })
            ->where(function ($q) {
                $q->whereNull('valido_hasta')
                  ->orWhere('valido_hasta', '>=', now()->toDateString());
            });
    }

    public function scopePorCodigo($query, string $codigo)
    {
        return $query->where('codigo', strtoupper($codigo));
    }

    public function estaVigente(): bool
    {
        if (!$this->activa) return false;
        if ($this->valido_desde && $this->valido_desde->gt(now()->toDateString())) return false;
        if ($this->valido_hasta && $this->valido_hasta->lt(now()->toDateString())) return false;
        if ($this->uso_maximo && $this->uso_actual >= $this->uso_maximo) return false;

        return true;
    }

    public function calcularDescuento(float $subtotal, ?int $aplicaItemId = null): float
    {
        if (!$this->estaVigente()) return 0;
        if ($subtotal < $this->minimo_compra) return 0;

        if ($this->aplica_a === 'todos') {
            return $this->aplicarValor($subtotal);
        }

        if ($this->aplica_a === 'producto' && $aplicaItemId === $this->aplica_a_id) {
            return $this->aplicarValor($subtotal);
        }

        return 0;
    }

    private function aplicarValor(float $subtotal): float
    {
        return match ($this->tipo) {
            'porcentaje' => $subtotal * ($this->valor / 100),
            'monto' => min($this->valor, $subtotal),
            '2x1' => $subtotal * (1 / 3),
            'envio_gratis' => 0,
            'regalo' => 0,
            default => 0,
        };
    }
}
