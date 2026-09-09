<?php

namespace App\Mail;

use App\Models\BusinessInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SolicitudAprobadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BusinessInstance $instance
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Solicitud aprobada! Tu empresa ya está activa — ' . config('app.name'),
            middleware: [new \App\Mail\Middleware\ApplyGlobalSmtpConfig()],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitud-aprobada',
        );
    }
}
