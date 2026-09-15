<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransferDetail extends Model
{
    use Auditable, HasFactory, TenantScope;

    protected $table = 'stock_transfer_details';

    protected $fillable = [
        'transfer_id',
        'producto_id',
        'cantidad',
        'recibido',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'recibido' => 'integer',
    ];

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(StockTransfer::class, 'transfer_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
