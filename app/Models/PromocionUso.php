<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\TenantScope;

class PromocionUso extends Model
{
    use TenantScope;

    protected $table = 'promocion_usos';

    protected $fillable = [
        'promocion_id',
        'cart_id',
        'venta_id',
        'descuento_aplicado',
    ];

    protected $casts = [
        'descuento_aplicado' => 'decimal:2',
    ];

    public function promocion(): BelongsTo
    {
        return $this->belongsTo(Promocion::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }
}
