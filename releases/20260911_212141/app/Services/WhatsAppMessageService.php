<?php

namespace App\Services;

use App\Models\OrdenReparacion;
use App\Models\ServicioDomotica;
use Illuminate\Support\Facades\Log;

class WhatsAppMessageService
{
    private const ORDEN_TEMPLATES = [
        'recibo' => fn($o) => "Hola {$o->cliente->nombre}, recibimos tu {$o->equipo->marca} {$o->equipo->modelo} (IMEI: {$o->equipo->serial_imei}). Nº Orden: {$o->numero_orden}. Estimado de entrega: " . ($o->fecha_entrega_estimada?->format('d/m/Y') ?? 'Por confirmar'),
        'diagnostico' => fn($o) => "Tu {$o->equipo->marca} {$o->equipo->modelo} fue diagnosticado: {$o->diagnostico ?? 'Pendiente'}. Costo estimado: RD$" . number_format($o->total ?? 0, 2),
        'listo' => fn($o) => "¡Tu {$o->equipo->marca} {$o->equipo->modelo} está listo para recoger! Nº Orden: {$o->numero_orden}. Dirección: Sucursal Principal",
        'entrega' => fn($o) => "Tu {$o->equipo->marca} {$o->equipo->modelo} fue entregado exitosamente. ¡Gracias por confiar en nosotros!",
    ];

    public function enviarMensajeRecibo(int $ordenId): bool
    {
        return $this->enviarParaOrden($ordenId, 'recibo');
    }

    public function enviarMensajeDiagnostico(int $ordenId): bool
    {
        return $this->enviarParaOrden($ordenId, 'diagnostico');
    }

    public function enviarMensajeListo(int $ordenId): bool
    {
        return $this->enviarParaOrden($ordenId, 'listo');
    }

    public function enviarMensajeEntrega(int $ordenId): bool
    {
        return $this->enviarParaOrden($ordenId, 'entrega');
    }

    public function enviarMensajeProgramacion(int $servicioDomoticaId): bool
    {
        $servicio = ServicioDomotica::with('cliente')->findOrFail($servicioDomoticaId);
        $cliente = $servicio->cliente;

        if (!$cliente || !$cliente->telefono) {
            Log::warning('WhatsApp: Sin teléfono para programación domótica #' . $servicioDomoticaId);
            return false;
        }

        $mensaje = "Tu servicio de {$servicio->tipo_servicio_label} (#{$servicio->numero_proyecto}) fue programado para el " . ($servicio->fecha_programada?->format('d/m/Y') ?? 'por confirmar');

        return $this->_enviar($cliente->telefono, $mensaje, 'programacion', 'servicio_domotica', $servicioDomoticaId);
    }

    public function enviarMensajeActualizacion(int $servicioDomoticaId): bool
    {
        $servicio = ServicioDomotica::with('cliente')->findOrFail($servicioDomoticaId);
        $cliente = $servicio->cliente;

        if (!$cliente || !$cliente->telefono) {
            Log::warning('WhatsApp: Sin teléfono para actualización domótica #' . $servicioDomoticaId);
            return false;
        }

        $mensaje = "Actualización de tu servicio de {$servicio->tipo_servicio_label}: Estado actual - {$servicio->estado_label}. Avance: {$servicio->avance}%";

        return $this->_enviar($cliente->telefono, $mensaje, 'actualizacion', 'servicio_domotica', $servicioDomoticaId);
    }

    private function enviarParaOrden(int $ordenId, string $tipo): bool
    {
        $orden = OrdenReparacion::with('cliente.equipo')->findOrFail($ordenId);
        $cliente = $orden->cliente;

        if (!$cliente || !$cliente->telefono) {
            Log::warning('WhatsApp: Sin teléfono para orden #' . $ordenId);
            return false;
        }

        $template = self::ORDEN_TEMPLATES[$tipo] ?? null;
        if (!$template) {
            Log::warning('WhatsApp: Plantilla desconocida "' . $tipo . '" para orden #' . $ordenId);
            return false;
        }

        return $this->_enviar($cliente->telefono, $template($orden), $tipo, 'orden_reparacion', $ordenId);
    }

    private function _enviar(string $telefono, string $mensaje, string $tipo, ?string $relatedType = null, ?int $relatedId = null): bool
    {
        Log::channel('whatsapp')->info("WhatsApp enviado a {$telefono}: {$mensaje}");

        $this->_guardarRegistro($telefono, $mensaje, $tipo, $relatedType, $relatedId, true);

        return true;
    }

    private function _guardarRegistro(string $telefono, string $mensaje, string $tipo, ?string $relatedType, ?int $relatedId, bool $enviado): void
    {
        try {
            \DB::table('whatsapp_messages')->insert([
                'recipient_phone' => $telefono,
                'mensaje' => $mensaje,
                'tipo' => $tipo,
                'related_type' => $relatedType,
                'related_id' => $relatedId,
                'enviado' => $enviado,
                'respuesta' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error guardando registro WhatsApp: ' . $e->getMessage());
        }
    }
}
