<?php

namespace App\Mail;

use App\Mail\Middleware\ApplyGlobalSmtpConfig;
use App\Models\BusinessInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionSuspendedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $instance;

    public function __construct(BusinessInstance $instance)
    {
        $this->instance = $instance;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Suscripción suspendida — ' . $this->instance->nombre,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription_suspended',
            with: [
                'instanceName' => $this->instance->nombre,
                'planName' => $this->instance->plan?->nombre ?? 'Personalizado',
                'deuda' => number_format($this->instance->deudaEstimada(), 2),
            ],
        );
    }

    public function middleware(): array
    {
        return [new ApplyGlobalSmtpConfig()];
    }
}
