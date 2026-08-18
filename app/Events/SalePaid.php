<?php

namespace App\Events;

use App\Models\Venta;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SalePaid
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Venta $venta) {}
}
