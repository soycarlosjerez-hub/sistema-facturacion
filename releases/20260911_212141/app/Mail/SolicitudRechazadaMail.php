<?php

namespace App\Mail;

use App\Models\BusinessInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SolicitudRechazadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BusinessInstance $instance,
        public string $motivo
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Solicitud rechazada — '.config('app.name'),
            middleware: [new \App\Mail\Middleware\ApplyGlobalSmtpConfig],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitud-rechazada',
        );
    }
}
