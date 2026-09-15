<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;

class MesaUbicacion extends Model
{
    use Auditable, TenantScope;

    protected $table = 'mesa_ubicaciones';

    protected $fillable = ['nombre', 'descripcion', 'activa', 'tenant_id'];

    protected $casts = ['activa' => 'boolean'];

    public function mesas()
    {
        return $this->hasMany(Mesa::class, 'ubicacion_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }
}
