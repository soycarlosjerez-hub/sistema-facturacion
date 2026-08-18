<?php

namespace App\Mail;

use App\Mail\Middleware\ApplyGlobalSmtpConfig;
use App\Models\BusinessInstance;
use App\Models\PagoInstancia;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PagoConfirmadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public BusinessInstance $instance;
    public PagoInstancia $pago;

    public function __construct(BusinessInstance $instance, PagoInstancia $pago)
    {
        $this->instance = $instance;
        $this->pago = $pago;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Pago confirmado! Tu suscripción está activa — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pago-confirmado',
            with: [
                'instance' => $this->instance,
                'empresa' => $this->instance->nombre,
                'monto' => number_format($this->pago->monto, 2),
                'referencia' => $this->pago->referencia_externa ?: '—',
                'mesPagado' => $this->pago->mes_pagado?->format('m/Y') ?? '—',
                'suscripcionUrl' => route('suscripcion.index'),
            ],
        );
    }

    public function middleware(): array
    {
        return [new ApplyGlobalSmtpConfig()];
    }
}