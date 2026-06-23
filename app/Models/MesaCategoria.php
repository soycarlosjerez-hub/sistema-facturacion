<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScope;

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
