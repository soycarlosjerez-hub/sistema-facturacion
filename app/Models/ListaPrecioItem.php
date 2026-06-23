<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\TenantScope;

class ListaPrecioItem extends Model
{
    use TenantScope;

    protected $table = 'lista_precio_items';

    protected $fillable = ['lista_precio_id', 'producto_id', 'precio', 'tenant_id'];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    public function listaPrecio(): BelongsTo
    {
        return $this->belongsTo(ListaPrecio::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
