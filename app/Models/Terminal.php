<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Terminal extends Model
{
    use Auditable, TenantScope;

    protected $fillable = [
        'tenant_id', 'nombre', 'codigo',
        'ubicacion', 'caja_id', 'activo',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function ordenes()
    {
        return $this->hasMany(Orden::class);
    }
}
