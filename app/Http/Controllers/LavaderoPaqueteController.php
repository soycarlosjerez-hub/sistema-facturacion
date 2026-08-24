<?php

namespace App\Http\Controllers;

use App\Models\LavaderoPaquete;
use App\Services\LavaderoPaqueteService;
use Illuminate\Http\Request;

class LavaderoPaqueteController extends Controller
{
    protected LavaderoPaqueteService $service;

    public function __construct(LavaderoPaqueteService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $paquetes = $this->service->getAll();
        return view('lavadero-paquetes.index', compact('paquetes'));
    }

    public function create()
    {
        $servicios = \App\Models\LavaderoServicio::activos()->orderBy('nombre')->get();
        $productos = \App\Models\Producto::activos()->orderBy('nombre')->get();
        $businessTypes = \App\Models\BusinessType::all();

        return view('lavadero-paquetes.create', compact('servicios', 'productos', 'businessTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'           => 'required|string|max:200',
            'descripcion'      => 'nullable|string',
            'precio'           => 'required|numeric|min:0',
            'precio_anterior'  => 'nullable|numeric|min:0|gte:precio',
            'duracion_minutos' => 'nullable|integer|min:1',
            'aplicable_a_tipo' => 'nullable|string|max:50',
            'max_usos_cliente' => 'nullable|integer|min:0',
            'activo'           => 'boolean',
            'orden'            => 'nullable|integer|min:0',
            'configuracion'    => 'nullable|array',
            'tags'             => 'nullable|array',
            'items'            => 'nullable|array',
            'items.*.tipo'     => 'required|in:servicio,producto',
            'items.*.servicio_id' => 'nullable|exists:lavadero_servicios,id',
            'items.*.producto_id' => 'nullable|exists:productos,id',
            'items.*.cantidad'    => 'required|numeric|min:0.01',
            'items.*.incluir_automatico' => 'boolean',
        ]);

        $data['activo'] = $request->boolean('activo', true);

        $paquete = $this->service->createWithItems($data, $request->input('items', []));

        return redirect()->route('lavadero-paquetes.index')
            ->with('success', 'Paquete creado correctamente.');
    }

    public function show(LavaderoPaquete $lavaderoPaquete)
    {
        $completo = $this->service->getPaqueteCompleto($lavaderoPaquete->id);
        return view('lavadero-paquetes.show', compact('completo'));
    }

    public function edit(LavaderoPaquete $lavaderoPaquete)
    {
        $servicios = \App\Models\LavaderoServicio::activos()->orderBy('nombre')->get();
        $productos = \App\Models\Producto::activos()->orderBy('nombre')->get();
        $businessTypes = \App\Models\BusinessType::all();

        return view('lavadero-paquetes.edit', compact('lavaderoPaquete', 'servicios', 'productos', 'businessTypes'));
    }

    public function update(Request $request, LavaderoPaquete $lavaderoPaquete)
    {
        $data = $request->validate([
            'nombre'           => 'required|string|max:200',
            'descripcion'      => 'nullable|string',
            'precio'           => 'required|numeric|min:0',
            'precio_anterior'  => 'nullable|numeric|min:0|gte:precio',
            'duracion_minutos' => 'nullable|integer|min:1',
            'aplicable_a_tipo' => 'nullable|string|max:50',
            'max_usos_cliente' => 'nullable|integer|min:0',
            'activo'           => 'boolean',
            'orden'            => 'nullable|integer|min:0',
            'configuracion'    => 'nullable|array',
            'tags'             => 'nullable|array',
            'items'            => 'nullable|array',
            'items.*.tipo'     => 'required|in:servicio,producto',
            'items.*.servicio_id' => 'nullable|exists:lavadero_servicios,id',
            'items.*.producto_id' => 'nullable|exists:productos,id',
            'items.*.cantidad'    => 'required|numeric|min:0.01',
            'items.*.incluir_automatico' => 'boolean',
        ]);

        $data['activo'] = $request->boolean('activo', true);

        $paquete = $this->service->updateWithItems($lavaderoPaquete->id, $data, $request->input('items', []));

        return redirect()->route('lavadero-paquetes.index')
            ->with('success', 'Paquete actualizado correctamente.');
    }

    public function destroy(LavaderoPaquete $lavaderoPaquete)
    {
        $this->service->delete($lavaderoPaquete->id);

        return redirect()->route('lavadero-paquetes.index')
            ->with('success', 'Paquete eliminado correctamente.');
    }

    public function toggleActivo(LavaderoPaquete $lavaderoPaquete)
    {
        $paquete = $this->service->toggleActivo($lavaderoPaquete->id);

        return response()->json([
            'success' => true,
            'activo'  => $paquete->activo,
            'label'   => $paquete->activo ? 'Activo' : 'Inactivo',
        ]);
    }

    public function preview(LavaderoPaquete $lavaderoPaquete)
    {
        $preview = $this->service->getPaqueteCompleto($lavaderoPaquete->id);

        return response()->json($preview);
    }

    public function calcularPrecio(LavaderoPaquete $lavaderoPaquete)
    {
        $precio = $this->service->calcularPrecio($lavaderoPaquete->id);

        return response()->json([
            'paquete' => $lavaderoPaquete->nombre,
            'precio'  => $precio,
        ]);
    }
}
