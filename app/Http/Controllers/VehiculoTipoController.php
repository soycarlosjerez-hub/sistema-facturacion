<?php

namespace App\Http\Controllers;

use App\Models\VehiculoTipo;
use Illuminate\Http\Request;

class VehiculoTipoController extends Controller
{
    public function index()
    {
        $tipos = VehiculoTipo::orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return view('vehiculo-tipos.index', compact('tipos'));
    }

    public function create()
    {
        return view('vehiculo-tipos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:50|unique:vehiculo_tipos,nombre',
            'slug'   => 'nullable|string|max:50|unique:vehiculo_tipos,slug',
            'icono'  => 'nullable|string|max:50',
            'color'  => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'activo' => 'boolean',
            'orden'  => 'nullable|integer|min:0',
        ]);

        $data['activo'] = $request->boolean('activo', true);
        $data['tenant_id'] = auth()->user()->business_instance_id;

        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['nombre']);
        }

        if (empty($data['color'])) {
            $data['color'] = $this->getRandomColor();
        }

        VehiculoTipo::create($data);

        return redirect()->route('vehiculo-tipos.index')
            ->with('success', 'Tipo de vehículo creado correctamente.');
    }

    public function show(VehiculoTipo $vehiculoTipo)
    {
        $vehiculoTipo->loadCount('vehiculos');
        return view('vehiculo-tipos.show', compact('vehiculoTipo'));
    }

    public function edit(VehiculoTipo $vehiculoTipo)
    {
        return view('vehiculo-tipos.edit', compact('vehiculoTipo'));
    }

    public function update(Request $request, VehiculoTipo $vehiculoTipo)
    {
        $nombreRules = 'required|string|max:50';
        $slugRules = 'nullable|string|max:50';

        $tenantId = auth()->user()->business_instance_id;
        if ($tenantId) {
            $nombreRules .= '|unique:vehiculo_tipos,nombre,' . $vehiculoTipo->id . ',id,tenant_id,' . $tenantId;
            $slugRules .= '|unique:vehiculo_tipos,slug,' . $vehiculoTipo->id . ',id,tenant_id,' . $tenantId;
        } else {
            $nombreRules .= '|unique:vehiculo_tipos,nombre,' . $vehiculoTipo->id;
            $slugRules .= '|unique:vehiculo_tipos,slug,' . $vehiculoTipo->id;
        }

        $data = $request->validate([
            'nombre' => $nombreRules,
            'slug'   => $slugRules,
            'icono'  => 'nullable|string|max:50',
            'color'  => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'activo' => 'boolean',
            'orden'  => 'nullable|integer|min:0',
        ]);

        $data['activo'] = $request->boolean('activo', true);

        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['nombre']);
        }

        $vehiculoTipo->update($data);

        return redirect()->route('vehiculo-tipos.index')
            ->with('success', 'Tipo de vehículo actualizado correctamente.');
    }

    public function destroy(VehiculoTipo $vehiculoTipo)
    {
        if ($vehiculoTipo->vehiculos()->exists()) {
            return back()->with('error', 'No se puede eliminar porque tiene vehículos asociados.');
        }

        $vehiculoTipo->delete();
        return redirect()->route('vehiculo-tipos.index')
            ->with('success', 'Tipo de vehículo eliminado correctamente.');
    }

    public function toggleActivo(VehiculoTipo $vehiculoTipo)
    {
        $vehiculoTipo->update(['activo' => !$vehiculoTipo->activo]);

        return response()->json([
            'success' => true,
            'activo'  => $vehiculoTipo->fresh()->activo,
            'label'   => $vehiculoTipo->activo ? 'Activo' : 'Inactivo',
        ]);
    }

    protected function getRandomColor(): string
    {
        $colors = [
            '#E74C3C', '#3498DB', '#2ECC71', '#F39C12',
            '#9B59B6', '#1ABC9C', '#E67E22', '#34495E',
        ];
        return $colors[array_rand($colors)];
    }
}
