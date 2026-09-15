<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoClima extends Model
{
    use Auditable, HasFactory, TenantScope;

    protected $table = 'tipos_clima';

    protected $fillable = [
        'slug',
        'nombre',
        'categoria',
        'icono',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    public function scopeActivos($query)
    {
        return $query->where('activo', true)->orderBy('orden');
    }

    public static function getTiposOptions(): array
    {
        return self::activos()->pluck('nombre', 'id')->toArray();
    }
}
