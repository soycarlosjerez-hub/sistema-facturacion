<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use Auditable;
    protected $casts = [
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'descuento' => 'decimal:2',
        'propina' => 'decimal:2',
        'cargo_servicio' => 'decimal:2',
        'total' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'fecha' => 'datetime',
    ];

    protected $fillable = [
        'ncf', 'ncf_tipo', 'ncf_vencimiento',
        'tipo_comprobante', 'encf',
        'user_id', 'caja_id', 'sesion_caja_id',
        'cliente_id', 'tipo_venta_id', 'sucursal_id', 'mesa_id',
        'fecha', 'subtotal', 'impuestos', 'descuento', 'total', 'estado',
        'descuento_tipo', 'descuento_motivo', 'notas', 'tipo_orden', 'propina',
        'delivery_company_id', 'delivery_fee', 'cargo_servicio', 'vehiculo_id',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
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
        return $this->hasMany(VentaDetalle::class);
    }

    public function tipoVenta()
    {
        return $this->belongsTo(TipoVenta::class);
    }
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function ecf()
    {
        return $this->hasOne(EcfDocumento::class, 'venta_id');
    }

    public function ecfDocumento()
    {
        return $this->ecf();
    }

    public function scopeDeSucursal($query)
    {
        if ($sucursalId = session('sucursal_id')) {
            return $query->where('sucursal_id', $sucursalId);
        }
        return $query;
    }

    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }

    public function deliveryCompany()
    {
        return $this->belongsTo(DeliveryCompany::class);
    }

    public function splitBillPersons()
    {
        return $this->hasMany(\App\Models\SplitBillPerson::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function lavadores()
    {
        return $this->belongsToMany(\App\Models\Lavador::class, 'lavador_venta')
            ->withPivot('porcentaje_aplicado', 'comision')
            ->withTimestamps();
    }

    public function usaEcf(): bool
    {
        return $this->tipo_comprobante === 'ecf' || !empty($this->encf);
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
