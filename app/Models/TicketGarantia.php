<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class TicketGarantia extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $fillable = [
        'codigo',
        'business_instance_id',
        'producto_id',
        'cliente_id',
        'instalacion_id',
        'compra_original_id',
        'fecha_compra',
        'fecha_vencimiento_garantia',
        'tipo_garantia',
        'descripcion_problema',
        'estado',
        'resultado_evaluacion',
        'accion',
        'tecnico_asignado_id',
        'cerrado_en',
        'created_by',
    ];

    protected $casts = [
        'fecha_compra'              => 'date',
        'fecha_vencimiento_garantia' => 'date',
        'cerrado_en'                => 'datetime',
    ];

    const TIPOS = [
        'fabrica'     => 'Fabricante',
        'instalacion' => 'Instalación',
    ];

    const ESTADOS = [
        'abierto'    => 'Abierto',
        'evaluando'  => 'Evaluando',
        'aprobado'   => 'Aprobado',
        'rechazado'  => 'Rechazado',
        'cerrado'    => 'Cerrado',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function instalacion(): BelongsTo
    {
        return $this->belongsTo(Instalacion::class);
    }

    public function compraOriginal(): BelongsTo
    {
        return $this->belongsTo(Compra::class, 'compra_original_id');
    }

    public function tecnicoAsignado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tecnico_asignado_id');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeAbiertos($query)
    {
        return $query->whereIn('estado', ['abierto', 'evaluando']);
    }

    public function estaVigente(): bool
    {
        return $this->fecha_vencimiento_garantia >= now()->toDateString();
    }

    public function diasRestantes(): int
    {
        return max(0, now()->diffInDays($this->fecha_vencimiento_garantia, false));
    }

    public function generarCodigo(): string
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return sprintf('TG-%s-%05d', $year, $count);
    }
}
