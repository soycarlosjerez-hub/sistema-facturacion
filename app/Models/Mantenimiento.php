<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class Mantenimiento extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $fillable = [
        'numero',
        'business_instance_id',
        'cliente_id',
        'tecnico_id',
        'tipo',
        'estado',
        'contrato_mantenimiento_id',
        'descripcion_falla',
        'solucion_aplicada',
        'repuestos_usados',
        'costo_repuestos',
        'mano_de_obra',
        'total',
        'programada_para',
        'completada_en',
        'created_by',
    ];

    protected $casts = [
        'repuestos_usados' => 'array',
        'programada_para'  => 'datetime',
        'completada_en'    => 'datetime',
        'costo_repuestos'  => 'decimal:2',
        'mano_de_obra'     => 'decimal:2',
        'total'            => 'decimal:2',
    ];

    const TIPOS = [
        'preventivo' => 'Preventivo',
        'correctivo' => 'Correctivo',
    ];

    const ESTADOS = [
        'pendiente'  => 'Pendiente',
        'programada' => 'Programada',
        'en_curso'   => 'En Curso',
        'completado' => 'Completado',
        'cancelado'  => 'Cancelado',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(ContratoMantenimiento::class, 'contrato_mantenimiento_id');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['pendiente', 'programada']);
    }

    public function calcularTotal(): void
    {
        $this->total = ($this->costo_repuestos ?? 0) + ($this->mano_de_obra ?? 0);
        $this->save();
    }

    public function generarNumero(): string
    {
        $year = date('Y');
        $prefix = $this->tipo === 'preventivo' ? 'PREV' : 'CORR';
        $count = self::whereYear('created_at', $year)->where('tipo', $this->tipo)->count() + 1;
        return sprintf('%s-%s-%05d', $prefix, $year, $count);
    }
}
