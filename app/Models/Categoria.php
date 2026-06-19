<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class Categoria extends Model
{
    use Auditable, TenantScope;

    protected $fillable = ['nombre', 'descripcion', 'activa', 'tenant_id'];

    protected $casts = ['activa' => 'boolean'];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }
}
