<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Categoria extends Model
{
    use Auditable;
    protected $fillable = ['nombre', 'descripcion', 'activa'];

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
