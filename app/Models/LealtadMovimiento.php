<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LealtadMovimiento extends Model
{
    protected $table = 'lealtad_movimientos';

    protected $fillable = [
        'cuenta_id',
        'tipo',
        'cantidad',
        'venta_id',
        'notas',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(LealtadCuenta::class, 'cuenta_id');
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}
