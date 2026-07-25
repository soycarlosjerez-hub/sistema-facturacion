<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class ClimatizacionFactura extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'climatizacion_facturas';

    public $tenantColumn = 'business_instance_id';

    protected $fillable = [
        'business_instance_id',
        'cliente_id',
        'created_by',
        'origen',
        'origen_id',
        'referencia',
        'subtotal',
        'itbis',
        'descuento',
        'total',
        'estado',
        'detalle',
    ];

    protected $casts = [
        'detalle' => 'array',
        'subtotal' => 'decimal:2',
        'itbis' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    const ORIGENES = [
        'mantenimiento' => 'Mantenimiento',
        'contrato_cuota' => 'Cuota de Contrato',
        'instalacion' => 'Instalación',
        'emergencia' => 'Emergencia',
    ];

    const ESTADOS = [
        'borrador' => 'Borrador',
        'generada' => 'Generada',
        'enviada' => 'Enviada',
        'anulada' => 'Anulada',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function origenModelo()
    {
        return $this->morphTo();
    }

    public function scopePorOrigen($query, $origen)
    {
        return $query->where('origen', $origen);
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeActivas($query)
    {
        return $query->whereIn('estado', ['generada', 'enviada']);
    }

    public function generarNumero(): string
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return sprintf('CF-%s-%05d', $year, $count);
    }
}
