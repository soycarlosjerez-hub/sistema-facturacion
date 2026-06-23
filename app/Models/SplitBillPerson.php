<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;

class SplitBillPerson extends Model
{
    use Auditable;
    use TenantScope;

    protected $fillable = [
        'venta_id', 'persona_num', 'persona_nombre', 'items', 'subtotal', 'tenant_id',
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'decimal:2',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
