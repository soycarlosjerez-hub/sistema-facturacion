<?php

namespace App\Services\Ai\Tools;

use Illuminate\Contracts\Auth\Authenticatable;

interface AiToolInterface
{
    public function getName(): string;

    public function getDescription(): string;

    public function getParameters(): array;

    public function execute(array $input, Authenticatable $user): array;
}
