<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;

class LavaderoCita extends Model
{
    use Auditable;
    use TenantScope;

    protected $fillable = [
        'cliente_id', 'vehiculo_id', 'user_id', 'sucursal_id',
        'fecha_hora', 'servicio', 'estado', 'notas', 'tenant_id',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function scopeDeSucursal($query)
    {
        if ($sucursalId = session('sucursal_id')) {
            return $query->where('sucursal_id', $sucursalId);
        }
        return $query;
    }

    public function scopeDelDia($query)
    {
        return $query->whereDate('fecha_hora', today());
    }
}
