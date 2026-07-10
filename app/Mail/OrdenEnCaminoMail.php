<?php

namespace App\Mail;

use App\Models\Orden;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrdenEnCaminoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Orden $orden
    ) {
        $this->orden->load(['detalles.producto', 'cliente']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu pedido está en camino #' . $this->orden->id
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orden-en-camino',
            with: ['orden' => $this->orden]
        );
    }
}
