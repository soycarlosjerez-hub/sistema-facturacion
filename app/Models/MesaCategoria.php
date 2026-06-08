<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MesaCategoria extends Model
{
    protected $fillable = ['nombre', 'color', 'icono', 'orden'];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function mesas()
    {
        return $this->hasMany(Mesa::class, 'categoria_id');
    }
}
