<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NcfExpiring
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $ncfNumber,
        public string $type,
        public string $expiryDate,
        public int $daysRemaining,
        public ?int $businessInstanceId = null
    ) {}
}
