<?php

namespace App\Http\Controllers;

use App\Models\BusinessType;
use App\Models\BusinessTypeModule;
use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class OwnerBusinessTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

    private function logOwnerAction(string $action, string $description, ?array $oldValues = null, ?array $newValues = null, ?Model $model = null): void
    {
        try {
            DB::table('audit_logs')->insert([
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
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to log owner action: ' . $e->getMessage());
        }
    }

    /**
     * Listar todos los tipos de negocio
     */
    public function index(): \Illuminate\View\View
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

    /**
     * Mostrar el formulario de creación
     */
    public function create(): \Illuminate\View\View
    {
        $allModules = Modulo::where('activo', true)->orderBy('orden')->get();
        return view('owner.business-types.create', compact('allModules'));
    }

    /**
     * Almacenar un nuevo tipo de negocio
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
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

        $this->logOwnerAction('BUSINESS_TYPE_CREATE', "Tipo de negocio \"{$businessType->nombre}\" creado", null, ['id' => $businessType->id, 'slug' => $businessType->slug], $businessType);

        return redirect()->route('owner.business-types.index')
            ->with('success', "Tipo de negocio \"{$businessType->nombre}\" creado correctamente.");
    }

    /**
     * Mostrar el formulario de edición
     */
    public function edit($id): \Illuminate\View\View
    {
        $businessType = BusinessType::with('modules')->findOrFail($id);
        $allModules = Modulo::where('activo', true)->orderBy('orden')->get();
        return view('owner.business-types.edit', compact('businessType', 'allModules'));
    }

    /**
     * Actualizar un tipo de negocio
     */
    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
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
            'facturacion_modo' => 'nullable|string|in:productos,obras_arte,equipos,productos_y_servicios,productos_y_equipos',
        ]);

        $oldData = $businessType->getAttributes();

        $businessType->update([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'color' => $data['color'] ?? null,
            'icon' => $data['icon'] ?? null,
            'activo' => $request->boolean('activo', true),
            'orden' => $data['orden'] ?? 0,
            'config' => array_merge($businessType->config ?? [], [
                'facturacion_modo' => $data['facturacion_modo'] ?? 'productos',
            ]),
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

        $this->logOwnerAction('BUSINESS_TYPE_UPDATE', "Tipo de negocio \"{$businessType->nombre}\" actualizado", $oldData, $businessType->getAttributes(), $businessType);

        return redirect()->route('owner.business-types.index')
            ->with('success', 'Tipo de negocio actualizado correctamente.');
    }

    /**
     * Eliminar un tipo de negocio
     */
    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        $businessType = BusinessType::findOrFail($id);

        $instancesCount = $businessType->businessInstances()->count();
        if ($instancesCount > 0) {
            return back()->with('error', "No se puede eliminar \"{$businessType->nombre}\" porque {$instancesCount} instancia(s) lo están usando.");
        }

        $this->logOwnerAction('BUSINESS_TYPE_DELETE', "Tipo de negocio \"{$businessType->nombre}\" eliminado", ['id' => $businessType->id], null, $businessType);

        $businessType->modules()->delete();
        $businessType->delete();
        BusinessType::flush();

        return redirect()->route('owner.business-types.index')
            ->with('success', "Tipo de negocio \"{$businessType->nombre}\" eliminado.");
    }
}
