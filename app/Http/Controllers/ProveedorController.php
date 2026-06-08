<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Support\RncValidator;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Asegúrate de tener barryvdh/laravel-dompdf instalado
use Maatwebsite\Excel\Facades\Excel;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        
        $proveedores = Proveedor::when($buscar, function ($q) use ($buscar) {
            $q->where('nombre', 'like', '%' . $buscar . '%')
              ->orWhere('email', 'like', '%' . $buscar . '%')
              ->orWhere('telefono', 'like', '%' . $buscar . '%')
              ->orWhere('rnc_cedula', 'like', '%' . $buscar . '%');
        })
        ->latest()
        ->paginate(10);

        $totalProveedores = Proveedor::count();

        return view('proveedores.index', compact('proveedores', 'totalProveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function show(Proveedor $proveedore)
    {
        $proveedore->load('compras');
        return view('proveedores.show', compact('proveedore'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'               => 'required|string|max:255',
            'email'                => 'nullable|email|max:255',
            'telefono'             => 'nullable|string|max:30',
            'direccion'            => 'nullable|string|max:255',
            'rnc'                  => 'nullable|string|max:20',
            'tipo_persona'         => 'nullable|string|in:fisica,juridica',
            'sujeto_retencion_isr' => 'boolean',
            'sujeto_retencion_itbis' => 'boolean',
        ]);

        if (!empty($validated['rnc']) && !RncValidator::validar($validated['rnc'])) {
            return back()->withErrors(['rnc' => 'El RNC ingresado no es válido.'])->withInput();
        }

        $validated['sujeto_retencion_isr'] = $request->boolean('sujeto_retencion_isr');
        $validated['sujeto_retencion_itbis'] = $request->boolean('sujeto_retencion_itbis');

        Proveedor::create($validated);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado correctamente.');
    }

    // Mostrar formulario de edición
    public function edit(Proveedor $proveedore)
    {
        // $proveedore es el modelo inyectado por route model binding
        return view('proveedores.edit', compact('proveedore'));
    }

    public function update(Request $request, Proveedor $proveedore)
    {
        $validated = $request->validate([
            'nombre'               => 'required|string|max:255',
            'email'                => 'nullable|email|max:255',
            'telefono'             => 'nullable|string|max:30',
            'direccion'            => 'nullable|string|max:255',
            'rnc'                  => 'nullable|string|max:20',
            'tipo_persona'         => 'nullable|string|in:fisica,juridica',
            'sujeto_retencion_isr' => 'boolean',
            'sujeto_retencion_itbis' => 'boolean',
        ]);

        if (!empty($validated['rnc']) && !RncValidator::validar($validated['rnc'])) {
            return back()->withErrors(['rnc' => 'El RNC ingresado no es válido.'])->withInput();
        }

        $validated['sujeto_retencion_isr'] = $request->boolean('sujeto_retencion_isr');
        $validated['sujeto_retencion_itbis'] = $request->boolean('sujeto_retencion_itbis');

        $proveedore->update($validated);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente');
    }

    public function destroy(Proveedor $proveedore)
    {
        $proveedore->delete();
        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado correctamente');
    }


    public function pdf(Request $request)
    {
        // Construir la consulta
        $query = Proveedor::query();

        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                    ->orWhere('email', 'like', "%{$busqueda}%")
                    ->orWhere('telefono', 'like', "%{$busqueda}%")
                    ->orWhere('direccion', 'like', "%{$busqueda}%");
            });
        }

        // Obtener los proveedores
        $proveedores = $query->latest()->get();

        // Generar PDF usando la vista 'proveedores.pdf'
        $pdf = Pdf::loadView('proveedores.pdf', compact('proveedores'));

        // Mostrar el PDF en el navegador
        return $pdf->stream('proveedores.pdf');
    }
}
