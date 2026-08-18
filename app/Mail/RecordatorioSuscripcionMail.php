<?php

namespace App\Mail;

use App\Mail\Middleware\ApplyGlobalSmtpConfig;
use App\Models\BusinessInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecordatorioSuscripcionMail extends Mailable
{
    use Queueable, SerializesModels;

    public BusinessInstance $instance;
    public string $titulo;
    public int $dias;

    public function __construct(BusinessInstance $instance, string $titulo, int $dias)
    {
        $this->instance = $instance;
        $this->titulo = $titulo;
        $this->dias = $dias;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->titulo . ' — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        $proximo = $this->instance->proximoPagoEsperado();

        return new Content(
            view: 'emails.recordatorio-suscripcion',
            with: [
                'instance' => $this->instance,
                'empresa' => $this->instance->nombre,
                'titulo' => $this->titulo,
                'dias' => $this->dias,
                'fechaVencimiento' => $proximo?->format('d/m/Y') ?? '—',
                'deuda' => number_format($this->instance->deudaEstimada(), 2),
                'suscripcionUrl' => route('suscripcion.index'),
            ],
        );
    }

    public function middleware(): array
    {
        return [new ApplyGlobalSmtpConfig()];
    }
}