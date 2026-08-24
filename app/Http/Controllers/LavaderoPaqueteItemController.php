<?php

namespace App\Http\Controllers;

use App\Models\LavaderoPaquete;
use App\Models\LavaderoPaqueteItem;
use App\Models\LavaderoServicio;
use App\Models\Producto;
use Illuminate\Http\Request;

class LavaderoPaqueteItemController extends Controller
{
    public function index(Request $request)
    {
        $query = LavaderoPaqueteItem::with(['paquete', 'servicio', 'producto']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('servicio', fn($sq) => $sq->where('nombre', 'like', "%{$search}%"))
                  ->orWhereHas('producto', fn($pq) => $pq->where('nombre', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('paquete_id')) {
            $query->where('paquete_id', $request->paquete_id);
        }

        $items = $query->orderBy('paquete_id')
                       ->orderBy('orden')
                       ->paginate(20);

        $paquetes = LavaderoPaquete::activos()->orderBy('nombre')->get();

        return view('lavadero-paquete-items.index', compact('items', 'paquetes'));
    }

    public function create()
    {
        $paquetes = LavaderoPaquete::activos()->orderBy('nombre')->get();
        $servicios = LavaderoServicio::activos()->orderBy('nombre')->get();
        $productos = Producto::activos()->orderBy('nombre')->get();

        return view('lavadero-paquete-items.create', compact('paquetes', 'servicios', 'productos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'paquete_id'      => 'required|exists:lavadero_paquetes,id',
            'tipo'            => 'required|in:servicio,producto',
            'servicio_id'     => 'required_if:tipo,servicio|nullable|exists:lavadero_servicios,id',
            'producto_id'     => 'required_if:tipo,producto|nullable|exists:productos,id',
            'cantidad'        => 'required|numeric|min:0.01',
            'incluir_automatico' => 'boolean',
            'orden'           => 'nullable|integer|min:0',
        ]);

        $data['incluir_automatico'] = $request->boolean('incluir_automatico', false);

        LavaderoPaqueteItem::create($data);

        return redirect()->route('lavadero-paquete-items.index')
            ->with('success', 'Ítem del paquete creado correctamente.');
    }

    public function show(LavaderoPaqueteItem $lavaderoPaqueteItem)
    {
        $lavaderoPaqueteItem->load(['paquete', 'servicio', 'producto']);
        return view('lavadero-paquete-items.show', compact('lavaderoPaqueteItem'));
    }

    public function edit(LavaderoPaqueteItem $lavaderoPaqueteItem)
    {
        $paquetes = LavaderoPaquete::activos()->orderBy('nombre')->get();
        $servicios = LavaderoServicio::activos()->orderBy('nombre')->get();
        $productos = Producto::activos()->orderBy('nombre')->get();

        return view('lavadero-paquete-items.edit', compact('lavaderoPaqueteItem', 'paquetes', 'servicios', 'productos'));
    }

    public function update(Request $request, LavaderoPaqueteItem $lavaderoPaqueteItem)
    {
        $data = $request->validate([
            'paquete_id'      => 'required|exists:lavadero_paquetes,id',
            'tipo'            => 'required|in:servicio,producto',
            'servicio_id'     => 'required_if:tipo,servicio|nullable|exists:lavadero_servicios,id',
            'producto_id'     => 'required_if:tipo,producto|nullable|exists:productos,id',
            'cantidad'        => 'required|numeric|min:0.01',
            'incluir_automatico' => 'boolean',
            'orden'           => 'nullable|integer|min:0',
        ]);

        $data['incluir_automatico'] = $request->boolean('incluir_automatico', false);

        $lavaderoPaqueteItem->update($data);

        return redirect()->route('lavadero-paquete-items.index')
            ->with('success', 'Ítem del paquete actualizado correctamente.');
    }

    public function destroy(LavaderoPaqueteItem $lavaderoPaqueteItem)
    {
        $lavaderoPaqueteItem->delete();

        return redirect()->route('lavadero-paquete-items.index')
            ->with('success', 'Ítem del paquete eliminado correctamente.');
    }
}
