<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class Garantia extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;

    protected $table = 'garantias';

    protected $fillable = [
        'tenant_id',
        'orden_reparacion_id',
        'equipo_id',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'cobertura',
        'estado',
        'terminos_condiciones',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'cobertura' => 'decimal:2',
    ];

    public function ordenReparacion(): BelongsTo
    {
        return $this->belongsTo(OrdenReparacion::class);
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function scopeVigentes($query)
    {
        return $query->where('fecha_fin', '>=', today())
            ->where('estado', 'activa');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorEquipo($query, $equipoId)
    {
        return $query->where('equipo_id', $equipoId);
    }

    public function getEstadoLabelAttribute(): ?string
    {
        return match ($this->estado) {
            'activa' => 'Activa',
            'expirada' => 'Expirada',
            'cancelada' => 'Cancelada',
            'en_reclamo' => 'En Reclamo',
            default => null,
        };
    }

    public function getTipoLabelAttribute(): ?string
    {
        return match ($this->tipo) {
            'reparacion' => 'Reparación',
            'pieza' => 'Pieza',
            'servicio' => 'Servicio',
            'extendida' => 'Extendida',
            default => null,
        };
    }

    public function getEstaVigenteAttribute(): bool
    {
        return $this->fecha_fin && $this->fecha_fin->gte(today()) && $this->estado === 'activa';
    }

    public function getDiasRestantesAttribute(): int
    {
        if (!$this->fecha_fin) {
            return 0;
        }

        return max(0, today()->diffInDays($this->fecha_fin, false));
    }
}
