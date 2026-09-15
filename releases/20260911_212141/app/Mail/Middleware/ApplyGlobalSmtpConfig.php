<?php

namespace App\Mail\Middleware;

use App\Services\ErrorMailer;

class ApplyGlobalSmtpConfig
{
    public function handle($mailable, $next)
    {
        ErrorMailer::applyGlobalSmtp();

        return $next($mailable);
    }
}
