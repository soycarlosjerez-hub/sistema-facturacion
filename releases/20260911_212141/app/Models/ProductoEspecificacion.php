<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoEspecificacion extends Model
{
    use Auditable;
    use HasFactory;

    protected $table = 'producto_especificaciones';

    protected $fillable = [
        'producto_id',
        'especificacion_key',
        'especificacion_value',
    ];

    protected $casts = [
        'especificacion_value' => 'string',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function scopePorKey(Builder $query, string $key): Builder
    {
        return $query->where('especificacion_key', $key);
    }

    public function scopeParaProducto(Builder $query, int $productoId): Builder
    {
        return $query->where('producto_id', $productoId);
    }

    /**
     * Obtiene todas las especificaciones del producto como un array clave-valor.
     */
    public function getEspecificacionesArray(int $productoId): array
    {
        return static::where('producto_id', $productoId)
            ->get()
            ->pluck('especificacion_value', 'especificacion_key')
            ->toArray();
    }
}
