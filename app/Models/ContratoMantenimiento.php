<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class ContratoMantenimiento extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $fillable = [
        'codigo',
        'business_instance_id',
        'cliente_id',
        'tipo_periodicidad',
        'equipos_cubiertos',
        'vigencia_desde',
        'vigencia_hasta',
        'valor_mensual',
        'estado',
        'incluye_visitas',
        'num_visitas_anuales',
        'visitas_realizadas',
        'deducible',
        'cobertura_maxima',
        'created_by',
    ];

    protected $casts = [
        'equipos_cubiertos'  => 'array',
        'vigencia_desde'     => 'date',
        'vigencia_hasta'     => 'date',
        'valor_mensual'      => 'decimal:2',
        'incluye_visitas'    => 'boolean',
        'num_visitas_anuales' => 'integer',
        'visitas_realizadas' => 'integer',
        'deducible'          => 'decimal:2',
        'cobertura_maxima'   => 'decimal:2',
    ];

    const PERIODICIDADES = [
        'mensual'   => 'Mensual',
        'trimestral' => 'Trimestral',
        'semestral' => 'Semestral',
        'anual'     => 'Anual',
    ];

    const ESTADOS = [
        'borrador' => 'Borrador',
        'activo'   => 'Activo',
        'vencido'  => 'Vencido',
        'cancelado' => 'Cancelado',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function visitas(): HasMany
    {
        return $this->hasMany(VisitaProgramada::class, 'contrato_mantenimiento_id');
    }

    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class, 'contrato_mantenimiento_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo')
            ->where('vigencia_hasta', '>=', now()->toDateString());
    }

    public function scopeProximosAVencer($query, $dias = 30)
    {
        return $query->where('estado', 'activo')
            ->where('vigencia_hasta', '<=', now()->addDays($dias)->toDateString())
            ->where('vigencia_hasta', '>', now()->toDateString());
    }

    public function estaActivo(): bool
    {
        return $this->estado === 'activo' && $this->vigencia_hasta >= now()->toDateString();
    }

    public function generarCodigo(): string
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return sprintf('CM-%s-%05d', $year, $count);
    }
}
