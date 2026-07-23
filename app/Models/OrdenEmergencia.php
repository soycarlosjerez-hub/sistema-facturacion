<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class OrdenEmergencia extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $fillable = [
        'codigo',
        'business_instance_id',
        'cliente_id',
        'prioridad',
        'tipo_falla',
        'direccion',
        'contacto_telefono',
        'estado',
        'descripcion',
        'tecnico_id',
        'costo_estimado',
        'costo_final',
        'sla_deadline',
        'respondida_en',
        'resuelta_en',
        'created_by',
    ];

    protected $casts = [
        'costo_estimado'  => 'decimal:2',
        'costo_final'     => 'decimal:2',
        'sla_deadline'    => 'datetime',
        'respondida_en'   => 'datetime',
        'resuelta_en'     => 'datetime',
    ];

    const PRIORIDADES = [
        'critica' => 'Crítica',
        'alta'    => 'Alta',
        'media'   => 'Media',
        'baja'    => 'Baja',
    ];

    const TIPOS_FALLA = [
        'sin_frio'       => 'Sin Frío',
        'sin_calor'      => 'Sin Calor',
        'fuga_gas'       => 'Fuga de Gas',
        'ruido_excesivo' => 'Ruido Excesivo',
        'cortocircuito'  => 'Cortocircuito',
        'otro'           => 'Otro',
    ];

    const ESTADOS = [
        'reportada' => 'Reportada',
        'asignada'  => 'Asignada',
        'en_camino' => 'En Camino',
        'en_lugar'  => 'En Lugar',
        'resuelta'  => 'Resuelta',
        'cerrada'   => 'Cerrada',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePorPrioridad($query, $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeActivas($query)
    {
        return $query->whereIn('estado', ['reportada', 'asignada', 'en_camino', 'en_lugar']);
    }

    public function scopeCriticas($query)
    {
        return $query->where('prioridad', 'critica')
            ->whereIn('estado', ['reportada', 'asignada']);
    }

    public function slaCumplido(): ?bool
    {
        if (!$this->sla_deadline) {
            return null;
        }
        return now()->lte($this->sla_deadline);
    }

    public function tiempoRespuestaMinutos(): ?int
    {
        if (!$this->respondida_en || !$this->created_at) {
            return null;
        }
        return $this->created_at->diffInMinutes($this->respondida_en);
    }

    public function tiempoResolutionMinutos(): ?int
    {
        if (!$this->resuelta_en || !$this->created_at) {
            return null;
        }
        return $this->created_at->diffInMinutes($this->resuelta_en);
    }

    public function generarCodigo(): string
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return sprintf('EMERG-%s-%05d', $year, $count);
    }

    public function calcularSLA(): void
    {
        $horasSLA = match ($this->prioridad) {
            'critica' => 2,
            'alta'    => 4,
            'media'   => 8,
            'baja'    => 24,
            default   => 24,
        };
        $this->sla_deadline = $this->created_at->addHours($horasSLA);
        $this->save();
    }
}
