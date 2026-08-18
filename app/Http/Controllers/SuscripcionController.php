<?php

namespace App\Http\Controllers;

use App\Models\BusinessInstance;
use App\Models\CuentaBancaria;
use App\Models\PagoInstancia;
use App\Services\BillingNotificationService;
use Illuminate\Http\Request;

class SuscripcionController extends Controller
{
    public function __construct(protected BillingNotificationService $billing)
    {
    }

    protected function instanciaActual(): BusinessInstance
    {
        $user = auth()->user();
        abort_unless($user && $user->business_instance_id, 403);

        return $user->businessInstance->loadMissing(['plan', 'pagos']);
    }

    public function index()
    {
        $instance = $this->instanciaActual();

        $pendiente = PagoInstancia::query()
            ->where('business_instance_id', $instance->id)
            ->where('estado_pago', 'pendiente')
            ->latest('created_at')
            ->first();

        $cuentas = CuentaBancaria::activo()
            ->orderBy('nombre')
            ->get();

        return view('suscripcion.index', compact('instance', 'pendiente', 'cuentas'));
    }

    public function pagar(Request $request)
    {
        $instance = $this->instanciaActual();

        $pendiente = PagoInstancia::query()
            ->where('business_instance_id', $instance->id)
            ->where('estado_pago', 'pendiente')
            ->first();

        if ($pendiente) {
            return back()->with('error', 'Ya tienes un pago pendiente de confirmación. Nuestro equipo lo validará en breve.');
        }

        $validated = $request->validate([
            'referencia_externa' => ['required', 'string', 'min:4', 'max:120'],
            'monto' => ['nullable', 'numeric', 'min:1'],
        ]);

        $proximo = $instance->proximoPagoEsperado();
        $monto = $validated['monto'] ?? null;
        if ($monto === null) {
            $monto = $instance->deudaEstimada() > 0
                ? $instance->deudaEstimada()
                : $instance->precioMensual();
        }

        $pago = PagoInstancia::create([
            'business_instance_id' => $instance->id,
            'plan_id' => $instance->plan_id,
            'monto' => $monto,
            'mes_pagado' => $proximo?->startOfMonth(),
            'fecha_pago' => now(),
            'metodo_pago' => 'transferencia',
            'referencia_externa' => $validated['referencia_externa'],
            'estado_pago' => 'pendiente',
            'notas' => 'Reportado por el cliente desde el portal de suscripción',
            'registrado_por' => auth()->id(),
        ]);

        $this->billing->transferenciaRecibida($instance, $pago);

        return redirect()->route('suscripcion.index')
            ->with('success', '¡Recibimos tu referencia de transferencia! Será confirmada por nuestro equipo en breve.');
    }

    public function pagos()
    {
        $instance = $this->instanciaActual();

        $pagos = PagoInstancia::query()
            ->where('business_instance_id', $instance->id)
            ->latest('fecha_pago')
            ->paginate(25);

        return view('suscripcion.pagos', compact('instance', 'pagos'));
    }
}