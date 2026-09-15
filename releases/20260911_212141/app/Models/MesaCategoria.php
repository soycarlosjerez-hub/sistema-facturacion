<?php

namespace App\Models;

use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;

class MesaCategoria extends Model
{
    use TenantScope;

    protected $fillable = ['nombre', 'color', 'icono', 'orden', 'tenant_id'];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function mesas()
    {
        return $this->hasMany(Mesa::class, 'categoria_id');
    }
}
