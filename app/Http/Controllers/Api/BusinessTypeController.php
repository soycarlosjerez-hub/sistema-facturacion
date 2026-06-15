<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use Illuminate\Http\Request;

class BusinessTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = BusinessType::with('modules')->where('activo', true);
        
        if ($request->boolean('with_categories')) {
            $query->withCount('categories');
        }

        return $query->orderBy('orden')->get();
    }

    public function store(Request $request)
    {
        $this->authorize('manage', BusinessType::class);
        
        $validated = $request->validate([
            'key' => 'required|string|max:50|unique:business_types,key',
            'slug' => 'required|string|max:50|unique:business_types,slug',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:50',
            'color_default' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'icon' => 'nullable|string|max:50',
            'icono_default' => ['nullable', 'string|max:50'],
            'activo' => 'boolean',
            'orden' => 'integer|min:0',
            'campos_extra' => 'nullable|array',
            'soft_delete_default' => 'boolean',
        ]);

        $bt = BusinessType::create($validated);
        BusinessType::flush();

        return response()->json($bt, 201);
    }

    public function show(BusinessType $businessType)
    {
        return $businessType->load('modules');
    }

    public function update(Request $request, BusinessType $businessType)
    {
        $this->authorize('manage', BusinessType::class);

        $validated = $request->validate([
            'key' => 'sometimes|string|max:50|unique:business_types,key,' . $businessType->id,
            'slug' => 'sometimes|string|max:50|unique:business_types,slug,' . $businessType->id,
            'nombre' => 'sometimes|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:50',
            'color_default' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'icon' => 'nullable|string|max:50',
            'icono_default' => ['nullable', 'string|max:50'],
            'activo' => 'boolean',
            'orden' => 'integer|min:0',
            'campos_extra' => 'nullable|array',
            'soft_delete_default' => 'boolean',
        ]);

        $businessType->update($validated);
        BusinessType::flush();

        return response()->json($businessType->fresh());
    }

    public function destroy(BusinessType $businessType)
    {
        $this->authorize('manage', BusinessType::class);

        // Check if any categories are linked
        if ($businessType->categories()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el tipo de negocio porque tiene categorías asociadas.',
            ], 422);
        }

        $businessType->delete();
        BusinessType::flush();

        return response()->json(['message' => 'Tipo de negocio eliminado.']);
    }
}