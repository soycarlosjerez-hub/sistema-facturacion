<?php

namespace App\Services;

use App\Models\EcfDocumento;
use App\Models\SesionCaja;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * Servicio especializado en la cancelación/anulación de ventas.
 * 
 * Responsable: anular una venta, devolver stock, anular e-CF,
 * reponer deuda del cliente, decrementar caja por método de pago.
 * 
 * Maneja el flujo completo de reversión de una venta ya completada,
 * incluyendo las consecuencias fiscales (Nota de Crédito E34).
 */
class SaleCancelService
{
    /**
     * @var SaleStockService Servicio para gestión de inventario
     */
    protected SaleStockService $stockService;

    /**
     * @var SalePaymentService Servicio para gestión de pagos
     */
    protected SalePaymentService $paymentService;

    /**
     * @var SaleEcfService Servicio para gestión de e-CF
     */
    protected SaleEcfService $ecfService;

    /**
     * Constructor con inyección de dependencias.
     */
    public function __construct(
        SaleStockService $stockService,
        SalePaymentService $paymentService,
        SaleEcfService $ecfService
    ) {
        $this->stockService = $stockService;
        $this->paymentService = $paymentService;
        $this->ecfService = $ecfService;
    }

    /**
     * Cancelar (anular) una venta.
     * 
     * @param int $id ID de la venta a cancelar
     * @param string $motivo Motivo de la anulación
     * @return Venta La venta anulada (con soft delete)
     * @throws \Exception Si la venta ya está anulada o no existe
     */
    public function cancelarVenta(int $id, string $motivo): Venta
    {
        $motivo = strip_tags(trim($motivo));
        $tenantId = Auth::user()->business_instance_id;

        // Buscar la venta
        $venta = Venta::with(['detalles', 'ecfDocumento'])
            ->where('tenant_id', $tenantId)
            ->findOrFail($id);

        // Verificar que no esté ya anulada
        if ($venta->trashed()) {
            throw new \Exception('Esta venta ya fue anulada previamente.');
        }

        if ($venta->estado === 'anulada') {
            throw new \Exception('Esta venta ya se encuentra anulada.');
        }

        // 1. Devolver stock de productos
        $this->stockService->devolverStock($venta, $motivo, $tenantId);

        // 2. Revertir obras de arte a disponible
        $this->stockService->revertirObrasVendidas($venta, $tenantId);

        // 3. Revertir equipos a disponible
        $this->stockService->revertirEquiposVendidos($venta);

        // 4. Devolver deuda del cliente si estaba pendiente
        if ($venta->cliente_id && in_array($venta->estado, ['pendiente', 'cuenta_abierta'])) {
            $this->paymentService->devolverDeudaCliente($venta->cliente_id, $venta);
        }

        // 5. Devolver montos de caja
        if ($venta->sesion_caja_id) {
            $sesion = SesionCaja::where('id', $venta->sesion_caja_id)
                ->where('tenant_id', $tenantId)
                ->first();

            if ($sesion) {
                foreach ($venta->pagos as $pago) {
                    $this->paymentService->actualizarCajaPorMetodo(
                        $sesion,
                        $pago->metodo_pago,
                        $pago->monto,
                        false
                    );
                }
            }
        }

        // 6. Generar Nota de Crédito E34 si tiene e-CF aprobado
        if ($venta->ecfDocumento && $venta->ecfDocumento->estado === 'aprobado') {
            $this->ecfService->generarNotaCreditoE34(
                $venta->ecfDocumento,
                $venta,
                $motivo
            );
        }

        // 7. Actualizar estado y soft delete
        $venta->update(['estado' => 'anulada']);
        $venta->delete();

        Log::info('Venta anulada (soft delete)', [
            'venta_id' => $venta->id,
            'total' => $venta->total,
            'motivo' => $motivo,
            'user_id' => Auth::id(),
        ]);

        Event::dispatch(new \App\Events\SaleCancelled($venta, $motivo));

        return $venta;
    }
}
