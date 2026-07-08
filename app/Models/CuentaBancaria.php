<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;

class CuentaBancaria extends Model
{
    use Auditable;
    use TenantScope;

    protected $table = 'cuentas_bancarias';

    protected $fillable = [
        'nombre', 'banco', 'tipo_cuenta', 'numero_cuenta',
        'moneda', 'titular', 'cedula_ruc',
        'saldo_inicial', 'saldo_actual', 'activo', 'tenant_id',
    ];

    protected $casts = [
        'saldo_inicial' => 'decimal:2',
        'saldo_actual'  => 'decimal:2',
        'activo'        => 'boolean',
    ];

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopeInactivo($query)
    {
        return $query->where('activo', false);
    }
}
