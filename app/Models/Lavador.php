<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Lavador extends Model
{
    use Auditable;

    protected $table = 'lavadores';

    protected $fillable = [
        'nombre', 'tipo', 'porcentaje', 'telefono', 'email',
        'identificacion', 'activo', 'notas', 'user_id',
    ];

    protected $casts = [
        'porcentaje' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ventas()
    {
        return $this->belongsToMany(Venta::class, 'lavador_venta')
            ->withPivot('porcentaje_aplicado', 'comision')
            ->withTimestamps();
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeFijos($query)
    {
        return $query->where('tipo', 'fijo');
    }

    public function scopeTemporales($query)
    {
        return $query->where('tipo', 'temporal');
    }
}
