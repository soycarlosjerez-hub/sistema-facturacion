<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class VisitaProgramada extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $fillable = [
        'contrato_mantenimiento_id',
        'mantenimiento_id',
        'fecha_programada',
        'fecha_ejecutada',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_programada' => 'date',
        'fecha_ejecutada'  => 'datetime',
    ];

    const ESTADOS = [
        'programada' => 'Programada',
        'completada' => 'Completada',
        'cancelada'  => 'Cancelada',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(ContratoMantenimiento::class, 'contrato_mantenimiento_id');
    }

    public function mantenimiento(): BelongsTo
    {
        return $this->belongsTo(Mantenimiento::class);
    }

    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha_programada', $fecha);
    }

    public function scopeProgramadas($query)
    {
        return $query->where('estado', 'programada');
    }
}
