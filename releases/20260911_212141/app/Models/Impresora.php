<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Impresora extends Model
{
    use Auditable, HasFactory, TenantScope;

    protected $table = 'impresoras';

    protected $fillable = [
        'tenant_id',
        'sucursal_id',
        'nombre',
        'tipo',
        'tipo_conexion',
        'direccion_ip',
        'puerto',
        'ruta_compartida',
        'driver',
        'papel_tamano',
        'caracteres_por_linea',
        'auto_imprimir_ventas',
        'auto_imprimir_cotizaciones',
        'auto_imprimir_conduces',
        'activo',
        'orden',
        'descripcion',
        'configuracion',
    ];

    protected $casts = [
        'puerto' => 'integer',
        'caracteres_por_linea' => 'integer',
        'orden' => 'integer',
        'auto_imprimir_ventas' => 'boolean',
        'auto_imprimir_cotizaciones' => 'boolean',
        'auto_imprimir_conduces' => 'boolean',
        'activo' => 'boolean',
        'configuracion' => 'json',
    ];

    protected static function booted(): void
    {
        static::creating(function ($impresora) {
            if (auth()->check() && ! $impresora->tenant_id) {
                $impresora->tenant_id = auth()->user()->business_instance_id;
            }
        });
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeParaSucursal($query, $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }

    public function scopeAutoImprimir($query, $modulo)
    {
        $columnas = [
            'ventas' => 'auto_imprimir_ventas',
            'cotizaciones' => 'auto_imprimir_cotizaciones',
            'conduces' => 'auto_imprimir_conduces',
        ];

        $columna = $columnas[$modulo] ?? 'auto_imprimir_ventas';

        return $query->where($columna, true);
    }
}
