<?php

namespace App\Http\Controllers;

use App\Models\PlantaGasto;
use Illuminate\Http\Request;

class PlantaGastoController extends Controller
{
    public function index(Request $request)
    {
        $query = PlantaGasto::query()->with('tenant');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->search . '%')
                  ->orWhere('comprobante', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('activo')) {
            $query->where('activo', $request->activo === '1');
        }

        $plantillas = $query->latest()->paginate(15);
        $categorias = PlantaGasto::categorias();

        return view('plantilla-gastos.index', compact('plantillas', 'categorias'));
    }

    public function create()
    {
        $categorias = PlantaGasto::categorias();
        $metodosPago = PlantaGasto::metodosPago();
        return view('plantilla-gastos.create', compact('categorias', 'metodosPago'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:500',
            'categoria'   => 'nullable|string|max:100',
            'metodo_pago' => 'nullable|string|max:50',
            'comprobante' => 'nullable|string|max:100',
            'notas'       => 'nullable|string|max:2000',
            'activo'      => 'boolean',
        ]);

        PlantaGasto::create($validated);

        return redirect()->route('plantilla-gastos.index')
            ->with('success', 'Plantilla de gasto creada correctamente.');
    }

    public function show(PlantaGasto $plantaGasto)
    {
        $plantillaGasto = $plantaGasto;
        return view('plantilla-gastos.show', compact('plantillaGasto'));
    }

    public function edit(PlantaGasto $plantaGasto)
    {
        $plantillaGasto = $plantaGasto;
        $categorias = PlantaGasto::categorias();
        $metodosPago = PlantaGasto::metodosPago();
        return view('plantilla-gastos.edit', compact('plantillaGasto', 'categorias', 'metodosPago'));
    }

    public function update(Request $request, PlantaGasto $plantaGasto)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:500',
            'categoria'   => 'nullable|string|max:100',
            'metodo_pago' => 'nullable|string|max:50',
            'comprobante' => 'nullable|string|max:100',
            'notas'       => 'nullable|string|max:2000',
            'activo'      => 'boolean',
        ]);

        $plantaGasto->update($validated);

        return redirect()->route('plantilla-gastos.index')
            ->with('success', 'Plantilla de gasto actualizada correctamente.');
    }

    public function destroy(PlantaGasto $plantaGasto)
    {
        $plantaGasto->delete();

        return redirect()->route('plantilla-gastos.index')
            ->with('success', 'Plantilla de gasto eliminada correctamente.');
    }

    public function activar(PlantaGasto $plantaGasto)
    {
        $plantaGasto->update(['activo' => true]);

        return back()->with('success', 'Plantilla activada correctamente.');
    }

    public function desactivar(PlantaGasto $plantaGasto)
    {
        $plantaGasto->update(['activo' => false]);

        return back()->with('success', 'Plantilla desactivada correctamente.');
    }
}
