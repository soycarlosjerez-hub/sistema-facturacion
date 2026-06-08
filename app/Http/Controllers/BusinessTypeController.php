<?php

namespace App\Http\Controllers;

use App\Models\BusinessType;
use App\Models\BusinessTypeModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BusinessTypeController extends Controller
{
    public function index()
    {
        $tipos = BusinessType::with('modules')->orderBy('orden')->get();
        $modulosDisponibles = $this->getModulosDisponibles();
        
        return view('business-types.index', compact('tipos', 'modulosDisponibles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string|max:50|unique:business_types,slug',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'color' => 'required|string|in:primary,secondary,success,danger,warning,info,light,dark',
            'icon' => 'required|string|max:50',
            'activo' => 'boolean',
            'orden' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $tipo = BusinessType::create($request->only(['slug', 'nombre', 'descripcion', 'color', 'icon', 'activo', 'orden']));

        return redirect()->route('business-types.index')
            ->with('success', 'Tipo de negocio creado correctamente.');
    }

    public function update(Request $request, BusinessType $businessType)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string|max:50|unique:business_types,slug,' . $businessType->id,
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'color' => 'required|string|in:primary,secondary,success,danger,warning,info,light,dark',
            'icon' => 'required|string|max:50',
            'activo' => 'boolean',
            'orden' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $businessType->update($request->only(['slug', 'nombre', 'descripcion', 'color', 'icon', 'activo', 'orden']));

        return redirect()->route('business-types.index')
            ->with('success', 'Tipo de negocio actualizado correctamente.');
    }

    public function destroy(BusinessType $businessType)
    {
        $businessType->delete();

        return redirect()->route('business-types.index')
            ->with('success', 'Tipo de negocio eliminado correctamente.');
    }

    public function updateModules(Request $request, BusinessType $businessType)
    {
        $payload = $request->isJson() ? $request->json()->all() : $request->all();
        $modulos = $payload['modulos'] ?? [];

        BusinessTypeModule::where('business_type_id', $businessType->id)->delete();

        foreach ($modulos as $moduloKey => $data) {
            if (!is_string($moduloKey) || is_numeric($moduloKey)) {
                continue;
            }
            BusinessTypeModule::create([
                'business_type_id' => $businessType->id,
                'modulo_key' => $moduloKey,
                'visible' => is_array($data) ? ($data['visible'] ?? true) : true,
                'orden' => is_array($data) ? ($data['orden'] ?? 0) : 0,
            ]);
        }

        BusinessType::flush();

        return response()->json(['success' => true, 'message' => 'Módulos actualizados correctamente.']);
    }

    public function modulesData(BusinessType $businessType)
    {
        $modulos = $businessType->modules()->pluck('modulo_key')->toArray();

        return response()->json(['modulos' => $modulos]);
    }

    private function getModulosDisponibles()
    {
        return \App\Models\Modulo::where('activo', true)
            ->orderBy('orden')
            ->get()
            ->keyBy('key')
            ->map(fn($m) => ['label' => $m->label, 'icon' => $m->icon, 'categoria' => $m->categoria])
            ->toArray();
    }
}