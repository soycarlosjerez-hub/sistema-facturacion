<?php

namespace App\Events;

use App\Models\Producto;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockCritical
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Producto $product, public int $currentStock) {}
}
