<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class ServicioDomotica extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;

    protected $table = 'servicios_domotica';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'numero_proyecto',
        'cliente_id',
        'titulo',
        'descripcion',
        'tipo_servicio',
        'direccion_instalacion',
        'equipo_asignado_id',
        'presupuesto',
        'precio_final',
        'subtotal',
        'itbis',
        'descuento',
        'total',
        'estado',
        'fecha_programada',
        'fecha_completada',
        'materiales_usados',
        'horas_trabajadas',
        'notas',
    ];

    protected $casts = [
        'presupuesto' => 'decimal:2',
        'precio_final' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'itbis' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_programada' => 'date',
        'fecha_completada' => 'date',
        'materiales_usados' => 'array',
        'horas_trabajadas' => 'decimal:5,2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(Tecnico::class, 'equipo_asignado_id');
    }

    public function instalaciones(): HasMany
    {
        return $this->hasMany(InstalacionEquipoDomotico::class);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopePorCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['programado', 'pendiente']);
    }

    public function getEstadoLabelAttribute(): ?string
    {
        return match ($this->estado) {
            'pendiente' => 'Pendiente',
            'programado' => 'Programado',
            'en_curso' => 'En Curso',
            'completado' => 'Completado',
            'cancelado' => 'Cancelado',
            default => null,
        };
    }

    public function getTipoServicioLabelAttribute(): ?string
    {
        return match ($this->tipo_servicio) {
            'camaras_seguridad' => 'Cámaras de Seguridad',
            'alarmas' => 'Alarmas',
            'control_acceso' => 'Control de Acceso',
            'redes' => 'Redes',
            'automatizacion' => 'Automatización',
            'sonido' => 'Sonido',
            'iluminacion' => 'Iluminación',
            'otro' => 'Otro',
            default => null,
        };
    }

    public function getAvanceAttribute(): int
    {
        return match ($this->estado) {
            'pendiente' => 0,
            'programado' => 10,
            'en_curso' => 50,
            'completado' => 100,
            'cancelado' => 0,
            default => 0,
        };
    }

    public function calcularTotales(): void
    {
        $this->subtotal = $this->presupuesto;
        $this->itbis = $this->subtotal * 0.18;
        $this->total = $this->subtotal + $this->itbis - $this->descuento;
        $this->save();
    }
}
