<?php

namespace App\Http\Controllers;

use App\Models\BusinessInstance;
use App\Models\BusinessType;
use App\Models\InstanceErrorLog;
use App\Models\User;
use App\Services\OwnerService;
use App\Services\PlanLimitService;
use App\Services\TenantCleanupService;
use App\Traits\LogsOwnerAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OwnerInstanceController extends Controller
{
    use LogsOwnerAction;

    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

    /**
     * Listar todas las instancias con búsqueda y paginación.
     */
    public function instances(): \Illuminate\View\View
    {
        $query = BusinessInstance::with(['businessType', 'plan', 'owner', 'ultimoPago']);

        if (request('show_trashed') === '1') {
            $query->withTrashed();
        }

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('rnc', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (request('business_type')) {
            $query->where('business_type_id', request('business_type'));
        }

        if (request('status')) {
            match (request('status')) {
                'al-dia' => $query->where('activo', true)
                    ->where('bloqueado', false)
                    ->where(function ($q) {
                        $q->whereNull('fecha_vencimiento')
                            ->orWhere('fecha_vencimiento', '>=', now());
                    }),
                'atrasado' => $query->where('activo', true)
                    ->where('bloqueado', false)
                    ->where(function ($q) {
                        $q->whereNull('fecha_vencimiento')
                            ->orWhere('fecha_vencimiento', '<', now());
                    }),
                'bloqueado' => $query->where('bloqueado', true),
                'vencido' => $query->where('activo', true)
                    ->whereNotNull('fecha_vencimiento')
                    ->where('fecha_vencimiento', '<', now()),
                'inactivo' => $query->where('activo', false),
                'pendiente' => $query->where('aprobado', false),
                default => null,
            };
        }

        $instances = $query
            ->orderByRaw('bloqueado DESC, activo DESC, aprobado ASC')
            ->orderByDesc('created_at')
            ->paginate(15);

        // KPIs
        $totalInstances = BusinessInstance::withTrashed()->count();
        $totalActivas = BusinessInstance::where('activo', true)->where('bloqueado', false)->count();
        $totalBloqueadas = BusinessInstance::where('bloqueado', true)->count();
        $totalAtrasadas = BusinessInstance::where('activo', true)
            ->where('bloqueado', false)
            ->where(function ($q) {
                $q->whereNull('fecha_vencimiento')
                    ->orWhere('fecha_vencimiento', '<', now());
            })->count();
        $totalPendientes = BusinessInstance::where('aprobado', false)->count();

        $businessTypes = BusinessType::where('activo', true)->orderBy('nombre')->get();
        $systemMoneda = \App\Models\SystemSetting::get('moneda_simbolo', 'RD$');

        return view('owner.instances.index', compact(
            'instances', 'businessTypes', 'systemMoneda',
            'totalInstances', 'totalActivas', 'totalBloqueadas', 'totalAtrasadas', 'totalPendientes'
        ));
    }

    public function approveInstance($id): \Illuminate\Http\RedirectResponse
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
                Mail::to($instance->owner_email)->send(new \App\Mail\SolicitudAprobadaMail($instance));
            } catch (\Throwable $e) {
                Log::warning('No se pudo enviar email de aprobación: '.$e->getMessage());
            }

            return redirect()->route('owner.instances.index')
                ->with('success', "La solicitud de '{$instance->nombre}' ha sido aprobada exitosamente.");
        } catch (\Throwable $e) {
            Log::error('Error al aprobar solicitud', [
                'instance_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Error al aprobar la solicitud. Intente nuevamente.');
        }
    }

    public function rejectInstance($id, Request $request): \Illuminate\Http\RedirectResponse
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
                    ->send(new \App\Mail\SolicitudRechazadaMail($instance, $request->motivo));
            } catch (\Throwable $e) {
                Log::warning('No se pudo enviar email de rechazo: '.$e->getMessage());
            }

            return redirect()->route('owner.instances.index')
                ->with('success', "La solicitud de '{$instance->nombre}' ha sido rechazada.");
        } catch (\Throwable $e) {
            Log::error('Error al rechazar solicitud', [
                'instance_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Error al rechazar la solicitud. Intente nuevamente.');
        }
    }

    /**
     * Mostrar formulario de creación de instancia.
     */
    public function instancesCreate(): \Illuminate\View\View
    {
        $businessTypes = BusinessType::where('activo', true)->orderBy('nombre')->get();
        $owners = OwnerService::getOwnerUsers();
        $plans = \App\Models\Plan::active();

        return view('owner.instances.create', compact('businessTypes', 'owners', 'plans'));
    }

    /**
     * Almacenar una nueva instancia.
     */
    public function instancesStore(Request $request): \Illuminate\Http\RedirectResponse
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'slug' => 'required|string|max:100|unique:business_instances,slug',
            'rnc' => 'nullable|string|max:20|unique:business_instances,rnc',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:500',
            'business_type_id' => 'required|exists:business_types,id',
            'plan_id' => 'nullable|exists:plans,id',
            'owner_user_id' => 'nullable|exists:users,id',
            'costo_mensual' => 'nullable|numeric|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'activo' => 'boolean',
            'crear_usuario' => 'boolean',
        ];

        if ($request->boolean('crear_usuario')) {
            $allRoles = \Spatie\Permission\Models\Role::pluck('name')->toArray();
            $rules['user_name'] = 'required|string|max:255';
            $rules['user_email'] = 'required|email|max:255|unique:users,email';
            $rules['user_password'] = 'required|string|min:12|confirmed';
            $rules['user_role'] = ['required', 'string', \Illuminate\Validation\Rule::in($allRoles)];
        }

        $data = $request->validate($rules);

        $plan = $data['plan_id'] ? \App\Models\Plan::find($data['plan_id']) : null;

        // Verificar límite de empresas del owner
        if ($plan && $data['owner_user_id']) {
            $instanciasActuales = BusinessInstance::where('owner_user_id', $data['owner_user_id'])
                ->where('activo', true)
                ->count();
            $check = app(PlanLimitService::class)->verificarEmpresa($plan, $instanciasActuales);
            if (! $check['ok']) {
                return back()->withInput()->with('error', $check['mensaje']);
            }
        }

        // Usar precio de lanzamiento para la primera factura si aplica
        $costoMensual = $plan?->precio_mensual ?? $data['costo_mensual'] ?? null;
        $esNuevaInstancia = true; // Primera creación
        $primerPago = $plan?->costoImplementacionEfectivo(); // precio_lanzamiento o precio_implementacion

        $instance = BusinessInstance::create([
            'nombre' => $data['nombre'],
            'slug' => Str::slug($data['slug']),
            'rnc' => $data['rnc'] ?? null,
            'email' => $data['email'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'business_type_id' => $data['business_type_id'],
            'plan_id' => $plan?->id,
            'owner_user_id' => $data['owner_user_id'] ?? auth()->id(),
            'costo_mensual' => $costoMensual,
            'fecha_vencimiento' => $data['fecha_vencimiento'] ?? now()->addMonth(),
            'activo' => $request->boolean('activo', true),
            'configuracion' => [],
        ]);

        // Registrar primer pago (implementación + primer mes) si hay plan
        if ($plan && $primerPago > 0) {
            \App\Models\PagoInstancia::create([
                'business_instance_id' => $instance->id,
                'plan_id' => $plan->id,
                'monto' => $primerPago,
                'mes_pagado' => now()->startOfMonth(),
                'fecha_pago' => now(),
                'metodo_pago' => 'transferencia',
                'referencia_externa' => 'IMPLEMENTACION-LANZAMIENTO',
                'estado_pago' => 'pagado',
                'notas' => 'Implementación (oferta lanzamiento) + primer mes',
                'registrado_por' => auth()->id(),
            ]);

            // El siguiente vencimiento es el mes siguiente al primer mes pagado
            $instance->update(['fecha_vencimiento' => now()->addMonth()->startOfMonth()->addMonth()]);
        }

        if ($request->boolean('crear_usuario')) {
            $businessType = BusinessType::find($data['business_type_id']);
            $newUser = User::create([
                'name' => $data['user_name'],
                'email' => $data['user_email'],
                'password' => \Illuminate\Support\Facades\Hash::make($data['user_password']),
                'business_type_id' => $businessType?->id,
                'business_instance_id' => $instance->id,
                'sucursal_id' => null,
            ]);
            $newUser->assignRole($data['user_role']);
        }

        $this->logOwnerAction('INSTANCE_CREATE', "Instancia '{$instance->nombre}' creada", null, ['id' => $instance->id, 'slug' => $instance->slug], $instance);

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', 'Instancia creada correctamente.');
    }

    /**
     * Mostrar detalle de una instancia.
     */
    public function instancesShow($id): \Illuminate\View\View
    {
        $instance = BusinessInstance::withTrashed()->with(['businessType', 'plan', 'owner', 'ultimoPago'])
            ->findOrFail($id);
        // Only load a reasonable number of users, without loading all Sanctum tokens
        // (tokens are massive and cause N+1 / memory issues)
        $pagosRecientes = \App\Models\PagoInstancia::where('business_instance_id', $id)
            ->with('registradoPor')
            ->latest('mes_pagado')
            ->take(5)
            ->get();
        $errorCount = InstanceErrorLog::where('tenant_id', $id)->recent(7)->count();
        $recentErrors = InstanceErrorLog::where('tenant_id', $id)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('owner.instances.show', compact('instance', 'pagosRecientes', 'errorCount', 'recentErrors'));
    }

    /**
     * Mostrar formulario de edición de instancia.
     */
    public function instancesEdit($id): \Illuminate\View\View
    {
        $instance = BusinessInstance::withTrashed()->findOrFail($id);
        $businessTypes = BusinessType::where('activo', true)->orderBy('nombre')->get();
        $owners = OwnerService::getOwnerUsers();
        $plans = \App\Models\Plan::active();

        return view('owner.instances.edit', compact('instance', 'businessTypes', 'owners', 'plans'));
    }

    /**
     * Actualizar una instancia.
     */
    public function instancesUpdate(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $instance = BusinessInstance::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'rnc' => 'nullable|string|max:20|unique:business_instances,rnc,'.$instance->id,
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:500',
            'business_type_id' => 'required|exists:business_types,id',
            'plan_id' => 'nullable|exists:plans,id',
            'owner_user_id' => 'nullable|exists:users,id',
            'costo_mensual' => 'nullable|numeric|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'activo' => 'boolean',
        ]);

        $plan = $data['plan_id'] ? \App\Models\Plan::find($data['plan_id']) : null;

        $oldData = $instance->getAttributes();
        $instance->update([
            'nombre' => $data['nombre'],
            'rnc' => $data['rnc'] ?? null,
            'email' => $data['email'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'business_type_id' => $data['business_type_id'],
            'plan_id' => $plan?->id,
            'owner_user_id' => $data['owner_user_id'] ?? $instance->owner_user_id,
            'costo_mensual' => $plan?->precio_mensual ?? $data['costo_mensual'] ?? null,
            'fecha_vencimiento' => $data['fecha_vencimiento'] ?? null,
            'activo' => $request->boolean('activo', true),
        ]);

        $this->logOwnerAction('INSTANCE_UPDATE', "Instancia '{$instance->nombre}' actualizada", $oldData, $instance->getAttributes(), $instance);

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', 'Instancia actualizada correctamente.');
    }

    /**
     * Archivar/eliminar permanentemente una instancia.
     */
    public function instancesDestroy($id): \Illuminate\Http\RedirectResponse
    {
        $instance = BusinessInstance::withTrashed()->findOrFail($id);

        if ($instance->trashed()) {
            $instance->forceDelete();
            $msg = 'Instancia eliminada permanentemente.';
        } else {
            $instance->delete();
            $msg = 'Instancia archivada correctamente.';
        }

        return redirect()->route('owner.instances.index')
            ->with('success', $msg);
    }

    /**
     * Limpiar datos operacionales de una instancia.
     */
    public function cleanInstance(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $instance = BusinessInstance::withTrashed()->findOrFail($id);

        $request->validate([
            'confirm_name' => 'required|string|in:'.$instance->nombre,
        ]);

        $tenantId = $instance->id;

        $deletedCount = app(TenantCleanupService::class)->clearTenantData($tenantId);

        BusinessInstance::where('id', $tenantId)->update([
            'setup_completed' => false,
        ]);

        $this->logOwnerAction(
            'INSTANCE_CLEAN',
            "Datos operacionales de '{$instance->nombre}' eliminados completamente ({$deletedCount} filas)",
            null,
            ['tenant_id' => $tenantId, 'rows_deleted' => $deletedCount],
            $instance
        );

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', "Todos los datos operacionales de {$instance->nombre} han sido eliminados. El wizard de configuración se reiniciará en el próximo inicio de sesión.");
    }

    /**
     * Alternar estado de bloqueo de una instancia.
     */
    public function alternarBloqueo(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $instance = BusinessInstance::withTrashed()->findOrFail($id);

        $data = $request->validate([
            'bloqueado' => 'required|boolean',
            'motivo_bloqueo' => 'required_if:bloqueado,1|string|max:500',
        ]);

        $oldBlockState = $instance->bloqueado;
        $instance->update([
            'bloqueado' => $data['bloqueado'],
            'motivo_bloqueo' => $data['bloqueado'] ? $data['motivo_bloqueo'] : null,
            'bloqueado_en' => $data['bloqueado'] ? now() : null,
        ]);

        $this->logOwnerAction(
            $data['bloqueado'] ? 'INSTANCE_BLOCK' : 'INSTANCE_UNBLOCK',
            "Instancia '{$instance->nombre}' ".($data['bloqueado'] ? 'bloqueada' : 'desbloqueada').($data['bloqueado'] && $data['motivo_bloqueo'] ? ': '.$data['motivo_bloqueo'] : ''),
            ['bloqueado' => $oldBlockState],
            ['bloqueado' => $data['bloqueado']],
            $instance
        );

        $msg = $data['bloqueado']
            ? 'Instancia bloqueada correctamente.'
            : 'Instancia desbloqueada correctamente.';

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', $msg);
    }

    /**
     * Vista global: todos los usuarios online de todas las instancias.
     */
    public function onlineUsers(Request $request): \Illuminate\View\View|\Illuminate\Http\JsonResponse
    {
        $threshold = now()->subMinutes(5);

        $onlineUsers = User::with(['businessInstance', 'instanceRole'])
            ->whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', $threshold)
            ->whereNotNull('business_instance_id')
            ->orderByDesc('last_seen_at')
            ->get();

        // Agrupados por instancia
        $byInstance = $onlineUsers->groupBy('business_instance_id');

        $instancias = BusinessInstance::whereIn('id', $byInstance->keys())->get()->keyBy('id');

        // Total de usuarios registrados en cada instancia (para contexto)
        $totalByInstance = User::whereNotNull('business_instance_id')
            ->selectRaw('business_instance_id, count(*) as total')
            ->groupBy('business_instance_id')
            ->pluck('total', 'business_instance_id');

        if ($request->ajax() || $request->header('Accept') === 'application/json') {
            $html = view('owner._online_users_partial', compact('onlineUsers', 'byInstance', 'instancias', 'totalByInstance'))->render();

            return response()->json([
                'online_count' => $onlineUsers->count(),
                'html' => $html,
            ]);
        }

        return view('owner.online', compact('onlineUsers', 'byInstance', 'instancias', 'totalByInstance'));
    }

    /**
     * Vista por instancia: usuarios online de una instancia específica.
     */
    public function instanceOnlineUsers($id): \Illuminate\View\View
    {
        $instance = BusinessInstance::findOrFail($id);
        $threshold = now()->subMinutes(5);

        $onlineUsers = User::with('instanceRole')
            ->where('business_instance_id', $instance->id)
            ->whereNotNull('last_seen_at')
            ->where('last_seen_at', '>=', $threshold)
            ->orderByDesc('last_seen_at')
            ->get();

        $totalUsers = User::where('business_instance_id', $instance->id)->count();

        return view('owner.instances.online', compact('instance', 'onlineUsers', 'totalUsers'));
    }
}
