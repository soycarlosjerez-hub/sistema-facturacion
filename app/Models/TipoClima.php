<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class TipoClima extends Model
{
    use HasFactory, Auditable, TenantScope;

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
        'orden'  => 'integer',
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
