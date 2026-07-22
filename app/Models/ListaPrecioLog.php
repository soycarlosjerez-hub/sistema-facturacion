<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\TenantScope;

class ListaPrecioLog extends Model
{
    use TenantScope;

    protected $table = 'lista_precio_logs';

    protected $fillable = [
        'tenant_id',
        'lista_precio_id',
        'producto_id',
        'precio_anterior',
        'precio_nuevo',
        'usuario_id',
        'cambio_en',
        'observacion',
    ];

    protected $casts = [
        'precio_anterior' => 'decimal:2',
        'precio_nuevo' => 'decimal:2',
    ];

    public function listaPrecio(): BelongsTo
    {
        return $this->belongsTo(ListaPrecio::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
