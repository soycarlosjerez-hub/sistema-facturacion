<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class AlmacenMovimiento extends Model
{
    use HasFactory;
    use Auditable;

    protected $fillable = [
        'producto_id', 'detalle_compra_id', 'user_id', 'almacen_id', 'tipo', 'cantidad', 'nota'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }

    public function detalleCompra()
    {
        return $this->belongsTo(DetalleCompra::class, 'detalle_compra_id');
    }
}
