<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SystemSettingResource;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index(Request $request)
    {
        $query = SystemSetting::query()
            ->when($request->grupo, fn ($q) => $q->where('grupo', $request->grupo));

        return SystemSettingResource::collection($query->orderBy('grupo')->orderBy('clave')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'grupo' => 'required|string|max:100',
            'clave' => 'required|string|max:255',
            'valor' => 'required',
            'tipo' => 'required|string|max:20',
            'descripcion' => 'nullable|string',
        ]);

        $validated['tenant_id'] = auth()->user()->business_instance_id;
        $setting = SystemSetting::create($validated);

        return new SystemSettingResource($setting);
    }

    public function show(SystemSetting $systemSetting)
    {
        return new SystemSettingResource($systemSetting);
    }

    public function update(Request $request, SystemSetting $systemSetting)
    {
        $validated = $request->validate([
            'grupo' => 'sometimes|string|max:100',
            'clave' => 'sometimes|string|max:255',
            'valor' => 'sometimes|required',
            'tipo' => 'sometimes|string|max:20',
            'descripcion' => 'nullable|string',
        ]);

        $systemSetting->update($validated);

        return new SystemSettingResource($systemSetting);
    }

    public function destroy(SystemSetting $systemSetting)
    {
        $systemSetting->delete();
        return response()->json(['message' => 'Configuración eliminada.']);
    }
}
