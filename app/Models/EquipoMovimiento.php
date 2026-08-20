<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class EquipoMovimiento extends Model
{
    use HasFactory;
    use Auditable;

    protected $table = 'equipo_movimientos';

    protected $fillable = [
        'equipo_id',
        'tipo_movimiento',
        'cantidad',
        'motivo',
        'tenant_id',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    protected $appends = ['tipo_movimiento_label'];

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function scopePorTipo(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo_movimiento', $tipo);
    }

    public function scopePorEquipo(Builder $query, int $equipoId): Builder
    {
        return $query->where('equipo_id', $equipoId);
    }

    public function scopeRecientes(Builder $query, int $dias = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    public function scopeEntradas(Builder $query): Builder
    {
        return $query->where('tipo_movimiento', 'entrada');
    }

    public function scopeSalidas(Builder $query): Builder
    {
        return $query->where('tipo_movimiento', 'salida');
    }

    public function scopeTransferencias(Builder $query): Builder
    {
        return $query->where('tipo_movimiento', 'transferencia');
    }

    public function scopeAjustes(Builder $query): Builder
    {
        return $query->where('tipo_movimiento', 'ajuste');
    }

    public function getTipoMovimientoLabelAttribute(): ?string
    {
        return match ($this->tipo_movimiento) {
            'entrada'      => 'Entrada',
            'salida'       => 'Salida',
            'transferencia' => 'Transferencia',
            'ajuste'       => 'Ajuste de Inventario',
            'devolucion'   => 'Devolución',
            'merma'        => 'Merma',
            default        => null,
        };
    }

    /**
     * Registra un movimiento de equipo de forma atómica.
     */
    public static function registrarMovimiento(
        int $equipoId,
        string $tipoMovimiento,
        int $cantidad,
        ?string $motivo,
        int $tenantId
    ): EquipoMovimiento {
        return static::create([
            'equipo_id'       => $equipoId,
            'tipo_movimiento' => $tipoMovimiento,
            'cantidad'        => $cantidad,
            'motivo'          => $motivo,
            'tenant_id'       => $tenantId,
        ]);
    }
}
