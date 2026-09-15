<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoCompra extends Model
{
    use Auditable, HasFactory, TenantScope;

    protected $fillable = [
        'tenant_id',
        'compra_id',
        'caja_id',
        'sesion_caja_id',
        'monto',
        'metodo_pago',
        'payment_processor_id',
        'nota',
        'fecha_pago',
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
        'monto' => 'decimal:2',
    ];

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    public function sesionCaja(): BelongsTo
    {
        return $this->belongsTo(SesionCaja::class, 'sesion_caja_id');
    }

    public function paymentProcessor(): BelongsTo
    {
        return $this->belongsTo(PaymentProcessor::class);
    }
}
