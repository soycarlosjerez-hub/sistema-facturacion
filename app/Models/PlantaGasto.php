<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class PlantaGasto extends Model
{
    use Auditable;
    use TenantScope;

    protected $table = 'plantilla_gastos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria',
        'metodo_pago',
        'comprobante',
        'notas',
        'activo',
        'tenant_id',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\App\Models\BusinessInstance::class, 'tenant_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public static function categorias(): array
    {
        return [
            'servicios' => 'Servicios',
            'suministros' => 'Suministros',
            'mantenimiento' => 'Mantenimiento',
            'salarios' => 'Salarios',
            'impuestos' => 'Impuestos',
            'transporte' => 'Transporte',
            'publicidad' => 'Publicidad',
            'alimentacion' => 'Alimentación',
            'otros' => 'Otros',
        ];
    }

    public static function metodosPago(): array
    {
        return [
            'efectivo' => 'Efectivo',
            'tarjeta' => 'Tarjeta',
            'transferencia' => 'Transferencia',
            'cheque' => 'Cheque',
            'otro' => 'Otro',
        ];
    }
}
