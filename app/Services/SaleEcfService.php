<?php

namespace App\Services;

use App\Models\EcfDocumento;
use App\Models\Venta;
use App\Services\Ecf\EcfService;
use App\Support\RncValidator;
use Illuminate\Support\Facades\Log;

/**
 * Servicio especializado en la generación y gestión de e-CF para DGII.
 * 
 * Responsable: firmar e-CF, generar e-CF, enviar a DGII,
 * generar notas de crédito E34, validaciones de RNC/Cédula
 * del cliente.
 * 
 * Este servicio actúa como adaptador entre el sistema de ventas
 * y el EcfService que maneja las llamadas externas a la DGII.
 * Todo el procesamiento de e-CF pasa por aquí.
 */
class SaleEcfService
{
    /**
     * @var EcfService Servicio de e-CF para operaciones reales (firmar, generar, enviar)
     */
    protected EcfService $ecfService;

    public function __construct(EcfService $ecfService)
    {
        $this->ecfService = $ecfService;
    }

    /**
     * Procesar la emisión de e-CF para una venta.
     * 
     * Se ejecuta FUERA de la transacción DB (después del commit)
     * para evitar bloqueos de larga duración.
     * 
     * Idempotente: reutiliza el e-CF existente si ya se emitió.
     * 
     * @param Venta $venta La venta a facturar
     * @throws \Exception Si la validación de RNC del cliente falla
     */
    public function procesarEcf(Venta $venta): void
    {
        // Idempotente: reutilizar el e-CF existente si ya se emitió uno
        $existente = EcfDocumento::where('venta_id', $venta->id)
            ->whereNotNull('encf')
            ->orderByDesc('id')
            ->first();

        if ($existente) {
            if ($existente->pendienteEnvio()) {
                try {
                    $this->ecfService->enviar($existente);
                } catch (\Throwable $e) {
                    Log::warning('No se pudo reenviar e-CF de la venta #' . $venta->id . ': ' . $e->getMessage());
                }
            }

            return;
        }

        // Validar RNC/Cédula del cliente para e-CF
        if ($venta->cliente_id) {
            $cliente = $venta->cliente;

            if ($cliente && !empty($cliente->rnc_cedula)) {
                $tipoDoc = $cliente->tipo_documento ?? RncValidator::inferirTipo($cliente->rnc_cedula);
                
                if (!RncValidator::validar($cliente->rnc_cedula, $tipoDoc)) {
                    throw new \Exception(
                        "El RNC/Cédula del cliente ({$cliente->rnc_cedula}) no es válido según DGII."
                    );
                }
            } elseif ($cliente && in_array($venta->tipo_ecf ?? '', ['E31', 'E44', 'E45'])) {
                throw new \Exception("Los e-CF tipo Crédito Fiscal requieren un cliente con RNC válido.");
            }
        }

        // Generar, firmar y enviar e-CF
        try {
            $ecf = $this->ecfService->generarEcf($venta);
            $ecfFirmado = $this->ecfService->firmar($ecf);
            $this->ecfService->enviar($ecfFirmado);
        } catch (\Throwable $e) {
            Log::warning('No se pudo generar e-CF para la venta #' . $venta->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Generar una Nota de Crédito E34 para anulación de venta.
     * 
     * @param EcfDocumento $ecfDocumento El e-CF original a anular
     * @param Venta $venta La venta que se está anulando
     * @param string $motivo Motivo de la anulación
     * @return EcfDocumento|null La nota de crédito creada, o null si falló
     */
    public function generarNotaCreditoE34(EcfDocumento $ecfDocumento, Venta $venta, string $motivo): ?EcfDocumento
    {
        try {
            $nc = $this->ecfService->generarNotaCredito(
                $ecfDocumento,
                'Anulación de venta #' . $venta->id . ': ' . $motivo
            );

            Log::info('Nota de crédito E34 generada por anulación', [
                'venta_id' => $venta->id,
                'nc_encf' => $nc->encf,
            ]);

            return $nc;
        } catch (\Throwable $e) {
            Log::warning('Falló generación de E34 por anulación', [
                'venta_id' => $venta->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Reenviar un e-CF pendiente de envío.
     * Útil para reintentos automáticos o manuales.
     * 
     * @param EcfDocumento $ecfDocumento El e-CF a reenviar
     * @return bool True si se reenvió exitosamente
     */
    public function reenviarEcfPendiente(EcfDocumento $ecfDocumento): bool
    {
        if (!$ecfDocumento->pendienteEnvio()) {
            return false;
        }

        try {
            $this->ecfService->enviar($ecfDocumento);
            return true;
        } catch (\Throwable $e) {
            Log::warning('No se pudo reenviar e-CF pendiente: ' . $e->getMessage());
            return false;
        }
    }
}
