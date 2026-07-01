<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessInstanceResource;
use App\Models\BusinessInstance;
use Illuminate\Http\Request;

class BusinessInstanceController extends Controller
{
    public function index(Request $request)
    {
        $query = BusinessInstance::with(['businessType', 'owner', 'users', 'sucursales', 'modules'])
            ->when($request->boolean('active'), fn ($q) => $q->where('activo', true))
            ->when($request->boolean('al_dia'), fn ($q) => $q->alDia())
            ->when($request->boolean('bloqueadas'), fn ($q) => $q->bloqueadas())
            ->when($request->owner_id, fn ($q) => $q->ownedBy($request->owner_id));

        return BusinessInstanceResource::collection($query->orderBy('created_at', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:business_instances,slug',
            'rnc' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'business_type_id' => 'required|exists:business_types,id',
            'owner_user_id' => 'nullable|exists:users,id',
            'configuracion' => 'nullable|array',
            'activo' => 'boolean',
            'fecha_vencimiento' => 'nullable|date',
            'costo_mensual' => 'nullable|numeric|min:0',
            'bloqueado' => 'boolean',
            'motivo_bloqueo' => 'nullable|string|max:500',
            'setup_completed' => 'boolean',
        ]);

        $instance = BusinessInstance::create($validated);

        return new BusinessInstanceResource($instance->load(['businessType', 'owner']));
    }

    public function show(BusinessInstance $businessInstance)
    {
        return new BusinessInstanceResource($businessInstance->load([
            'businessType.modules',
            'owner',
            'users',
            'sucursales',
            'modules',
            'ultimoPago',
        ]));
    }

    public function update(Request $request, BusinessInstance $businessInstance)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:business_instances,slug,' . $businessInstance->id,
            'rnc' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'business_type_id' => 'sometimes|exists:business_types,id',
            'owner_user_id' => 'nullable|exists:users,id',
            'configuracion' => 'nullable|array',
            'activo' => 'boolean',
            'fecha_vencimiento' => 'nullable|date',
            'costo_mensual' => 'nullable|numeric|min:0',
            'bloqueado' => 'boolean',
            'motivo_bloqueo' => 'nullable|string|max:500',
            'setup_completed' => 'boolean',
        ]);

        $businessInstance->update($validated);

        return new BusinessInstanceResource($businessInstance->load(['businessType', 'owner']));
    }

    public function destroy(BusinessInstance $businessInstance)
    {
        $businessInstance->delete();
        return response()->json(['message' => 'Business instance deleted.']);
    }

    public function toggleModule(Request $request, BusinessInstance $businessInstance)
    {
        $validated = $request->validate([
            'modulo_key' => 'required|string',
            'visible' => 'required|boolean',
            'orden' => 'nullable|integer|min:0',
        ]);

        $module = $businessInstance->modules()->updateOrCreate(
            ['modulo_key' => $validated['modulo_key']],
            ['visible' => $validated['visible'], 'orden' => $validated['orden'] ?? 0]
        );

        return new BusinessInstanceResource($businessInstance->load('modules'));
    }
}
