<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class SplitBillPerson extends Model
{
    use Auditable;

    protected $fillable = [
        'venta_id', 'persona_num', 'persona_nombre', 'items', 'subtotal',
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
