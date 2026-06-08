<?php

namespace App\Services;

use App\Models\Cotizacion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CotizacionEmailService
{
    /**
     * Enviar cotización por email al cliente
     *
     * @return array{success: bool, destinatario?: string, error?: string, mail_id?: string}
     */
    public function enviar(
        Cotizacion $cotizacion,
        string $mensaje = '',
        ?string $emailDestino = null,
        bool $incluirPdf = true
    ): array {
        try {
            $email = $emailDestino ?? $cotizacion->cliente?->email;

            if (!$email) {
                return [
                    'success' => false,
                    'error' => 'El cliente no tiene un email configurado',
                ];
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'error' => "Email inválido: {$email}",
                ];
            }

            $mail = new \App\Mail\CotizacionEnviadaMail($cotizacion, $mensaje, $incluirPdf);
            $nombreDestinatario = $cotizacion->cliente?->nombre ?? 'Cliente';

            $sentMessage = Mail::to($email, $nombreDestinatario)->send($mail);

            if ($cotizacion->estado === 'borrador') {
                $cotizacion->update(['estado' => 'enviada']);
            }

            Log::info("Cotización {$cotizacion->numero} enviada por email a {$email}");

            return [
                'success' => true,
                'destinatario' => $email,
                'mail_id' => method_exists($sentMessage, 'getMessageId') ? $sentMessage->getMessageId() : null,
            ];

        } catch (\Exception $e) {
            Log::error("Error enviando cotización {$cotizacion->numero}: " . $e->getMessage(), [
                'exception' => $e,
                'cotizacion_id' => $cotizacion->id,
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
