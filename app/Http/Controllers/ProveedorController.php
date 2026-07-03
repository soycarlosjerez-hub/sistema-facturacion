<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Services\ProveedorService;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function __construct(
        protected ProveedorService $proveedorService
    ) {}

    public function index(Request $request)
    {
        return view('proveedores.index', $this->proveedorService->list($request->all()));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'                => 'required|string|max:255',
            'email'                 => 'nullable|email|max:255',
            'telefono'              => 'nullable|string|max:30',
            'direccion'             => 'nullable|string|max:255',
            'rnc'                   => 'nullable|string|max:20',
            'tipo_persona'          => 'nullable|string|in:fisica,juridica',
            'sujeto_retencion_isr'  => 'boolean',
            'sujeto_retencion_itbis' => 'boolean',
        ]);

        $this->proveedorService->create($data);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado correctamente.');
    }

    public function show(Proveedor $proveedore)
    {
        $proveedore->load('compras');
        return view('proveedores.show', compact('proveedore'));
    }

    public function edit(Proveedor $proveedore)
    {
        return view('proveedores.edit', compact('proveedore'));
    }

    public function update(Request $request, Proveedor $proveedore)
    {
        $data = $request->validate([
            'nombre'                => 'required|string|max:255',
            'email'                 => 'nullable|email|max:255',
            'telefono'              => 'nullable|string|max:30',
            'direccion'             => 'nullable|string|max:255',
            'rnc'                   => 'nullable|string|max:20',
            'tipo_persona'          => 'nullable|string|in:fisica,juridica',
            'sujeto_retencion_isr'  => 'boolean',
            'sujeto_retencion_itbis' => 'boolean',
            'activo'                => 'boolean',
        ]);

        $this->proveedorService->update($proveedore, $data);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente');
    }

    public function destroy(Proveedor $proveedore)
    {
        $result = $this->proveedorService->delete($proveedore);
        $type = $result['deactivated'] ? 'warning' : 'success';
        return redirect()->route('proveedores.index')->with($type, $result['message']);
    }

    public function pdf(Request $request)
    {
        return $this->proveedorService->pdf($request->all());
    }
}
