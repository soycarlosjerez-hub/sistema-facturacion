<?php

namespace App\Http\Controllers;

use App\Mail\SolicitudAprobadaMail;
use App\Mail\SolicitudRechazadaMail;
use App\Models\BusinessInstance;
use App\Models\BusinessType;
use App\Models\InstanceRole;
use App\Services\BillingNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OwnerSolicitudController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner|root']);
    }

    public function index(): View
    {
        $pendingInstances = BusinessInstance::where('aprobado', false)
            ->with(['businessType', 'owner'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $pendingCount = BusinessInstance::where('aprobado', false)->count();
        $approvedCount = BusinessInstance::where('aprobado', true)->count();

        return view('owner.solicitudes.index', compact('pendingInstances', 'pendingCount', 'approvedCount'));
    }

    public function show(int $id): View
    {
        $instance = BusinessInstance::with(['businessType', 'owner'])
            ->findOrFail($id);

        return view('owner.solicitudes.show', compact('instance'));
    }

    public function aprobar(int $id): RedirectResponse
    {
        $instance = BusinessInstance::where('aprobado', false)->findOrFail($id);

        try {
            DB::transaction(function () use ($instance) {
                $instance->update([
                    'aprobado' => true,
                    'aprobado_en' => now(),
                ]);

                $instance->update([
                    'trial_started_at' => now(),
                    'trial_ends_at' => now()->addDays($instance->trialDays()),
                ]);
            });

            try {
                Mail::to($instance->owner_email)->send(new SolicitudAprobadaMail($instance));
            } catch (\Throwable $e) {
                Log::warning('No se pudo enviar email de aprobación: ' . $e->getMessage());
            }

            return redirect()->route('owner.solicitudes.index')
                ->with('success', "La solicitud de '{$instance->nombre}' ha sido aprobada exitosamente.");
        } catch (\Throwable $e) {
            Log::error('Error al aprobar solicitud', [
                'instance_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Error al aprobar la solicitud. Intente nuevamente.');
        }
    }

    public function rechazar(int $id, Request $request): RedirectResponse
    {
        $request->validate([
            'motivo' => ['required', 'string', 'max:500'],
        ]);

        $instance = BusinessInstance::where('aprobado', false)->findOrFail($id);

        try {
            $instance->update([
                'rechazo_motivo' => $request->motivo,
            ]);

            try {
                Mail::to($instance->owner_email)
                    ->send(new SolicitudRechazadaMail($instance, $request->motivo));
            } catch (\Throwable $e) {
                Log::warning('No se pudo enviar email de rechazo: ' . $e->getMessage());
            }

            return redirect()->route('owner.solicitudes.index')
                ->with('success', "La solicitud de '{$instance->nombre}' ha sido rechazada.");
        } catch (\Throwable $e) {
            Log::error('Error al rechazar solicitud', [
                'instance_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Error al rechazar la solicitud. Intente nuevamente.');
        }
    }
}
