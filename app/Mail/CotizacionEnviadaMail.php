<?php

namespace App\Mail;

use App\Models\Cotizacion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class CotizacionEnviadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Cotizacion $cotizacion,
        public string $mensajeAdicional = '',
        public bool $incluirPdf = true
    ) {
        $this->cotizacion->load(['cliente', 'user', 'items']);
    }

    public function envelope(): Envelope
    {
        $clienteNombre = $this->cotizacion->cliente?->nombre ?? 'Cliente';
        
        return new Envelope(
            subject: "Cotización {$this->cotizacion->numero} - {$clienteNombre}",
            replyTo: [$this->cotizacion->user?->email ?? config('mail.from.address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cotizacion-enviada',
            text: 'emails.cotizacion-enviada-text',
            with: [
                'cotizacion' => $this->cotizacion,
                'mensajeAdicional' => $this->mensajeAdicional,
                'urlVer' => route('cotizaciones.show', $this->cotizacion),
            ],
        );
    }

    public function attachments(): array
    {
        if (!$this->incluirPdf) {
            return [];
        }

        try {
            $pdf = \PDF::loadView('cotizaciones.pdf', ['cotizacione' => $this->cotizacion]);
            $filename = "cotizacion-{$this->cotizacion->numero}.pdf";
            
            return [
                Attachment::fromData(fn () => $pdf->output(), $filename)
                    ->withMime('application/pdf'),
            ];
        } catch (\Exception $e) {
            \Log::warning('No se pudo adjuntar PDF a email de cotización: ' . $e->getMessage());
            return [];
        }
    }
}
