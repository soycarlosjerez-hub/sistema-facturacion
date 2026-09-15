<?php

namespace App\Events;

use App\Models\Orden;

class OrderStatusChanged
{
    public function __construct(
        public Orden $order,
        public string $fromStatus,
        public string $toStatus
    ) {}
}
