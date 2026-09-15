<?php

namespace App\Events;

use App\Models\SesionCaja;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShiftClosed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly SesionCaja $sesion) {}
}
