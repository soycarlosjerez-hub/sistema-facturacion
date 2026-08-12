<?php

namespace App\Http\Controllers;

use App\Mail\UserCreatedNotification;
use App\Models\BusinessInstance;
use App\Models\BusinessType;
use App\Models\BusinessTypeModule;
use App\Models\InstanceApiKey;
use App\Models\InstanceErrorLog;
use App\Models\InstanceNotificationSetting;
use App\Models\InstanceRole;
use App\Models\InstanceRoleModule;
use App\Models\Modulo;
use App\Models\PagoInstancia;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Services\UserBusinessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Database\Eloquent\Model;

class OwnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:owner');
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
            Log::error('Failed to log owner action: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $totalInstancias = BusinessInstance::withTrashed()->count();
        $archivadas = BusinessInstance::onlyTrashed()->count();
        $activas = BusinessInstance::where('activo', true)->count();
        $bloqueadas = BusinessInstance::where('bloqueado', true)->count();
        $vencidas = BusinessInstance::where('activo', true)
            ->where('fecha_vencimiento', '<', now())
            ->count();
        $porVencer = BusinessInstance::where('activo', true)
            ->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '>=', now())
            ->where('fecha_vencimiento', '<=', now()->addDays(30))
            ->count();

        $instancias = BusinessInstance::with(['businessType', 'owner', 'ultimoPago'])
            ->orderByRaw('bloqueado DESC, activo DESC')
            ->get();

        $instanciasPorTipo = $instancias->groupBy(fn($i) => $i->businessType?->nombre ?? 'Sin tipo')
            ->map(fn($g) => $g->count())
            ->sortDesc();

        $instanciasConAtraso = $instancias->filter(fn($i) => $i->activo && !$i->bloqueado && !$i->estaAlDia());

        $proximosVencimientos = $instancias->filter(fn($i) => $i->activo && !$i->bloqueado && $i->fecha_vencimiento && $i->fecha_vencimiento >= now() && $i->fecha_vencimiento <= now()->addDays(30))
            ->sortBy('fecha_vencimiento')
            ->take(5);

        $ingresosEsperados = $instancias->where('activo', true)->sum('costo_mensual');
        $ingresosRealesMes = PagoInstancia::whereMonth('fecha_pago', now()->month)
            ->whereYear('fecha_pago', now()->year)
            ->sum('monto');

        $mrr = BusinessInstance::where('activo', true)
            ->get()
            ->sum(fn($i) => $i->precioMensual());

        $planes = \App\Models\Plan::where('activo', true)
            ->orderBy('orden')
            ->withCount('businessInstances')
            ->get();

        $totalTipos = BusinessType::count();
        $totalUsuarios = User::count();

        return view('owner.dashboard', compact(
            'totalInstancias', 'activas', 'bloqueadas', 'vencidas', 'porVencer', 'archivadas',
            'instancias', 'instanciasPorTipo', 'instanciasConAtraso',
            'proximosVencimientos', 'ingresosEsperados', 'ingresosRealesMes',
            'totalTipos', 'totalUsuarios', 'mrr', 'planes'
        ));
    }

    public function businessTypes()
    {
        $businessTypes = BusinessType::with('modules')
            ->withCount([
                'businessInstances',
                'businessInstances as instancias_activas' => fn ($q) => $q->where('activo', true),
                'usersAsociados',
            ])
            ->orderBy('orden')
            ->get();

        $allModules = Modulo::where('activo', true)->orderBy('orden')->get();

        $stats = [
            'tipos'       => $businessTypes->count(),
            'instancias'  => $businessTypes->sum('business_instances_count'),
            'activas'     => $businessTypes->sum('instancias_activas'),
            'usuarios'    => $businessTypes->sum('users_asociados_count'),
            'modulos'     => $allModules->count(),
        ];

        return view('owner.business-types.index', compact('businessTypes', 'allModules', 'stats'));
    }

    public function businessTypesCreate()
    {
        $allModules = Modulo::where('activo', true)->orderBy('orden')->get();
        return view('owner.business-types.create', compact('allModules'));
    }

    public function businessTypesStore(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'slug' => 'required|string|max:50|unique:business_types,slug',
            'descripcion' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:100',
            'activo' => 'boolean',
            'orden' => 'integer|min:0',
            'modules' => 'nullable|array',
            'modules.*' => 'string',
        ]);

        $businessType = BusinessType::create([
            'nombre' => $data['nombre'],
            'slug' => $data['slug'],
            'descripcion' => $data['descripcion'] ?? null,
            'color' => $data['color'] ?? 'primary',
            'icon' => $data['icon'] ?? 'bi-building',
            'activo' => $request->boolean('activo', true),
            'orden' => $data['orden'] ?? 0,
        ]);

        $selectedModules = $data['modules'] ?? [];
        $allModules = Modulo::where('activo', true)->get();
        foreach ($allModules as $modulo) {
            BusinessTypeModule::create([
                'business_type_id' => $businessType->id,
                'modulo_key' => $modulo->key,
                'visible' => in_array($modulo->key, $selectedModules),
                'orden' => $modulo->orden ?? 0,
            ]);
        }

        BusinessType::flush();

        return redirect()->route('owner.business-types.index')
            ->with('success', "Tipo de negocio \"{$businessType->nombre}\" creado correctamente.");
    }

    public function businessTypesDestroy($id)
    {
        $businessType = BusinessType::findOrFail($id);

        $instancesCount = BusinessInstance::where('business_type_id', $id)->count();
        if ($instancesCount > 0) {
            return back()->with('error', "No se puede eliminar \"{$businessType->nombre}\" porque {$instancesCount} instancia(s) lo est&aacute;n usando.");
        }

        $businessType->modules()->delete();
        $businessType->delete();
        BusinessType::flush();

        return redirect()->route('owner.business-types.index')
            ->with('success', "Tipo de negocio \"{$businessType->nombre}\" eliminado.");
    }

    public function businessTypesEdit($id)
    {
        $businessType = BusinessType::with('modules')->findOrFail($id);
        $allModules = Modulo::where('activo', true)->orderBy('orden')->get();
        return view('owner.business-types.edit', compact('businessType', 'allModules'));
    }

    public function businessTypesUpdate(Request $request, $id)
    {
        $businessType = BusinessType::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:100',
            'activo' => 'boolean',
            'orden' => 'integer|min:0',
            'modules' => 'nullable|array',
            'modules.*' => 'string',
        ]);

        $businessType->update([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'color' => $data['color'] ?? null,
            'icon' => $data['icon'] ?? null,
            'activo' => $request->boolean('activo', true),
            'orden' => $data['orden'] ?? 0,
        ]);

        $selectedModules = $data['modules'] ?? [];
        $allModules = Modulo::where('activo', true)->get();
        foreach ($allModules as $modulo) {
            BusinessTypeModule::updateOrCreate(
                [
                    'business_type_id' => $businessType->id,
                    'modulo_key' => $modulo->key,
                ],
                [
                    'visible' => in_array($modulo->key, $selectedModules),
                    'orden' => $modulo->orden ?? 0,
                ]
            );
        }

        BusinessType::flush();

        return redirect()->route('owner.business-types.index')
            ->with('success', 'Tipo de negocio actualizado correctamente.');
    }

    // ─── Módulos CRUD ────────────────────────────────────────────────

    public function modulesIndex()
    {
        $categorias = Modulo::select('categoria')->distinct()->orderBy('categoria')->pluck('categoria');
        $modulos = Modulo::orderBy('categoria')->orderBy('orden')->get();
        return view('owner.modules.index', compact('modulos', 'categorias'));
    }

    public function modulesCreate()
    {
        $categorias = Modulo::select('categoria')->distinct()->orderBy('categoria')->pluck('categoria');
        return view('owner.modules.form', compact('categorias'));
    }

    public function modulesStore(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string|max:50|unique:modulos,key',
            'label' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'categoria' => 'required|string|max:50',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'boolean',
        ]);

        Modulo::create([
            'key' => $data['key'],
            'label' => $data['label'],
            'icon' => $data['icon'] ?? 'bi-circle',
            'categoria' => $data['categoria'],
            'orden' => $data['orden'] ?? 0,
            'activo' => $request->boolean('activo', true),
        ]);

        return redirect()->route('owner.modules.index')
            ->with('success', "Módulo \"{$data['label']}\" creado correctamente.");
    }

    public function modulesEdit($id)
    {
        $modulo = Modulo::findOrFail($id);
        $categorias = Modulo::select('categoria')->distinct()->orderBy('categoria')->pluck('categoria');
        return view('owner.modules.form', compact('modulo', 'categorias'));
    }

    public function modulesUpdate(Request $request, $id)
    {
        $modulo = Modulo::findOrFail($id);

        $data = $request->validate([
            'key' => 'required|string|max:50|unique:modulos,key,' . $modulo->id,
            'label' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'categoria' => 'required|string|max:50',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'boolean',
        ]);

        $modulo->update([
            'key' => $data['key'],
            'label' => $data['label'],
            'icon' => $data['icon'] ?? 'bi-circle',
            'categoria' => $data['categoria'],
            'orden' => $data['orden'] ?? 0,
            'activo' => $request->boolean('activo', true),
        ]);

        return redirect()->route('owner.modules.index')
            ->with('success', "Módulo \"{$modulo->label}\" actualizado correctamente.");
    }

    public function modulesDestroy($id)
    {
        $modulo = Modulo::findOrFail($id);

        $typesCount = BusinessTypeModule::where('modulo_key', $modulo->key)->count();
        $instanceRolesCount = InstanceRoleModule::where('modulo_key', $modulo->key)->count();
        $instanceOverrideCount = \App\Models\BusinessInstanceModule::where('modulo_key', $modulo->key)->count();

        if ($typesCount > 0 || $instanceRolesCount > 0 || $instanceOverrideCount > 0) {
            return back()->with('error', "No se puede eliminar \"{$modulo->label}\" porque está en uso por {$typesCount} tipo(s) de negocio, {$instanceRolesCount} role(s) de instancia y {$instanceOverrideCount} instancia(s). Desactívelo en su lugar.");
        }

        $modulo->delete();

        return redirect()->route('owner.modules.index')
            ->with('success', "Módulo \"{$modulo->label}\" eliminado.");
    }

    // ===================== PLANES (SaaS) =====================

    public function plansIndex()
    {
        $planes = \App\Models\Plan::withCount('businessInstances')->orderBy('orden')->get();

        return view('owner.planes.index', compact('planes'));
    }

    public function plansCreate()
    {
        $modulos = Modulo::allActive();

        return view('owner.planes.create', compact('modulos'));
    }

    public function plansStore(Request $request)
    {
        $data = $this->validatePlan($request);

        \App\Models\Plan::create($data);

        \App\Models\Plan::flush();

        return redirect()->route('owner.plans.index')
            ->with('success', 'Plan creado correctamente.');
    }

    public function plansEdit($id)
    {
        $plan = \App\Models\Plan::findOrFail($id);
        $modulos = Modulo::allActive();

        return view('owner.planes.edit', compact('plan', 'modulos'));
    }

    public function plansUpdate(Request $request, $id)
    {
        $plan = \App\Models\Plan::findOrFail($id);

        $data = $this->validatePlan($request, $plan);

        $plan->update($data);

        \App\Models\Plan::flush();

        return redirect()->route('owner.plans.index')
            ->with('success', 'Plan actualizado correctamente.');
    }

    public function plansDestroy($id)
    {
        $plan = \App\Models\Plan::findOrFail($id);

        $inUse = BusinessInstance::where('plan_id', $plan->id)->count();

        if ($inUse > 0) {
            return back()->with('error', "No se puede eliminar el plan \"{$plan->nombre}\" porque está asignado a {$inUse} instancia(s). Desactívelo en su lugar.");
        }

        $plan->delete();

        \App\Models\Plan::flush();

        return redirect()->route('owner.plans.index')
            ->with('success', 'Plan eliminado correctamente.');
    }

    private function validatePlan(Request $request, ?\App\Models\Plan $plan = null): array
    {
        $slugRule = 'required|string|max:100|unique:plans,slug' . ($plan ? ',' . $plan->id : '');

        return $request->validate([
            'nombre' => 'required|string|max:255',
            'slug' => $slugRule,
            'descripcion' => 'nullable|string|max:500',
            'precio_mensual' => 'required|numeric|min:0',
            'precio_implementacion' => 'nullable|numeric|min:0',
            'precio_lanzamiento' => 'nullable|numeric|min:0',
            'max_usuarios' => 'nullable|integer|min:0',
            'max_sucursales' => 'nullable|integer|min:0',
            'max_empresas' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'modulos' => 'nullable|array',
            'modulos.*' => 'string|max:100',
            'activo' => 'boolean',
            'recomendado' => 'boolean',
            'orden' => 'nullable|integer|min:0',
        ]) + [
            'modulos' => $request->input('modulos', []),
            'features' => $request->input('features', []),
            'activo' => $request->boolean('activo', true),
            'recomendado' => $request->boolean('recomendado', false),
            'orden' => (int) $request->input('orden', 0),
        ];
    }

    public function instances()
    {
        $query = BusinessInstance::with(['businessType', 'plan', 'owner', 'ultimoPago']);
        
        if (request('show_trashed') === '1') {
            $query->withTrashed();
        }

        $instances = $query
            ->orderByRaw('bloqueado DESC, activo DESC')
            ->latest()
            ->paginate(15);

        $businessTypes = BusinessType::where('activo', true)->orderBy('nombre')->get();

        return view('owner.instances.index', compact('instances', 'businessTypes'));
    }

    public function instancesCreate()
    {
        $businessTypes = BusinessType::where('activo', true)->orderBy('nombre')->get();
        $owners = User::role('owner')->orderBy('name')->get();
        $plans = \App\Models\Plan::active();
        return view('owner.instances.create', compact('businessTypes', 'owners', 'plans'));
    }

    public function instancesStore(Request $request)
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
            $rules['user_role'] = ['required', 'string', Rule::in($allRoles)];
        }

        $data = $request->validate($rules);

        $plan = $data['plan_id'] ? \App\Models\Plan::find($data['plan_id']) : null;

        // Verificar límite de empresas del owner
        if ($plan && $data['owner_user_id']) {
            $instanciasActuales = BusinessInstance::where('owner_user_id', $data['owner_user_id'])
                ->where('activo', true)
                ->count();
            $check = app(\App\Services\PlanLimitService::class)->verificarEmpresa($plan, $instanciasActuales);
            if (!$check['ok']) {
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
                'password' => Hash::make($data['user_password']),
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

    public function instancesShow($id)
    {
        $instance = BusinessInstance::withTrashed()->with(['businessType', 'plan', 'owner', 'users.tokens', 'ultimoPago'])
            ->findOrFail($id);
        $pagosRecientes = PagoInstancia::where('business_instance_id', $id)
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

    public function instancesEdit($id)
    {
        $instance = BusinessInstance::withTrashed()->findOrFail($id);
        $businessTypes = BusinessType::where('activo', true)->orderBy('nombre')->get();
        $owners = User::role('owner')->orderBy('name')->get();
        $plans = \App\Models\Plan::active();
        return view('owner.instances.edit', compact('instance', 'businessTypes', 'owners', 'plans'));
    }

    public function instancesUpdate(Request $request, $id)
    {
        $instance = BusinessInstance::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'rnc' => 'nullable|string|max:20|unique:business_instances,rnc,' . $instance->id,
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

    public function instancesDestroy($id)
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

    public function instancesConfig($id)
    {
        $instance = BusinessInstance::withTrashed()->with('businessType')->findOrFail($id);
        $globalSettings = [
            'nombre_empresa' => SystemSetting::get('nombre_empresa', ''),
            'slogan' => SystemSetting::get('slogan', ''),
            'moneda_simbolo' => SystemSetting::get('moneda_simbolo', 'RD$'),
            'itbis_porcentaje' => SystemSetting::get('itbis_porcentaje', 18),
            'prefijo_factura' => SystemSetting::get('prefijo_factura', 'FAC-'),
            'prefijo_ncf' => SystemSetting::get('prefijo_ncf', ''),
            'dias_credito' => SystemSetting::get('dias_credito', 30),
            'impresora_papel_default' => SystemSetting::get('impresora_papel_default', '80mm'),
        ];
        $instanceConfig = $instance->configuracion ?? [];
        $instanceNotifSettings = InstanceNotificationSetting::forInstance($instance);

        return view('owner.instances.config', compact('instance', 'globalSettings', 'instanceConfig', 'instanceNotifSettings'));
    }

    public function instancesConfigUpdate(Request $request, $id)
    {
        $instance = BusinessInstance::findOrFail($id);

        $data = $request->validate([
            'nombre_empresa' => 'nullable|string|max:255',
            'slogan' => 'nullable|string|max:500',
            'moneda_simbolo' => 'nullable|string|max:10',
            'itbis_porcentaje' => 'nullable|numeric|min:0|max:100',
            'prefijo_factura' => 'nullable|string|max:20',
            'prefijo_ncf' => 'nullable|string|max:10',
            'dias_credito' => 'nullable|integer|min:0|max:365',
            'impresora_papel_default' => 'nullable|in:58mm,80mm',
            'restaurante_valida_stock' => 'nullable|string',
            'enabled' => 'nullable|boolean',
            'sale_created' => 'nullable|boolean',
            'sale_paid' => 'nullable|boolean',
            'sale_cancelled' => 'nullable|boolean',
            'order_confirmed' => 'nullable|boolean',
            'order_ready' => 'nullable|boolean',
            'order_shipped' => 'nullable|boolean',
            'payment_received' => 'nullable|boolean',
            'credit_overdue' => 'nullable|boolean',
            'credit_abono' => 'nullable|boolean',
            'stock_critical' => 'nullable|boolean',
            'stock_restocked' => 'nullable|boolean',
            'product_created' => 'nullable|boolean',
            'shift_opened' => 'nullable|boolean',
            'shift_closed' => 'nullable|boolean',
            'cash_shortage' => 'nullable|boolean',
            'daily_report' => 'nullable|boolean',
            'ncff_expiring' => 'nullable|boolean',
            'ecf_certificate_expiring' => 'nullable|boolean',
            'backup_completed' => 'nullable|boolean',
            'backup_failed' => 'nullable|boolean',
            'user_registered' => 'nullable|boolean',
        ]);

        $data['restaurante_valida_stock'] = $request->has('restaurante_valida_stock') ? '1' : '0';

        $existingConfig = $instance->configuracion ?? [];
        $mergedConfig = array_merge($existingConfig, array_filter($data, fn($v) => !is_null($v)));

        $instance->update(['configuracion' => $mergedConfig]);

        $notifData = collect($data)->only([
            'enabled',
            'sale_created', 'sale_paid', 'sale_cancelled',
            'order_confirmed', 'order_ready', 'order_shipped',
            'payment_received', 'credit_overdue', 'credit_abono',
            'stock_critical', 'stock_restocked', 'product_created',
            'shift_opened', 'shift_closed', 'cash_shortage', 'daily_report',
            'ncff_expiring', 'ecf_certificate_expiring',
            'backup_completed', 'backup_failed', 'user_registered',
        ])->toArray();

        InstanceNotificationSetting::updateOrCreate(
            ['business_instance_id' => $instance->id],
            $notifData
        );

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', 'Configuración de instancia actualizada correctamente.');
    }

    public function cleanInstance(Request $request, $id)
    {
        $instance = BusinessInstance::withTrashed()->findOrFail($id);

        $request->validate([
            'confirm_name' => 'required|string|in:' . $instance->nombre,
        ]);

        $tenantId = $instance->id;

        DB::transaction(function () use ($tenantId) {

            // FK integrity preserved - filtered by tenant_id only
            $tables = [
                // Ventas y sus relaciones
                'split_bill_persons',
                'venta_detalles',
                'pagos',
                'ventas',

                // ECF / NCF
                'ecf_log_envios',
                'ecf_documentos',
                'secuencias_ecf',
                'ncf_sequences',

                // Conduces
                'conduce_items',
                'conduces',

                // Devoluciones
                'detalles_devolucion',
                'devoluciones',

                // Compras
                'compra_detalles',
                'compras',

                // Gastos
                'gastos',

                // Cotizaciones
                'cotizacion_items',
                'cotizaciones',

                // Almacenes
                'almacen_movimientos',
                'almacenes',

                // Restaurante
                'reservaciones',
                'waitlist_entries',
                'mesas',
                'mesa_ubicaciones',
                'mesa_categorias',
                'categories',

                // Lavadero
                'lavadero_citas',
                'lavadero_servicios',
                'lavadores',

                // Alquiler
                'alquiler_contratos',
                'alquiler_inquilinos',
                'alquiler_viviendas',
                'alquiler_pagos',

                // Tattoo
                'tattoo_appointments',
                'tattoo_artists',
                'tattoo_designs',

                // Vehículos
                'vehiculos',

                // Cajas
                'sesion_cajas',
                'cajas',

                // Listas de precio
                'lista_precio_items',
                'lista_precios',

                // Maestros operacionales
                'proveedores',
                'clientes',
                'productos',
                'categorias',
                'sucursales',

                // Configuración operacional de la instancia
                'system_settings',

                // Logs de errores de la instancia
                'instance_error_logs',
            ];

            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    DB::table($table)->where('tenant_id', $tenantId)->delete();
                }
            }

            \App\Models\BusinessInstance::where('id', $tenantId)->update([
                'setup_completed' => false,
            ]);
        });

        $this->logOwnerAction(
            'INSTANCE_CLEAN',
            "Datos operacionales de '{$instance->nombre}' eliminados completamente",
            null,
            ['tenant_id' => $tenantId],
            $instance
        );

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', "Todos los datos operacionales de {$instance->nombre} han sido eliminados. El wizard de configuración se reiniciará en el próximo inicio de sesión.");

    }

    public function alternarBloqueo(Request $request, $id)
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
            "Instancia '{$instance->nombre}' " . ($data['bloqueado'] ? 'bloqueada' : 'desbloqueada') . ($data['bloqueado'] && $data['motivo_bloqueo'] ? ': ' . $data['motivo_bloqueo'] : ''),
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

    public function paymentHistory($id)
    {
        $instance = BusinessInstance::with('businessType')->findOrFail($id);
        $pagos = PagoInstancia::where('business_instance_id', $id)
            ->with('registradoPor')
            ->latest('mes_pagado')
            ->paginate(20);

        return view('owner.instances.pagos.index', compact('instance', 'pagos'));
    }

    public function registerPayment($id)
    {
        $instance = BusinessInstance::with('ultimoPago')->findOrFail($id);
        $mesesDisponibles = $this->getMesesDisponibles($instance);
        return view('owner.instances.pagos.create', compact('instance', 'mesesDisponibles'));
    }

    public function storePayment(Request $request, $id)
    {
        $instance = BusinessInstance::with('plan')->findOrFail($id);

        $data = $request->validate([
            'monto' => 'required|numeric|min:0',
            'mes_pagado' => 'required|date_format:Y-m-d',
            'metodo_pago' => 'nullable|string|max:100',
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

    public function instanceUserCreate($id)
    {
        $instance = BusinessInstance::with('businessType')->findOrFail($id);
        $instanceRoles = InstanceRole::where('business_instance_id', $instance->id)->orderBy('name')->get();
        return view('owner.instances.users.create', compact('instance', 'instanceRoles'));
    }

    public function instanceUserStore(Request $request, $id)
    {
        $instance = BusinessInstance::with('businessType')->findOrFail($id);

        $limitCheck = app(\App\Services\PlanLimitService::class)->verificarUsuario($instance);
        if (! $limitCheck['ok']) {
            return back()->withInput()->with('error', $limitCheck['mensaje']);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:12|confirmed',
            'instance_role_id' => 'nullable|exists:instance_roles,id',
        ]);

        $duplicateEmail = User::where('email', $data['email'])
            ->where('business_instance_id', $instance->id)
            ->exists();
        if ($duplicateEmail) {
            return back()->withInput()->with('error', 'Ya existe un usuario con ese email en esta instancia.');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin-business',
            'business_type_id' => $instance->businessType?->id,
            'business_instance_id' => $instance->id,
            'instance_role_id' => $data['instance_role_id'] ?? null,
            'sucursal_id' => null,
        ]);

        $user->assignRole('admin-business');

        try {
            Mail::to($user->email)->send(new UserCreatedNotification($user, $data['password']));
        } catch (\Exception $e) {
            Log::error('Failed to send user created email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'instance_id' => $instance->id,
                'error' => $e->getMessage(),
            ]);
        }

        $this->logOwnerAction('USER_CREATE', "Usuario '{$user->name}' creado para instancia '{$instance->nombre}'", null, ['user_id' => $user->id], $instance);

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', "Usuario {$user->name} creado correctamente para {$instance->nombre}.");
    }

    public function instanceUserEdit($id, $userId)
    {
        $instance = BusinessInstance::findOrFail($id);
        $user = User::where('business_instance_id', $instance->id)->findOrFail($userId);
        $instanceRoles = InstanceRole::where('business_instance_id', $instance->id)->orderBy('name')->get();

        return view('owner.instances.users.edit', compact('instance', 'user', 'instanceRoles'));
    }

    public function instanceUserUpdate(Request $request, $id, $userId)
    {
        $instance = BusinessInstance::findOrFail($id);
        $user = User::where('business_instance_id', $instance->id)->findOrFail($userId);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:12|confirmed',
            'instance_role_id' => 'nullable|exists:instance_roles,id',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->instance_role_id = $data['instance_role_id'] ?? null;

        $passwordChanged = false;
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
            $passwordChanged = true;
        }

        $user->save();

        if ($passwordChanged) {
            try {
                Mail::to($user->email)->send(new UserCreatedNotification($user, $data['password']));
            } catch (\Exception $e) {
                Log::error('Failed to send password change email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'instance_id' => $instance->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function instanceUserDestroy($id, $userId)
    {
        $instance = BusinessInstance::findOrFail($id);
        $user = User::where('business_instance_id', $instance->id)->findOrFail($userId);

        if ($user->hasRole('owner')) {
            return redirect()->route('owner.instances.show', $instance)
                ->with('error', 'No puedes eliminar al dueño del sistema desde aquí.');
        }

        $name = $user->name;
        $this->logOwnerAction('USER_DELETE', "Usuario '{$name}' eliminado de instancia '{$instance->nombre}'", ['user_id' => $user->id, 'email' => $user->email], null, $instance);
        \App\Models\AuditLog::where('user_id', $user->id)->delete();
        $user->delete();

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', "Usuario {$name} eliminado de {$instance->nombre}.");
    }

    // ─── Instance Roles CRUD ─────────────────────────────────────────

    public function instanceRoles($id)
    {
        $instance = BusinessInstance::findOrFail($id);
        $roles = InstanceRole::where('business_instance_id', $instance->id)
            ->withCount('users')
            ->orderBy('name')
            ->get();
        return view('owner.instances.roles.index', compact('instance', 'roles'));
    }

    public function instanceRolesCreate($id)
    {
        $instance = BusinessInstance::findOrFail($id);
        $modulos = Modulo::allActive()->groupBy('categoria');
        $totalModulos = Modulo::allActive()->count();
        return view('owner.instances.roles.create', compact('instance', 'modulos', 'totalModulos'));
    }

    public function instanceRolesStore(Request $request, $id)
    {
        $instance = BusinessInstance::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'modulos' => 'nullable|array',
            'modulos.*' => 'string|exists:modulos,key',
        ]);

        $existing = InstanceRole::where('business_instance_id', $instance->id)
            ->where('name', $data['name'])->exists();
        if ($existing) {
            return back()->withInput()->with('error', 'Ya existe un rol con ese nombre en esta instancia.');
        }

        $role = InstanceRole::create([
            'business_instance_id' => $instance->id,
            'name' => $data['name'],
        ]);

        if (!empty($data['modulos'])) {
            $role->syncModules($data['modulos']);
        }

        $this->logOwnerAction('ROLE_CREATE', "Rol '{$role->name}' creado para instancia '{$instance->nombre}'", null, ['role_id' => $role->id], $instance);

        return redirect()->route('owner.instances.roles', $instance)
            ->with('success', "Rol '{$role->name}' creado correctamente.");
    }

    public function instanceRolesEdit($id, $roleId)
    {
        $instance = BusinessInstance::findOrFail($id);
        $role = InstanceRole::where('business_instance_id', $instance->id)
            ->with('modules')->findOrFail($roleId);
        $modulos = Modulo::allActive()->groupBy('categoria');

        // Extraer módulos de contabilidad y crear categoría propia
        $modsContabilidad = ['ncf','ecf','secuencias-ecf','certificados-digitales','libros-ventas','libros-compras','reportes-retenciones','reportes-fiscales','reportes-resumen','formulario-14-14'];
        $contabilidadMods = collect();
        foreach ($modsContabilidad as $key) {
            $modulos->each(function ($items) use ($key, &$contabilidadMods) {
                $found = $items->firstWhere('key', $key);
                if ($found) {
                    $contabilidadMods->push($found);
                }
            });
        }
        // Remover de categorías originales
        $modulos = $modulos->map(function ($items) use ($modsContabilidad) {
            return $items->reject(fn($m) => in_array($m->key, $modsContabilidad));
        })->filter(fn($items) => $items->isNotEmpty());
        if ($contabilidadMods->isNotEmpty()) {
            $modulos->put('contabilidad', $contabilidadMods);
        }

        $totalModulos = Modulo::allActive()->count();
        $selectedModulos = $role->modules->where('is_visible', true)->pluck('modulo_key')->toArray();

        // Ordenar categorías: contabilidad antes de sistema/reportes
        $orden = ['core','operaciones','clientes','organizacion','lavadero','restaurante','alquileres','tattoo','climatizacion','tecnologia','contabilidad','delivery','reportes','sistema','configuracion'];
        $sorted = [];
        foreach ($orden as $cat) {
            if ($modulos->has($cat)) {
                $sorted[$cat] = $modulos->get($cat);
            }
        }
        // Agregar cualquier categoría que no esté en el orden definido
        foreach ($modulos as $cat => $items) {
            if (!isset($sorted[$cat])) {
                $sorted[$cat] = $items;
            }
        }
        $modulos = collect($sorted);

        return view('owner.instances.roles.edit', compact('instance', 'role', 'modulos', 'totalModulos', 'selectedModulos'));
    }

    public function instanceRolesUpdate(Request $request, $id, $roleId)
    {
        $instance = BusinessInstance::findOrFail($id);
        $role = InstanceRole::where('business_instance_id', $instance->id)
            ->findOrFail($roleId);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'modulos' => 'nullable|array',
            'modulos.*' => 'string|exists:modulos,key',
        ]);

        $existing = InstanceRole::where('business_instance_id', $instance->id)
            ->where('name', $data['name'])
            ->where('id', '!=', $role->id)->exists();
        if ($existing) {
            return back()->withInput()->with('error', 'Ya existe otro rol con ese nombre en esta instancia.');
        }

        $role->update(['name' => $data['name']]);

        if (!empty($data['modulos'])) {
            $role->syncModules($data['modulos']);
        } else {
            $role->modules()->delete();
        }

        return redirect()->route('owner.instances.roles', $instance)
            ->with('success', "Rol '{$role->name}' actualizado correctamente.");
    }

    public function instanceRolesDestroy($id, $roleId)
    {
        $instance = BusinessInstance::findOrFail($id);
        $role = InstanceRole::where('business_instance_id', $instance->id)
            ->findOrFail($roleId);

        if ($role->users()->count() > 0) {
            return back()->with('error', 'No puedes eliminar un rol que tiene usuarios asignados.');
        }

        $name = $role->name;
        $this->logOwnerAction('ROLE_DELETE', "Rol '{$name}' eliminado de instancia '{$instance->nombre}'", ['role_id' => $role->id], null, $instance);
        $role->delete();

        return redirect()->route('owner.instances.roles', $instance)
            ->with('success', "Rol '{$name}' eliminado correctamente.");
    }

    /**
     * List users for the current admin-business instance
     */
    public function instanceUsersIndex()
    {
        $user = auth()->user();
        $instance = $user->businessInstance;
        
        if (!$instance) {
            abort(403, 'No tienes una instancia asignada.');
        }
        
        $users = User::where('business_instance_id', $instance->id)
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'owner');
            })
            ->with('roles')
            ->latest()
            ->paginate(15);
        
        return view('owner.instances.users.index', compact('instance', 'users'));
    }

    public function globalErrors()
    {
        $query = InstanceErrorLog::with('user', 'resolvedBy', 'tenant');

        if ($instanceId = request('instance_id')) {
            $query->where('tenant_id', $instanceId);
        }
        if ($level = request('level')) {
            $query->ofLevel($level);
        }
        if ($source = request('source')) {
            $query->ofSource($source);
        }
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }
        if ($desde = request('desde')) {
            $query->whereDate('created_at', '>=', $desde);
        }
        if ($hasta = request('hasta')) {
            $query->whereDate('created_at', '<=', $hasta);
        }
        if (request()->has('resolved') && request('resolved') !== '') {
            $query->where('resolved', request('resolved'));
        }

        $errorLogs = $query->latest()->paginate(30)->withQueryString();

        $instances = BusinessInstance::orderBy('nombre')->get();

        $stats = [
            'total' => InstanceErrorLog::count(),
            'last_7d' => InstanceErrorLog::recent(7)->count(),
            'errors' => InstanceErrorLog::ofLevel('error')->count(),
            'warnings' => InstanceErrorLog::ofLevel('warning')->count(),
            'criticals' => InstanceErrorLog::ofLevel('critical')->count(),
        ];

        return view('owner.errors.index', compact('errorLogs', 'stats', 'instances'));
    }

    public function instanceErrors($id)
    {
        $instance = BusinessInstance::findOrFail($id);

        $query = InstanceErrorLog::where('tenant_id', $id)->with('user', 'resolvedBy', 'tenant');

        if ($level = request('level')) {
            $query->ofLevel($level);
        }
        if ($source = request('source')) {
            $query->ofSource($source);
        }
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }
        if ($desde = request('desde')) {
            $query->whereDate('created_at', '>=', $desde);
        }
        if ($hasta = request('hasta')) {
            $query->whereDate('created_at', '<=', $hasta);
        }
        if (request()->has('resolved') && request('resolved') !== '') {
            $query->where('resolved', request('resolved'));
        }

        $errorLogs = $query->latest()->paginate(30)->withQueryString();

        $stats = [
            'total' => InstanceErrorLog::where('tenant_id', $id)->count(),
            'last_7d' => InstanceErrorLog::where('tenant_id', $id)->recent(7)->count(),
            'errors' => InstanceErrorLog::where('tenant_id', $id)->ofLevel('error')->count(),
            'warnings' => InstanceErrorLog::where('tenant_id', $id)->ofLevel('warning')->count(),
            'criticals' => InstanceErrorLog::where('tenant_id', $id)->ofLevel('critical')->count(),
        ];

        return view('owner.instances.errors', compact('instance', 'errorLogs', 'stats'));
    }

    public function clearErrors($id)
    {
        $instance = BusinessInstance::findOrFail($id);

        $deleted = InstanceErrorLog::where('tenant_id', $id)
            ->where('created_at', '<', now()->subDays(30))
            ->delete();

        return back()->with('success', "Se eliminaron {$deleted} errores antiguos.");
    }

    public function resolveError($instanceId, InstanceErrorLog $errorLog)
    {
        $instance = BusinessInstance::findOrFail($instanceId);

        if ($errorLog->tenant_id !== (int) $instanceId) {
            abort(404);
        }

        $errorLog->update([
            'resolved' => !$errorLog->resolved,
            'resolved_at' => $errorLog->resolved ? null : now(),
            'resolved_by' => $errorLog->resolved ? null : auth()->id(),
        ]);

        $msg = $errorLog->resolved ? 'Error marcado como resuelto.' : 'Error reabierto.';

        return back()->with('success', $msg);
    }

    private function getMesesDisponibles(BusinessInstance $instance): array
    {
        $ultimo = $instance->ultimoPago()->first();
        $desde = $ultimo
            ? $ultimo->mes_pagado->startOfMonth()->addMonth()
            : $instance->created_at->startOfMonth();

        $meses = [];
        $actual = now()->startOfMonth();
        $cursor = $desde->copy();

        while ($cursor->lessThanOrEqualTo($actual)) {
            $meses[$cursor->format('Y-m-d')] = $cursor->isoFormat('MMMM YYYY');
            $cursor->addMonth();
        }

        return $meses;
    }

    // ─────────────────────────────────────────────────────────
    //  Usuarios Online
    // ─────────────────────────────────────────────────────────

    /**
     * Vista global: todos los usuarios online de todas las instancias.
     */
    public function onlineUsers(Request $request)
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
    public function instanceOnlineUsers($id)
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

    // ─── API Tokens CRUD ────────────────────────────────────────────

    public function instanceTokensStore(Request $request, $id)
    {
        $instance = BusinessInstance::findOrFail($id);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
        ]);

        $user = User::where('business_instance_id', $instance->id)->findOrFail($data['user_id']);

        $abilities = $request->input('abilities', ['instancia:*']);
        $token = $user->createToken($data['name'], (array) $abilities);

        $this->logOwnerAction('TOKEN_CREATE', "Token '{$data['name']}' creado para usuario '{$user->name}' en instancia '{$instance->nombre}'", null, ['token_id' => $token->accessToken->id], $instance);

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', 'Token creado correctamente.')
            ->with('new_token', $token->plainTextToken);
    }

    public function instanceTokensDestroy($id, $tokenId)
    {
        $instance = BusinessInstance::findOrFail($id);

        $token = PersonalAccessToken::findOrFail($tokenId);

        $user = User::where('business_instance_id', $instance->id)
            ->findOrFail($token->tokenable_id);

        $this->logOwnerAction('TOKEN_REVOKE', "Token revocado para usuario '{$user->name}' en instancia '{$instance->nombre}'", ['token_id' => $token->id], null, $instance);
        $token->delete();

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', 'Token revocado correctamente.');
    }

    // ─── Instance API Keys CRUD ──────────────────────────────────────

    public function instanceApiKeys($id)
    {
        $instance = BusinessInstance::findOrFail($id);

        $apiKeys = InstanceApiKey::where('business_instance_id', $instance->id)
            ->with('creator')
            ->latest()
            ->get();

        return view('owner.instances.api-keys', compact('instance', 'apiKeys'));
    }

    public function instanceApiKeyGenerate(Request $request, $id)
    {
        $instance = BusinessInstance::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $rawKey = 'iak_' . Str::random(40);

        $apiKey = InstanceApiKey::create([
            'business_instance_id' => $instance->id,
            'name' => $data['name'],
            'key' => hash('sha256', $rawKey),
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        $this->logOwnerAction('API_KEY_CREATE', "API Key '{$apiKey->name}' creada para instancia '{$instance->nombre}'", null, ['api_key_id' => $apiKey->id], $instance);

        return redirect()->route('owner.instances.api-keys', $instance)
            ->with('success', 'API Key creada correctamente.')
            ->with('new_api_key', $rawKey);
    }

    public function instanceApiKeyRegenerate($id, $apiKeyId)
    {
        $instance = BusinessInstance::findOrFail($id);
        $apiKey = InstanceApiKey::where('business_instance_id', $instance->id)
            ->findOrFail($apiKeyId);

        $rawKey = 'iak_' . Str::random(40);
        $apiKey->update(['key' => hash('sha256', $rawKey)]);

        return redirect()->route('owner.instances.api-keys', $instance)
            ->with('success', 'API Key regenerada correctamente.')
            ->with('new_api_key', $rawKey);
    }

    public function instanceApiKeyToggle($id, $apiKeyId)
    {
        $instance = BusinessInstance::findOrFail($id);
        $apiKey = InstanceApiKey::where('business_instance_id', $instance->id)
            ->findOrFail($apiKeyId);

        $apiKey->update(['is_active' => !$apiKey->is_active]);
        $status = $apiKey->is_active ? 'activada' : 'desactivada';

        return redirect()->route('owner.instances.api-keys', $instance)
            ->with('success', "API Key \"{$apiKey->name}\" {$status} correctamente.");
    }

    public function instanceApiKeyDestroy($id, $apiKeyId)
    {
        $instance = BusinessInstance::findOrFail($id);
        $apiKey = InstanceApiKey::where('business_instance_id', $instance->id)
            ->findOrFail($apiKeyId);

        $name = $apiKey->name;
        $this->logOwnerAction('API_KEY_DELETE', "API Key '{$name}' eliminada de instancia '{$instance->nombre}'", ['api_key_id' => $apiKey->id], null, $instance);
        $apiKey->delete();

        return redirect()->route('owner.instances.api-keys', $instance)
            ->with('success', "API Key \"{$name}\" eliminada permanentemente.");
    }


    // --- Cuentas Bancarias (Owner) ---

    public function cuentasBancarias(Request $request)
    {
        $query = CuentaBancaria::query();

        // Filtro por instancia
        if ($instanceId = $request->input('instance_id')) {
            $query->where('tenant_id', $instanceId);
        }

        // Busqueda
        if ($buscar = $request->input('buscar')) {
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', '%'.$buscar.'%')
                    ->orWhere('banco', 'like', '%'.$buscar.'%')
                    ->orWhere('numero_cuenta', 'like', '%'.$buscar.'%')
                    ->orWhere('titular', 'like', '%'.$buscar.'%');
            });
        }

        // Incluir inactivas
        if ($request->input('incluir_inactivos') !== '1') {
            $query->activo();
        }

        $cuentas = $query->with('tenant')->latest()->paginate(15)->withQueryString();

        $instances = BusinessInstance::orderBy('nombre')->get();

        $stats = [
            'total' => CuentaBancaria::count(),
            'activas' => CuentaBancaria::activo()->count(),
            'instancias_con_cuentas' => CuentaBancaria::distinct('tenant_id')->count(),
        ];

        return view('owner.cuentas-bancarias.index', compact('cuentas', 'instances', 'stats'));
    }

    // ─── SMTP Configuration (Owner Only) ─────────────────────────────

    public function smtpSettings()
    {
        $settings = [
            'mail_mailer'       => SystemSetting::get('mail_mailer', 'smtp'),
            'mail_host'         => SystemSetting::get('mail_host', ''),
            'mail_port'         => SystemSetting::get('mail_port', '465'),
            'mail_username'     => SystemSetting::get('mail_username', ''),
            'mail_password'     => SystemSetting::get('mail_password', ''),
            'mail_encryption'   => SystemSetting::get('mail_encryption', 'ssl'),
            'mail_from_address' => SystemSetting::get('mail_from_address', ''),
            'mail_from_name'    => SystemSetting::get('mail_from_name', ''),
            'error_alert_email' => SystemSetting::get('error_alert_email', ''),
        ];

        return view('owner.smtp-settings', compact('settings'));
    }

    public function smtpSettingsUpdate(Request $request)
    {
        $data = $request->validate([
            'mail_mailer'       => 'nullable|string|in:smtp,log,mail,sendmail',
            'mail_host'         => 'nullable|string|max:255',
            'mail_port'         => 'nullable|string|max:10',
            'mail_username'     => 'nullable|string|max:255',
            'mail_password'     => 'nullable|string|max:255',
            'mail_encryption'   => 'nullable|string|in:tls,ssl,null|max:10',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name'    => 'nullable|string|max:255',
            'error_alert_email' => 'nullable|email|max:255',
        ]);

        $mailKeys = ['mail_mailer', 'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'];

        foreach ($mailKeys as $key) {
            if (array_key_exists($key, $data)) {
                $value = $data[$key];

                // Skip password if left blank (keep existing)
                if ($key === 'mail_password' && ($value === null || $value === '')) {
                    continue;
                }

                // Encrypt password (same method as seeder and ErrorMailer)
                if ($key === 'mail_password' && !empty($value)) {
                    $value = \Illuminate\Support\Facades\Crypt::encryptString($value);
                }

                // Convert null to empty string (value column is NOT NULL)
                if ($value === null) {
                    $value = '';
                }

                SystemSetting::updateOrCreate(
                    ['key' => $key, 'tenant_id' => null],
                    ['value' => $value]
                );
            }
        }

        // Save error_alert_email separately (not encrypted)
        if (array_key_exists('error_alert_email', $data)) {
            $value = $data['error_alert_email'];
            if ($value === null) {
                $value = '';
            }
            SystemSetting::updateOrCreate(
                ['key' => 'error_alert_email', 'tenant_id' => null],
                ['value' => $value]
            );
        }

        Cache::forget('system_settings_all_global');

        $this->logOwnerAction('SMTP_UPDATE', 'Configuración SMTP global actualizada');

        return redirect()->route('owner.smtp-settings')
            ->with('success', 'Configuración SMTP guardada correctamente.');
    }

    public function smtpSettingsTest(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $testEmail = $request->input('test_email');

        try {
            // Safely decrypt password (may be plaintext if saved before encryption was added)
            $rawPassword = SystemSetting::get('mail_password', '');
            try {
                $decryptedPassword = \Illuminate\Support\Facades\Crypt::decryptString($rawPassword);
            } catch (\Exception $e) {
                try {
                    $decryptedPassword = decrypt($rawPassword);
                } catch (\Exception $e2) {
                    $decryptedPassword = $rawPassword;
                }
            }

            // Temporarily override mail config
            config([
                'mail.default' => SystemSetting::get('mail_mailer', 'smtp'),
                'mail.mailers.smtp.host' => SystemSetting::get('mail_host', ''),
                'mail.mailers.smtp.port' => SystemSetting::get('mail_port', '465'),
                'mail.mailers.smtp.username' => SystemSetting::get('mail_username', ''),
                'mail.mailers.smtp.password' => $decryptedPassword,
                'mail.mailers.smtp.encryption' => SystemSetting::get('mail_encryption', 'ssl'),
                'mail.from.address' => SystemSetting::get('mail_from_address', ''),
                'mail.from.name' => SystemSetting::get('mail_from_name', ''),
            ]);

            \Illuminate\Support\Facades\Mail::raw('Este es un correo de prueba desde el sistema.', function ($message) use ($testEmail) {
                $message->to($testEmail)
                    ->subject('Prueba de Configuración SMTP');
            });

            return redirect()->route('owner.smtp-settings')
                ->with('success', "Correo de prueba enviado exitosamente a {$testEmail}.");
        } catch (\Throwable $e) {
            return redirect()->route('owner.smtp-settings')
                ->with('error', 'Error al enviar correo de prueba: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────
    //  Historial de Actividad de Usuarios
    // ─────────────────────────────────────────────────────────

    public function activityHistory(Request $request)
    {
        $query = UserActivityLog::with('user.businessInstance', 'user.sucursal')
            ->when($request->filled('start_date') && $request->filled('end_date'), function ($q) use ($request) {
                $q->whereBetween('logged_at', [$request->start_date, $request->end_date . ' 23:59:59']);
            })
            ->when($request->filled('action'), function ($q) use ($request) {
                $q->where('action', $request->action);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('user', function ($q2) use ($request) {
                    $q2->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%");
                });
            })
            ->latest('logged_at');

        $logs = $query->paginate(50)->withQueryString();

        $todayStats = [
            'logins_today' => UserActivityLog::today()->where('action', 'login')->count(),
            'logouts_today' => UserActivityLog::today()->where('action', 'logout')->count(),
            'views_today' => UserActivityLog::today()->where('action', 'page_view')->count(),
            'unique_active' => UserActivityLog::today()
                ->where('action', 'login')
                ->distinct('user_id')
                ->count('user_id'),
        ];

        return view('owner.activity-history', compact('logs', 'todayStats'));
    }

    public function activityHistoryJson(Request $request)
    {
        $request->validate([
            'limit' => 'sometimes|integer|min:1|max:100',
            'action' => 'sometimes|in:login,logout,page_view',
            'since' => 'sometimes|date',
        ]);

        $limit = $request->input('limit', 20);

        $query = UserActivityLog::with('user:name,email,business_instance_id,sucursal_id,instanceRole')
            ->when($request->filled('action'), function ($q) use ($request) {
                $q->where('action', $request->action);
            })
            ->when($request->filled('since'), function ($q) use ($request) {
                $q->where('logged_at', '>=', $request->since);
            })
            ->latest('logged_at')
            ->limit($limit);

        $logs = $query->get()->map(function ($log) {
            return [
                'id' => $log->id,
                'user_name' => $log->user->name,
                'user_email' => $log->user->email,
                'action' => $log->action,
                'ip_address' => $log->ip_address,
                'logged_at' => $log->logged_at->format('Y-m-d H:i:s'),
                'logged_at_human' => $log->logged_at->diffForHumans(),
                'instance' => $log->user->businessInstance?->nombre ?? 'N/A',
                'sucursel' => $log->user->sucursal?->nombre ?? 'N/A',
            ];
        });

        return response()->json(['data' => $logs]);
    }

    public function clearHistory(Request $request)
    {
        $request->validate(['days' => 'sometimes|integer|min:1|max:365']);
        $days = $request->input('days', 30);

        $count = UserActivityLog::where('logged_at', '<', now()->subDays($days))->delete();

        $this->logOwnerAction('ACTIVITY_CLEAR', "Historial de actividad limpiado: {$count} registros eliminados (anteriores a {$days} días).");

        return redirect()->route('owner.activity.history')
            ->with('success', "Se eliminaron {$count} registros anteriores a {$days} días.");
    }

    // ─── Platform Owners Management ──────────────────────────────────

    public function ownersIndex()
    {
        $owners = User::role('owner')
            ->withCount(['businessInstances', 'assignedInstances'])
            ->latest()
            ->paginate(15);

        $totalOwners = User::role('owner')->count();
        $totalInstances = BusinessInstance::count();
        $activeInstances = BusinessInstance::where('activo', true)->count();

        return view('owner.owners.index', compact(
            'owners',
            'totalOwners',
            'totalInstances',
            'activeInstances'
        ));
    }

    public function ownersCreate()
    {
        return view('owner.owners.create');
    }

    public function ownersStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:12|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'owner',
        ]);

        $user->assignRole('owner');

        $this->logOwnerAction('OWNER_CREATE', "Nuevo dueño de plataforma creado: {$user->name}", null, ['user_id' => $user->id], $user);

        return redirect()->route('owner.owners.index')
            ->with('success', "Dueño de plataforma '{$user->name}' creado correctamente.");
    }

    public function ownersEdit($id)
    {
        $owner = User::role('owner')->findOrFail($id);
        return view('owner.owners.edit', compact('owner'));
    }

    public function ownersUpdate(Request $request, $id)
    {
        $owner = User::role('owner')->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $owner->id,
            'password' => 'nullable|string|min:12|confirmed',
        ]);

        $oldData = $owner->getAttributes();
        $owner->name = $data['name'];
        $owner->email = $data['email'];

        if (!empty($data['password'])) {
            $owner->password = Hash::make($data['password']);
        }

        $owner->save();

        $this->logOwnerAction('OWNER_UPDATE', "Dueño de plataforma actualizado: {$owner->name}", $oldData, $owner->getAttributes(), $owner);

        return redirect()->route('owner.owners.index')
            ->with('success', "Dueño de plataforma '{$owner->name}' actualizado correctamente.");
    }

    public function ownersDestroy($id)
    {
        $owner = User::role('owner')->findOrFail($id);

        if ($owner->id === auth()->id()) {
            return redirect()->route('owner.owners.index')
                ->with('error', 'No puedes eliminar tu propio cuenta.');
        }

        $linkedInstances = BusinessInstance::where('owner_user_id', $owner->id)->count();
        if ($linkedInstances > 0) {
            return redirect()->route('owner.owners.index')
                ->with('error', "No puedes eliminar '{$owner->name}' porque tiene {$linkedInstances} instancia(s) vinculada(s). Desvincula las instancias primero.");
        }

        $name = $owner->name;
        $this->logOwnerAction('OWNER_DELETE', "Dueño de plataforma eliminado: {$name}", ['user_id' => $owner->id], null, $owner);
        $owner->delete();

        return redirect()->route('owner.owners.index')
            ->with('success', "Dueño de plataforma '{$name}' eliminado correctamente.");
    }

    // ──────────────────────────────────────────────
    // AUDITORÍA — Registro de acciones del Owner
    // ──────────────────────────────────────────────

    public function auditLogsIndex(Request $request)
    {
        $query = \App\Models\AuditLog::with('user')
            ->where(function ($q) {
                // Owner ve TODO o solo sus propias acciones
                $q->whereNull('tenant_id')
                  ->orWhere('tenant_id', auth()->user()->businessInstance_id);
            })
            ->latest();

        if ($request->filled('action')) {
            $query->ofAction($request->action);
        }
        if ($request->filled('model')) {
            $query->where('model_type', 'like', '%' . $request->model);
        }
        if ($request->filled('user_id')) {
            $query->ofUser($request->user_id);
        }
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('description', 'like', "%{$s}%")
                  ->orWhere('action', 'like', "%{$s}%");
        }

        $logs = $query->paginate(50);
        $actions = \App\Models\AuditLog::distinct('action')->pluck('action');
        $models = \App\Models\AuditLog::distinct('model_type')->pluck('model_type')
                   ->map(fn($m) => class_basename($m));
        $users = User::whereHas('roles', fn($q) => $q->whereIn('name', ['owner', 'admin-business', 'admin']))
                     ->get(['id', 'name']);

        return view('owner.audit-logs.index', compact('logs', 'actions', 'models', 'users'));
    }

    public function auditLogsShow(\App\Models\AuditLog $auditLog)
    {
        $auditLog->load('user');
        return view('owner.audit-logs.show', compact('auditLog'));
    }

    public function clearAuditLogs(Request $request)
    {
        $days = $request->input('days', 30);
        $cutOff = now()->subDays($days);

        $count = \App\Models\AuditLog::where('created_at', '<', $cutOff)
            ->where(function ($q) {
                $q->whereNull('tenant_id')
                  ->orWhere('tenant_id', auth()->user()->businessInstance_id);
            })
            ->delete();

        $this->logOwnerAction('AUDIT_LOG_CLEAR', "Historial de auditoría limpiado: {$count} registros eliminados (anteriores a {$days} días).");

        return redirect()->route('owner.audit-logs.index')
            ->with('success', "{$count} registros antiguos eliminados correctamente.");
    }
}