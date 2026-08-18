<?php

namespace App\Mail;

use App\Mail\Middleware\ApplyGlobalSmtpConfig;
use App\Models\BusinessInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BienvenidaSuscripcionMail extends Mailable
{
    use Queueable, SerializesModels;

    public BusinessInstance $instance;

    public function __construct(BusinessInstance $instance)
    {
        $this->instance = $instance;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Tu prueba gratuita ha comenzado! — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        $this->instance->loadMissing(['plan']);

        return new Content(
            view: 'emails.bienvenida-suscripcion',
            with: [
                'instance' => $this->instance,
                'empresa' => $this->instance->nombre,
                'planNombre' => $this->instance->plan?->nombre ?? '—',
                'precio' => number_format($this->instance->plan?->precio_mensual ?? $this->instance->costo_mensual ?? 0, 2),
                'trialDays' => $this->instance->trialDays(),
                'trialEnds' => $this->instance->trial_ends_at?->format('d/m/Y') ?? '—',
                'suscripcionUrl' => route('suscripcion.index'),
            ],
        );
    }

    public function middleware(): array
    {
        return [new ApplyGlobalSmtpConfig()];
    }
}