<?php

namespace App\Mail;

use App\Models\Reservacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservacionConfirmadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Reservacion $reservacion
    ) {
        $this->reservacion->load(['mesa']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu reservación ha sido confirmada'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservacion-confirmada',
            text: 'emails.reservacion-confirmada-text',
            with: [
                'reservacion' => $this->reservacion,
            ]
        );
    }
}
