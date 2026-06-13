<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Services\GastoService;
use Illuminate\Http\Request;

class GastoController extends Controller
{
    public function __construct(
        protected GastoService $gastoService
    ) {}

    public function index(Request $request)
    {
        return view('gastos.index', $this->gastoService->list($request->all()));
    }

    public function create()
    {
        $categorias = $this->gastoService->getCategorias();
        return view('gastos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'descripcion'  => 'required|string|max:500',
            'monto'        => 'required|numeric|min:0.01',
            'categoria'    => 'nullable|string|max:100',
            'notas'        => 'nullable|string|max:2000',
            'fecha_gasto'  => 'required|date',
            'metodo_pago'  => 'nullable|string|max:50',
            'comprobante'  => 'nullable|string|max:100',
        ]);

        $this->gastoService->create($data);

        return redirect()->route('gastos.index')
            ->with('success', 'Gasto registrado correctamente.');
    }

    public function show(Gasto $gasto)
    {
        $gasto->load('user', 'caja');
        return view('gastos.show', compact('gasto'));
    }

    public function edit(Gasto $gasto)
    {
        $categorias = $this->gastoService->getCategorias();
        return view('gastos.edit', compact('gasto', 'categorias'));
    }

    public function update(Request $request, Gasto $gasto)
    {
        $data = $request->validate([
            'descripcion'  => 'required|string|max:500',
            'monto'        => 'required|numeric|min:0.01',
            'categoria'    => 'nullable|string|max:100',
            'notas'        => 'nullable|string|max:2000',
            'fecha_gasto'  => 'required|date',
            'metodo_pago'  => 'nullable|string|max:50',
            'comprobante'  => 'nullable|string|max:100',
        ]);

        $this->gastoService->update($gasto, $data);

        return redirect()->route('gastos.index')
            ->with('success', 'Gasto actualizado correctamente.');
    }

    public function destroy(Gasto $gasto)
    {
        $this->gastoService->delete($gasto);

        return redirect()->route('gastos.index')
            ->with('success', 'Gasto eliminado correctamente.');
    }
}
