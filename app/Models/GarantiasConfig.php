<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class GarantiasConfig extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;

    protected $table = 'garantia_config';

    protected $fillable = [
        'nombre',
        'tipo_producto',
        'dias_garantia',
        'tipo_garantia',
        'cobertura',
        'activo',
        'orden',
        'tenant_id',
    ];

    protected $casts = [
        'dias_garantia' => 'integer',
        'activo'        => 'boolean',
        'orden'         => 'integer',
        'cobertura'     => 'array',
    ];

    protected $appends = ['activo_label', 'tipo_garantia_label'];

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeByTipoProducto(Builder $query, string $tipoProducto): Builder
    {
        return $query->where('tipo_producto', $tipoProducto);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    public function getActivoLabelAttribute(): string
    {
        return $this->activo ? 'Activo' : 'Inactivo';
    }

    public function getTipoGarantiaLabelAttribute(): ?string
    {
        return match ($this->tipo_garantia) {
            'fabrica'     => 'Garantía de Fábrica',
            'extendida'   => 'Garantía Extendida',
            'parcial'     => 'Garantía Parcial',
            'servicio'    => 'Garantía de Servicio',
            default       => null,
        };
    }

    public function getDuracionLabelAttribute(): string
    {
        $dias = (int) $this->dias_garantia;

        if ($dias >= 365) {
            $anios = intdiv($dias, 365);
            return $anios . ($anios > 1 ? ' años' : ' año');
        }

        if ($dias >= 30) {
            $meses = intdiv($dias, 30);
            return $meses . ($meses > 1 ? ' meses' : ' mes');
        }

        return $dias . ($dias !== 1 ? ' días' : ' día');
    }
}
