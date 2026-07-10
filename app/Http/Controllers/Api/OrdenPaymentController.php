<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrdenResource;
use App\Models\Orden;
use App\Services\OrdenPaymentService;
use Illuminate\Http\Request;

class OrdenPaymentController extends Controller
{
    public function __construct(
        protected OrdenPaymentService $paymentService
    ) {}

    public function process(Request $request, Orden $orden)
    {
        $validated = $request->validate([
            'metodo_pago'     => 'required|string|in:efectivo,tarjeta,transferencia,mixto,fiado',
            'monto_recibido'  => 'nullable|numeric|min:0',
            'monto_tarjeta'   => 'nullable|numeric|min:0',
            'monto_transferencia' => 'nullable|numeric|min:0',
            'propina'         => 'nullable|numeric|min:0',
            'cargo_servicio'  => 'nullable|boolean',
        ]);

        $result = $this->paymentService->procesarPago($orden, $validated);
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['code']);
        }

        return new OrdenResource($result['orden']);
    }
}
