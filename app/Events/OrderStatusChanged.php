<?php

namespace App\Events;

use App\Models\Orden;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged
{
    public function __construct(
        public Orden $order,
        public string $fromStatus,
        public string $toStatus
    ) {}
}
