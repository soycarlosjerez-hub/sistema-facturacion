<?php

namespace App\Http\Controllers;

use App\Models\DeliveryDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryDriverController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryDriver::query();

        if ($search = $this->dtSearch($request)) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellido', 'like', "%{$search}%")
                    ->orWhere('cedula', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        $drivers = $query->orderBy('nombre')->paginate(15)->withQueryString();

        return view('delivery-drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('delivery-drivers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'           => 'required|string|max:60',
            'apellido'         => 'required|string|max:60',
            'cedula'           => 'nullable|string|max:20',
            'telefono'         => 'nullable|string|max:30',
            'whatsapp'         => 'nullable|string|max:30',
            'licencia_conducir'=> 'nullable|string|max:50',
            'activo'           => 'boolean',
            'notas'            => 'nullable|string',
            'avatar_url'       => 'nullable|string|max:500',
        ]);

        $data['activo'] = $request->boolean('activo', true);
        $data['tenant_id'] = Auth::user()->business_instance_id;

        DeliveryDriver::create($data);

        return redirect()->route('delivery-drivers.index')
            ->with('success', 'Conductor creado correctamente.');
    }

    public function edit(DeliveryDriver $deliveryDriver)
    {
        return view('delivery-drivers.edit', ['driver' => $deliveryDriver]);
    }

    public function update(Request $request, DeliveryDriver $deliveryDriver)
    {
        $data = $request->validate([
            'nombre'           => 'required|string|max:60',
            'apellido'         => 'required|string|max:60',
            'cedula'           => 'nullable|string|max:20',
            'telefono'         => 'nullable|string|max:30',
            'whatsapp'         => 'nullable|string|max:30',
            'licencia_conducir'=> 'nullable|string|max:50',
            'activo'           => 'boolean',
            'notas'            => 'nullable|string',
            'avatar_url'       => 'nullable|string|max:500',
        ]);

        $data['activo'] = $request->boolean('activo', true);

        $deliveryDriver->update($data);

        return redirect()->route('delivery-drivers.index')
            ->with('success', 'Conductor actualizado correctamente.');
    }

    public function destroy(DeliveryDriver $deliveryDriver)
    {
        $deliveryDriver->delete();

        return redirect()->route('delivery-drivers.index')
            ->with('success', 'Conductor eliminado.');
    }

    public function listarActivos()
    {
        return response()->json(
            DeliveryDriver::activos()
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'apellido', 'telefono', 'whatsapp'])
        );
    }
}
