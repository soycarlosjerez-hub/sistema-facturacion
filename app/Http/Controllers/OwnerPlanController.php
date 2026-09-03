<?php

namespace App\Http\Controllers;

use App\Models\BusinessType;
use App\Models\BusinessTypeModule;
use App\Models\Modulo;
use App\Models\Plan;
use App\Models\BusinessInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class OwnerPlanController extends Controller
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
     * Listar todos los planes
     */
    public function index(): \Illuminate\View\View
    {
        $planes = Plan::withCount('businessInstances')->orderBy('orden')->get();

        return view('owner.planes.index', compact('planes'));
    }

    /**
     * Mostrar el formulario de creación
     */
    public function create(): \Illuminate\View\View
    {
        $modulos = Modulo::allActive();
        $businessTypes = BusinessType::where('activo', true)->orderBy('orden')->get();

        return view('owner.planes.create', compact('modulos', 'businessTypes'));
    }

    /**
     * Almacenar un nuevo plan
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $this->validatePlan($request);

        Plan::create($data);
        Plan::flush();

        return redirect()->route('owner.planes.index')
            ->with('success', 'Plan creado correctamente.');
    }

    /**
     * Mostrar el formulario de edición
     */
    public function edit($id): \Illuminate\View\View
    {
        $plan = Plan::findOrFail($id);
        $modulos = Modulo::allActive();
        $businessTypes = BusinessType::where('activo', true)->orderBy('orden')->get();

        // Pre-seleccionar business type si los módulos coinciden exactamente
        $planModulos = $plan->modulos ?? [];
        $preSelectedBusinessType = null;
        if (!empty($planModulos)) {
            foreach ($businessTypes as $bt) {
                $btModulos = $bt->modules()->pluck('modulo_key')->toArray();
                if (count($btModulos) === count($planModulos) && count(array_diff($btModulos, $planModulos)) === 0) {
                    $preSelectedBusinessType = $bt->id;
                    break;
                }
            }
        }

        return view('owner.planes.edit', compact('plan', 'modulos', 'businessTypes', 'preSelectedBusinessType'));
    }

    /**
     * Actualizar un plan
     */
    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $plan = Plan::findOrFail($id);

        $data = $this->validatePlan($request, $plan);

        $plan->update($data);

        Plan::flush();

        return redirect()->route('owner.planes.index')
            ->with('success', 'Plan actualizado correctamente.');
    }

    /**
     * Eliminar un plan
     */
    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        $plan = Plan::findOrFail($id);

        $inUse = BusinessInstance::where('plan_id', $plan->id)->count();

        if ($inUse > 0) {
            return back()->with('error', "No se puede eliminar el plan \"{$plan->nombre}\" porque está asignado a {$inUse} instancia(s). Desactívelo en su lugar.");
        }

        Plan::flush();
        $plan->delete();

        return redirect()->route('owner.planes.index')
            ->with('success', 'Plan eliminado correctamente.');
    }

    /**
     * Validar y sanitizar los datos de un plan
     */
    private function validatePlan(Request $request, ?Plan $plan = null): array
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
}
