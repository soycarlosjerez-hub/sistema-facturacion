<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    use Auditable, HasFactory, TenantScope;

    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activa',
        'color',
        'icono',
        'orden',
        'configuracion',
        'tenant_id',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'orden' => 'integer',
        'configuracion' => 'json',
    ];

    protected static function booted(): void
    {
        static::creating(function ($categoria) {
            if (auth()->check() && ! $categoria->tenant_id) {
                $categoria->tenant_id = auth()->user()->business_instance_id;
            }
        });
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'categoria_id', 'id');
    }
}
