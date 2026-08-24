<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LavaderoPaqueteItem extends Model
{
    use HasFactory;

    protected $table = 'lavadero_paquete_items';

    protected $fillable = [
        'paquete_id',
        'tipo',
        'servicio_id',
        'producto_id',
        'cantidad',
        'precio_individual',
        'incluir_automatico',
        'orden',
    ];

    protected $casts = [
        'cantidad'          => 'decimal:2',
        'precio_individual' => 'decimal:2',
        'incluir_automatico' => 'boolean',
        'orden'             => 'integer',
    ];

    /**
     * Paquete al que pertenece este ítem
     */
    public function paquete(): BelongsTo
    {
        return $this->belongsTo(LavaderoPaquete::class, 'paquete_id');
    }

    /**
     * Servicio del lavadero (si el ítem es un servicio)
     */
    public function servicio(): BelongsTo
    {
        return $this->belongsTo(LavaderoServicio::class, 'servicio_id');
    }

    /**
     * Producto (si el ítem es un producto físico)
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
