<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// NOTE: This model is a duplicate of DetalleCompra.
// DetalleCompra uses the table 'compra_detalles' and has full $fillable, $casts, and relationships.
// This file exists only for backward compatibility; use DetalleCompra for new code.
class CompraDetalle extends Model
{
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }
}
