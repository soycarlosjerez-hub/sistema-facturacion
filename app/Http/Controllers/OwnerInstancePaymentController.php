<?php

namespace App\Http\Controllers;

use App\Models\BusinessInstance;
use App\Models\PagoInstancia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class OwnerInstancePaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

    private function logOwnerAction(string $action, string $description, ?array $oldValues = null, ?array $newValues = null, ?Model $model = null): void
    {
        try {
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'model_type' => $model ? get_class($model) : null,
                'model_id' => $model?->id,
                'description' => $description,
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'tenant_id' => null,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to log owner action: ' . $e->getMessage());
        }
    }

    /**
     * Listar historial de pagos de una instancia.
     */
    public function paymentHistory($id): \Illuminate\View\View
    {
        $instance = BusinessInstance::with('businessType')->findOrFail($id);
        $pagos = PagoInstancia::where('business_instance_id', $id)
            ->with('registradoPor')
            ->latest('mes_pagado')
            ->paginate(20);

        return view('owner.instances.pagos.index', compact('instance', 'pagos'));
    }

    /**
     * Formulario para registrar un pago.
     */
    public function registerPayment($id): \Illuminate\View\View
    {
        $instance = BusinessInstance::with('ultimoPago')->findOrFail($id);
        $mesesDisponibles = $this->getMesesDisponibles($instance);
        return view('owner.instances.pagos.create', compact('instance', 'mesesDisponibles'));
    }

    /**
     * Almacena un pago para una instancia.
     */
    public function storePayment(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $instance = BusinessInstance::with('plan')->findOrFail($id);

        $data = $request->validate([
            'monto' => 'required|numeric|min:0',
            'mes_pagado' => 'required|date_format:Y-m-d',
            'metodo_pago' => 'required|string|max:100',
            'referencia_externa' => 'nullable|string|max:255',
            'estado_pago' => 'nullable|string|max:50',
            'notas' => 'nullable|string|max:500',
        ]);

        PagoInstancia::create([
            'business_instance_id' => $instance->id,
            'plan_id' => $instance->plan_id,
            'monto' => $data['monto'],
            'mes_pagado' => $data['mes_pagado'],
            'fecha_pago' => now(),
            'metodo_pago' => $data['metodo_pago'],
            'referencia_externa' => $data['referencia_externa'] ?? null,
            'estado_pago' => $data['estado_pago'] ?? 'completado',
            'notas' => $data['notas'],
            'registrado_por' => auth()->id(),
        ]);

        // Desbloqueo automático cuando el pago cubre el período vigente
        if ($instance->bloqueado && $instance->estaAlDia()) {
            $instance->update([
                'bloqueado' => false,
                'motivo_bloqueo' => null,
                'bloqueado_en' => null,
            ]);

            $this->logOwnerAction(
                'INSTANCE_UNBLOCK',
                "Instancia '{$instance->nombre}' desbloqueada automáticamente tras registrar pago.",
                null,
                ['bloqueado' => false],
                $instance
            );

            return redirect()->route('owner.instances.show', $instance)
                ->with('success', 'Pago registrado correctamente. La instancia fue desbloqueada.');
        }

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', 'Pago registrado correctamente.');
    }

    /**
     * Confirma un pago reportado por el cliente desde el portal de suscripción.
     */
    public function confirmPayment($id, $pagoId): \Illuminate\Http\RedirectResponse
    {
        $instance = BusinessInstance::with('plan')->findOrFail($id);

        $pago = PagoInstancia::where('business_instance_id', $instance->id)
            ->where('id', $pagoId)
            ->where('estado_pago', 'pendiente')
            ->firstOrFail();

        $pago->update([
            'estado_pago' => 'completado',
            'fecha_pago' => now(),
            'notas' => ($pago->notas ? $pago->notas . ' | ' : '') . 'Pago confirmado por ' . auth()->user()->name,
            'registrado_por' => auth()->id(),
        ]);

        $nuevoVencimiento = $pago->mes_pagado?->startOfMonth()->addMonth() ?? now()->addMonth();
        $unblocked = $instance->bloqueado;

        $instance->update([
            'fecha_vencimiento' => $nuevoVencimiento,
            'bloqueado' => false,
            'motivo_bloqueo' => null,
            'bloqueado_en' => null,
        ]);

        $this->logOwnerAction(
            'PAYMENT_CONFIRM',
            "Pago de RD$ " . number_format($pago->monto, 2) . " confirmado para la instancia '{$instance->nombre}' (mes " . $pago->mes_pagado?->format('m/Y') . ').',
            null,
            ['pago_id' => $pago->id, 'monto' => $pago->monto],
            $instance
        );

        try {
            app(\App\Services\BillingNotificationService::class)->pagoConfirmado($instance, $pago);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('confirmPayment: no se pudo notificar el pago: ' . $e->getMessage());
        }

        return redirect()->route('owner.instances.pagos', $instance->id)
            ->with('success', 'Pago confirmado correctamente.' . ($unblocked ? ' La instancia fue desbloqueada.' : ''));
    }

    /**
     * Formulario para editar un pago.
     */
    public function editPayment($id, $pagoId): \Illuminate\View\View
    {
        $instance = BusinessInstance::with('plan')->findOrFail($id);
        $pago = PagoInstancia::where('business_instance_id', $id)->findOrFail($pagoId);
        $mesesDisponibles = $this->getMesesDisponibles($instance);

        return view('owner.instances.pagos.edit', compact('instance', 'pago', 'mesesDisponibles'));
    }

    /**
     * Actualiza un pago existente.
     */
    public function updatePayment(Request $request, $id, $pagoId): \Illuminate\Http\RedirectResponse
    {
        $instance = BusinessInstance::findOrFail($id);
        $pago = PagoInstancia::where('business_instance_id', $id)->findOrFail($pagoId);

        $oldValues = $pago->getAttributes();

        $data = $request->validate([
            'monto' => 'required|numeric|min:0',
            'mes_pagado' => 'required|date_format:Y-m-d',
            'metodo_pago' => 'nullable|string|max:100',
            'referencia_externa' => 'nullable|string|max:255',
            'estado_pago' => 'nullable|string|max:50',
            'notas' => 'nullable|string|max:500',
        ]);

        $pago->update($data);

        $this->logOwnerAction(
            'PAYMENT_UPDATE',
            "Pago #{$pago->id} actualizado para la instancia '{$instance->nombre}' (mes " . $pago->mes_pagado->format('m/Y') . ')',
            $oldValues,
            $pago->getAttributes(),
            $pago
        );

        return redirect()->route('owner.instances.pagos', $instance)
            ->with('success', 'Pago actualizado correctamente.');
    }

    /**
     * Obtener meses disponibles para pagos.
     */
    private function getMesesDisponibles(BusinessInstance $instance): array
    {
        $ultimo = $instance->ultimoPagoConfirmado()->first();
        $desde = $ultimo
            ? $ultimo->mes_pagado->startOfMonth()->addMonth()
            : ($instance->trial_ends_at ?? $instance->created_at)->startOfMonth();

        $meses = [];
        $actual = now()->startOfMonth();
        $cursor = $desde->copy();

        while ($cursor->lessThanOrEqualTo($actual)) {
            $meses[$cursor->format('Y-m-d')] = $cursor->isoFormat('MMMM YYYY');
            $cursor->addMonth();
        }

        return $meses;
    }
}
