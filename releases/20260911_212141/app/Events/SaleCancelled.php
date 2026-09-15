<?php

namespace App\Events;

use App\Models\Venta;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SaleCancelled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Venta $venta, public readonly string $motivo) {}
}
