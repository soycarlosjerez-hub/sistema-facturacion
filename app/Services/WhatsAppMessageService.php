<?php

namespace App\Services;

use App\Models\OrdenReparacion;
use App\Models\ServicioDomotica;
use App\Models\Cliente;
use Illuminate\Support\Facades\Log;

class WhatsAppMessageService
{
    public function __construct() {}

    public function enviarMensajeRecibo(int $ordenId): bool
    {
        $orden = OrdenReparacion::with('cliente.equipo')->findOrFail($ordenId);
        $cliente = $orden->cliente;
        $equipo = $orden->equipo;

        if (!$cliente || !$cliente->telefono) {
            Log::warning('WhatsApp: Sin teléfono para orden #' . $ordenId);
            return false;
        }

        $mensaje = "Hola {$cliente->nombre}, recibimos tu {$equipo->marca} {$equipo->modelo} (IMEI: {$equipo->serial_imei}). Nº Orden: {$orden->numero_orden}. Estimado de entrega: " . ($orden->fecha_entrega_estimada?->format('d/m/Y') ?? 'Por confirmar');

        return $this->_enviar($cliente->telefono, $mensaje, 'recibo', 'orden_reparacion', $ordenId);
    }

    public function enviarMensajeDiagnostico(int $ordenId): bool
    {
        $orden = OrdenReparacion::with('cliente.equipo')->findOrFail($ordenId);
        $cliente = $orden->cliente;
        $equipo = $orden->equipo;

        if (!$cliente || !$cliente->telefono) {
            Log::warning('WhatsApp: Sin teléfono para diagnóstico #' . $ordenId);
            return false;
        }

        $mensaje = "Tu {$equipo->marca} {$equipo->modelo} fue diagnosticado: {$orden->diagnostico ?? 'Pendiente'}. Costo estimado: RD$" . number_format($orden->total ?? 0, 2);

        return $this->_enviar($cliente->telefono, $mensaje, 'diagnostico', 'orden_reparacion', $ordenId);
    }

    public function enviarMensajeListo(int $ordenId): bool
    {
        $orden = OrdenReparacion::with('cliente.equipo')->findOrFail($ordenId);
        $cliente = $orden->cliente;
        $equipo = $orden->equipo;

        if (!$cliente || !$cliente->telefono) {
            Log::warning('WhatsApp: Sin teléfono para orden lista #' . $ordenId);
            return false;
        }

        $mensaje = "¡Tu {$equipo->marca} {$equipo->modelo} está listo para recoger! Nº Orden: {$orden->numero_orden}. Dirección: Sucursal Principal";

        return $this->_enviar($cliente->telefono, $mensaje, 'listo', 'orden_reparacion', $ordenId);
    }

    public function enviarMensajeEntrega(int $ordenId): bool
    {
        $orden = OrdenReparacion::with('cliente.equipo')->findOrFail($ordenId);
        $cliente = $orden->cliente;
        $equipo = $orden->equipo;

        if (!$cliente || !$cliente->telefono) {
            Log::warning('WhatsApp: Sin teléfono para entrega #' . $ordenId);
            return false;
        }

        $mensaje = "Tu {$equipo->marca} {$equipo->modelo} fue entregado exitosamente. ¡Gracias por confiar en nosotros!";

        return $this->_enviar($cliente->telefono, $mensaje, 'entrega', 'orden_reparacion', $ordenId);
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
