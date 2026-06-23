<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class Almacen extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;

    protected $table = 'almacenes';


    protected $fillable = ['tenant_id', 'nombre', 'ubicacion', 'sucursal_id'];

    public function movimientos()
    {
        return $this->hasMany(AlmacenMovimiento::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function scopeDeSucursal($query, $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }
}
