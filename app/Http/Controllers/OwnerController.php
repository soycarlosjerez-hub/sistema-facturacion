<?php

namespace App\Http\Controllers;

use App\Models\BusinessInstance;
use App\Models\BusinessType;
use App\Models\BusinessTypeModule;
use App\Models\InstanceApiKey;
use App\Models\InstanceErrorLog;
use App\Models\InstanceRole;
use App\Models\InstanceRoleModule;
use App\Models\Modulo;
use App\Models\PagoInstancia;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\UserBusinessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;

class OwnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:owner');
    }

    public function index()
    {
        $totalInstancias = BusinessInstance::count();
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

        $totalTipos = BusinessType::count();
        $totalUsuarios = User::count();

        return view('owner.dashboard', compact(
            'totalInstancias', 'activas', 'bloqueadas', 'vencidas', 'porVencer',
            'instancias', 'instanciasPorTipo', 'instanciasConAtraso',
            'proximosVencimientos', 'ingresosEsperados', 'ingresosRealesMes',
            'totalTipos', 'totalUsuarios'
        ));
    }

    public function businessTypes()
    {
        $businessTypes = BusinessType::with('modules')->orderBy('orden')->get();
        $allModules = Modulo::where('activo', true)->orderBy('orden')->get();
        return view('owner.business-types.index', compact('businessTypes', 'allModules'));
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

    public function instances()
    {
        $instances = BusinessInstance::with(['businessType', 'owner', 'ultimoPago'])
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
        return view('owner.instances.create', compact('businessTypes', 'owners'));
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
            $rules['user_password'] = 'required|string|min:6|confirmed';
            $rules['user_role'] = ['required', 'string', Rule::in($allRoles)];
        }

        $data = $request->validate($rules);

        $instance = BusinessInstance::create([
            'nombre' => $data['nombre'],
            'slug' => Str::slug($data['slug']),
            'rnc' => $data['rnc'] ?? null,
            'email' => $data['email'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'business_type_id' => $data['business_type_id'],
            'owner_user_id' => $data['owner_user_id'] ?? auth()->id(),
            'costo_mensual' => $data['costo_mensual'] ?? null,
            'fecha_vencimiento' => $data['fecha_vencimiento'] ?? null,
            'activo' => $request->boolean('activo', true),
            'configuracion' => [],
        ]);

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

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', 'Instancia creada correctamente.');
    }

    public function instancesShow($id)
    {
        $instance = BusinessInstance::with(['businessType', 'owner', 'users.tokens', 'ultimoPago'])
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
        $instance = BusinessInstance::findOrFail($id);
        $businessTypes = BusinessType::where('activo', true)->orderBy('nombre')->get();
        $owners = User::role('owner')->orderBy('name')->get();
        return view('owner.instances.edit', compact('instance', 'businessTypes', 'owners'));
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
            'owner_user_id' => 'nullable|exists:users,id',
            'costo_mensual' => 'nullable|numeric|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'activo' => 'boolean',
        ]);

        $instance->update([
            'nombre' => $data['nombre'],
            'rnc' => $data['rnc'] ?? null,
            'email' => $data['email'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'business_type_id' => $data['business_type_id'],
            'owner_user_id' => $data['owner_user_id'] ?? $instance->owner_user_id,
            'costo_mensual' => $data['costo_mensual'] ?? null,
            'fecha_vencimiento' => $data['fecha_vencimiento'] ?? null,
            'activo' => $request->boolean('activo', true),
        ]);

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', 'Instancia actualizada correctamente.');
    }

    public function instancesDestroy($id)
    {
        $instance = BusinessInstance::findOrFail($id);
        $instance->update(['activo' => false]);

        return redirect()->route('owner.instances.index')
            ->with('success', 'Instancia desactivada correctamente.');
    }

    public function instancesConfig($id)
    {
        $instance = BusinessInstance::with('businessType')->findOrFail($id);
        $globalSettings = [
            'nombre_empresa' => SystemSetting::get('nombre_empresa', ''),
            'slogan' => SystemSetting::get('slogan', ''),
            'moneda_simbolo' => SystemSetting::get('moneda_simbolo', 'RD$'),
            'itbis_porcentaje' => SystemSetting::get('itbis_porcentaje', 18),
            'prefijo_factura' => SystemSetting::get('prefijo_factura', 'FAC-'),
            'prefijo_ncf' => SystemSetting::get('prefijo_ncf', ''),
            'dias_credito' => SystemSetting::get('dias_credito', 30),
        ];
        $instanceConfig = $instance->configuracion ?? [];

        return view('owner.instances.config', compact('instance', 'globalSettings', 'instanceConfig'));
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
            'restaurante_valida_stock' => 'nullable|string',
        ]);

        $data['restaurante_valida_stock'] = $request->has('restaurante_valida_stock') ? '1' : '0';

        $existingConfig = $instance->configuracion ?? [];
        $mergedConfig = array_merge($existingConfig, array_filter($data, fn($v) => !is_null($v)));

        $instance->update(['configuracion' => $mergedConfig]);

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', 'Configuración de instancia actualizada correctamente.');
    }

    public function cleanInstance(Request $request, $id)
    {
        $instance = BusinessInstance::findOrFail($id);

        $request->validate([
            'confirm_name' => 'required|string|in:' . $instance->nombre,
        ]);

        $tenantId = $instance->id;

        DB::transaction(function () use ($tenantId) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Orden: detalles primero, luego cabeceras, luego maestros
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
                'mesa_ubicaciones',  // nueva
                'mesa_categorias',
                'categories',        // categorías extra (restaurante/lavadero)

                // Lavadero
                'lavadero_citas',
                'lavadero_servicios',
                'lavadores',

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
                // Verificar que la tabla exista antes de intentar limpiar
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    DB::table($table)->where('tenant_id', $tenantId)->delete();
                }
            }

            // Resetear setup_completed para que vuelva a hacer el wizard
            \App\Models\BusinessInstance::where('id', $tenantId)->update([
                'setup_completed' => false,
            ]);

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        });

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', "Todos los datos operacionales de {$instance->nombre} han sido eliminados. El wizard de configuración se reiniciará en el próximo inicio de sesión.");

    }

    public function alternarBloqueo(Request $request, $id)
    {
        $instance = BusinessInstance::findOrFail($id);

        $data = $request->validate([
            'bloqueado' => 'required|boolean',
            'motivo_bloqueo' => 'required_if:bloqueado,1|string|max:500',
        ]);

        $instance->update([
            'bloqueado' => $data['bloqueado'],
            'motivo_bloqueo' => $data['bloqueado'] ? $data['motivo_bloqueo'] : null,
            'bloqueado_en' => $data['bloqueado'] ? now() : null,
        ]);

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
        $instance = BusinessInstance::findOrFail($id);

        $data = $request->validate([
            'monto' => 'required|numeric|min:0',
            'mes_pagado' => 'required|date_format:Y-m-d',
            'metodo_pago' => 'nullable|string|max:100',
            'notas' => 'nullable|string|max:500',
        ]);

        PagoInstancia::create([
            'business_instance_id' => $instance->id,
            'monto' => $data['monto'],
            'mes_pagado' => $data['mes_pagado'],
            'fecha_pago' => now(),
            'metodo_pago' => $data['metodo_pago'],
            'notas' => $data['notas'],
            'registrado_por' => auth()->id(),
        ]);

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

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'instance_role_id' => 'nullable|exists:instance_roles,id',
        ]);

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
            'password' => 'nullable|string|min:6|confirmed',
            'instance_role_id' => 'nullable|exists:instance_roles,id',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->instance_role_id = $data['instance_role_id'] ?? null;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

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

        return redirect()->route('owner.instances.roles', $instance)
            ->with('success', "Rol '{$role->name}' creado correctamente.");
    }

    public function instanceRolesEdit($id, $roleId)
    {
        $instance = BusinessInstance::findOrFail($id);
        $role = InstanceRole::where('business_instance_id', $instance->id)
            ->with('modules')->findOrFail($roleId);
        $modulos = Modulo::allActive()->groupBy('categoria');
        $totalModulos = Modulo::allActive()->count();
        $selectedModulos = $role->modules->where('is_visible', true)->pluck('modulo_key')->toArray();
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

        $abilities = $request->input('abilities', ['*']);
        $token = $user->createToken($data['name'], (array) $abilities);

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
        $apiKey->delete();

        return redirect()->route('owner.instances.api-keys', $instance)
            ->with('success', "API Key \"{$name}\" eliminada permanentemente.");
    }
}
