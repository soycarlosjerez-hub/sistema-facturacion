<?php

namespace App\Mail;

use App\Models\BusinessInstance;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevaSolicitudInstancia extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BusinessInstance $instance,
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva solicitud de instancia — ' . $this->instance->nombre . ' (' . $this->instance->businessType?->nombre . ')',
            middleware: [new \App\Mail\Middleware\ApplyGlobalSmtpConfig()],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nueva-solicitud-instancia',
        );
    }
}
