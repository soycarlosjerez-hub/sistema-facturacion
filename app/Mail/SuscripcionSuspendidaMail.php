<?php

namespace App\Mail;

use App\Mail\Middleware\ApplyGlobalSmtpConfig;
use App\Models\BusinessInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuscripcionSuspendidaMail extends Mailable
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
            subject: 'Suscripción suspendida por falta de pago — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        $this->instance->loadMissing(['plan']);

        return new Content(
            view: 'emails.suscripcion-suspendida',
            with: [
                'instance' => $this->instance,
                'empresa' => $this->instance->nombre,
                'planNombre' => $this->instance->plan?->nombre ?? '—',
                'deuda' => number_format($this->instance->deudaEstimada(), 2),
                'mesesAtrasados' => $this->instance->mesesAtrasados(),
                'suscripcionUrl' => route('suscripcion.index'),
            ],
        );
    }

    public function middleware(): array
    {
        return [new ApplyGlobalSmtpConfig()];
    }
}