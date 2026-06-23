<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pago;
use App\Models\Venta;
use App\Models\SesionCaja;
use Illuminate\Support\Facades\Auth;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with('venta.cliente')->latest()->get();

        return view('pagos.index', compact('pagos'));
    }

    public function realizar_pago($venta_id)
    {
        $venta = Venta::with('cliente')->findOrFail($venta_id);
        return view('pagos.pago', compact('venta'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'venta_id' => 'required|exists:ventas,id',
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|string',
            'nota' => 'nullable|string',
        ]);

        \DB::beginTransaction();
        try {
            $venta = Venta::with('cliente')->findOrFail($request->venta_id);

            $metodo = $request->metodo_pago;
            $nota = trim(($request->nota ? $request->nota . ' - ' : '') . 'Método: ' . ucfirst($metodo));

            $sesion = SesionCaja::where('user_id', Auth::id())
                ->where('estado', 'abierta')
                ->latest('fecha_apertura')
                ->first();

            $pago = Pago::create([
                'tenant_id'      => Auth::user()->business_instance_id,
                'venta_id'       => $venta->id,
                'caja_id'        => $sesion?->caja_id ?? $venta->caja_id,
                'sesion_caja_id' => $sesion?->id ?? $venta->sesion_caja_id,
                'monto'          => $request->monto,
                'metodo_pago'    => $metodo,
                'nota'           => $nota,
                'fecha_pago'     => now(),
            ]);

            if ($sesion) {
                if ($metodo === 'efectivo')        $sesion->increment('ventas_efectivo', $request->monto);
                elseif ($metodo === 'tarjeta')     $sesion->increment('ventas_tarjeta', $request->monto);
                elseif ($metodo === 'transferencia')$sesion->increment('ventas_transferencia', $request->monto);
            }

            if ($venta->cliente) {
                $venta->cliente->decrement('balance_pendiente', $request->monto);
            }

            $montoPagadoNuevo = $venta->montoPagado();
            if ($montoPagadoNuevo >= $venta->total) {
                $venta->update(['estado' => 'completada']);
            }

            \DB::commit();
            return redirect()->route('ventas.show', $venta->id)->with('success', 'Pago de RD$ ' . number_format($request->monto, 2) . ' registrado correctamente.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors('Error al registrar el pago: ' . $e->getMessage());
        }
    }
}
