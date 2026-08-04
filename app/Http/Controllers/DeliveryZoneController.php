<?php

namespace App\Http\Controllers;

use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryZoneController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryZone::query();

        if ($search = $request->input('search')) {
            $query->where('nombre', 'like', "%{$search}%");
        }

        $zones = $query->orderBy('nombre')->paginate(15)->withQueryString();

        return view('delivery-zones.index', compact('zones'));
    }

    public function create()
    {
        return view('delivery-zones.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'                  => 'required|string|max:100',
            'descripcion'             => 'nullable|string',
            'radio_km'                => 'required|numeric|min:0',
            'tarifa_base'             => 'required|numeric|min:0',
            'tarifa_por_km'           => 'required|numeric|min:0',
            'tiempo_estimado_minutos' => 'required|integer|min:1',
            'minimo_para_envio_gratis'=> 'nullable|numeric|min:0',
            'activo'                  => 'boolean',
            'zona_poligono'           => 'nullable|array',
        ]);

        $data['activo'] = $request->boolean('activo', true);
        $data['tenant_id'] = Auth::user()->business_instance_id;

        DeliveryZone::create($data);

        return redirect()->route('delivery-zones.index')
            ->with('success', 'Zona de delivery creada correctamente.');
    }

    public function edit(DeliveryZone $deliveryZone)
    {
        return view('delivery-zones.edit', ['zone' => $deliveryZone]);
    }

    public function update(Request $request, DeliveryZone $deliveryZone)
    {
        $data = $request->validate([
            'nombre'                  => 'required|string|max:100',
            'descripcion'             => 'nullable|string',
            'radio_km'                => 'required|numeric|min:0',
            'tarifa_base'             => 'required|numeric|min:0',
            'tarifa_por_km'           => 'required|numeric|min:0',
            'tiempo_estimado_minutos' => 'required|integer|min:1',
            'minimo_para_envio_gratis'=> 'nullable|numeric|min:0',
            'activo'                  => 'boolean',
            'zona_poligono'           => 'nullable|array',
        ]);

        $data['activo'] = $request->boolean('activo', true);

        $deliveryZone->update($data);

        return redirect()->route('delivery-zones.index')
            ->with('success', 'Zona de delivery actualizada correctamente.');
    }

    public function destroy(DeliveryZone $deliveryZone)
    {
        $deliveryZone->delete();

        return redirect()->route('delivery-zones.index')
            ->with('success', 'Zona de delivery eliminada.');
    }

    public function listarActivas()
    {
        return response()->json(
            DeliveryZone::activos()
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'descripcion', 'radio_km', 'tarifa_base', 'tarifa_por_km', 'tiempo_estimado_minutos', 'minimo_para_envio_gratis'])
        );
    }
}
