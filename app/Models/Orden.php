<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use Auditable, TenantScope;

    protected $casts = [
        'subtotal'       => 'decimal:2',
        'impuestos'      => 'decimal:2',
        'descuento'      => 'decimal:2',
        'propina'        => 'decimal:2',
        'cargo_servicio' => 'decimal:2',
        'delivery_fee'   => 'decimal:2',
        'hora_retiro'    => 'datetime',
    ];

    protected $fillable = [
        'ncf', 'ncf_tipo', 'ncf_vencimiento',
        'tipo_comprobante', 'encf',
        'terminal_id', 'user_id', 'caja_id', 'sesion_caja_id',
        'cliente_id', 'sucursal_id',
        'tipo_orden', 'entrega_empresa_id',
        'direccion_entrega', 'telefono_contacto', 'hora_retiro',
        'subtotal', 'impuestos', 'descuento', 'total',
        'descuento_tipo', 'descuento_motivo', 'propina',
        'cargo_servicio', 'delivery_fee', 'notas', 'estado',
        'tenant_id',
    ];

    public function terminal()
    {
        return $this->belongsTo(Terminal::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function sesionCaja()
    {
        return $this->belongsTo(SesionCaja::class, 'sesion_caja_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function detalles()
    {
        return $this->hasMany(OrdenDetalle::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'orden_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function entregaEmpresa()
    {
        return $this->belongsTo(DeliveryCompany::class, 'entrega_empresa_id');
    }

    public function scopeDeSucursal($query)
    {
        if ($sucursalId = session('sucursal_id')) {
            return $query->where('sucursal_id', $sucursalId);
        }
        return $query;
    }

    public function montoPagado()
    {
        return $this->pagos()->sum('monto');
    }

    public function estaPagada()
    {
        return $this->montoPagado() >= $this->total;
    }
}
