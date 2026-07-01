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

    public function show(PlantaGasto $plantilla-gasto)
    {
        return view('plantilla-gastos.show', compact('plantilla-gasto'));
    }

    public function edit(PlantaGasto $plantilla-gasto)
    {
        $categorias = PlantaGasto::categorias();
        $metodosPago = PlantaGasto::metodosPago();
        return view('plantilla-gastos.edit', compact('plantilla-gasto', 'categorias', 'metodosPago'));
    }

    public function update(Request $request, PlantaGasto $plantilla-gasto)
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

        $plantilla-gasto->update($validated);

        return redirect()->route('plantilla-gastos.index')
            ->with('success', 'Plantilla de gasto actualizada correctamente.');
    }

    public function destroy(PlantaGasto $plantilla-gasto)
    {
        $plantilla-gasto->delete();

        return redirect()->route('plantilla-gastos.index')
            ->with('success', 'Plantilla de gasto eliminada correctamente.');
    }

    public function activar(PlantaGasto $plantilla-gasto)
    {
        $plantilla-gasto->update(['activo' => true]);

        return back()->with('success', 'Plantilla activada correctamente.');
    }

    public function desactivar(PlantaGasto $plantilla-gasto)
    {
        $plantilla-gasto->update(['activo' => false]);

        return back()->with('success', 'Plantilla desactivada correctamente.');
    }
}
