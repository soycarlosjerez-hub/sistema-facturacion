<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Support\RncValidator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $nombre = $request->input('nombre');
        
        $clientes = Cliente::when($nombre, function ($q) use ($nombre) {
            $q->where('nombre', 'like', '%' . $nombre . '%')
              ->orWhere('email', 'like', '%' . $nombre . '%')
              ->orWhere('rnc_cedula', 'like', '%' . $nombre . '%')
              ->orWhere('telefono', 'like', '%' . $nombre . '%');
        })
        ->latest()
        ->paginate(10)
        ->appends($request->query());

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function show($id)
    {
        $cliente = Cliente::with('ventas')->findOrFail($id);

        return view('clientes.show', compact('cliente'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'rnc_cedula' => 'nullable|digits_between:9,11',
            'tipo_documento' => 'nullable|in:rnc,cedula,pasaporte',
            'email' => 'nullable|email',
            'telefono' => 'nullable',
            'direccion' => 'nullable',
        ]);

        $data = $request->all();

        if (!empty($data['rnc_cedula'])) {
            $tipoDoc = $data['tipo_documento'] ?? RncValidator::inferirTipo($data['rnc_cedula']);
            if (!RncValidator::validar($data['rnc_cedula'], $tipoDoc)) {
                throw ValidationException::withMessages([
                    'rnc_cedula' => "El {$tipoDoc} ingresado no es válido (dígito verificador incorrecto).",
                ]);
            }
            $data['tipo_documento'] = $tipoDoc;
        }

        Cliente::create($data);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'rnc_cedula' => 'nullable|digits_between:9,11',
            'tipo_documento' => 'nullable|in:rnc,cedula,pasaporte',
            'email' => 'nullable|email',
            'telefono' => 'nullable',
            'direccion' => 'nullable',
        ]);

        $data = $request->all();

        if (!empty($data['rnc_cedula'])) {
            $tipoDoc = $data['tipo_documento'] ?? RncValidator::inferirTipo($data['rnc_cedula']);
            if (!RncValidator::validar($data['rnc_cedula'], $tipoDoc)) {
                throw ValidationException::withMessages([
                    'rnc_cedula' => "El {$tipoDoc} ingresado no es válido (dígito verificador incorrecto).",
                ]);
            }
            $data['tipo_documento'] = $tipoDoc;
        }

        $cliente->update($data);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado');
    }

    public function pdf(Request $request)
    {
        $query = Cliente::query();

        if ($request->filled('busqueda')) {
            $query->where('nombre', 'like', '%' . $request->busqueda . '%')
                ->orWhere('email', 'like', '%' . $request->busqueda . '%');
        }

        $clientes = $query->latest()->get();

        $pdf = Pdf::loadView('clientes.pdf', compact('clientes'));
        return $pdf->stream('clientes.pdf');
    }

    public function cuentas(Request $request)
    {
        $nombre = $request->input('buscar');
        
        $clientes = Cliente::where('balance_pendiente', '>', 0)
            ->where('nombre', '!=', 'Consumidor Final')
            ->when($nombre, function ($q) use ($nombre) {
                $q->where(function($sub) use ($nombre) {
                    $sub->where('nombre', 'like', '%' . $nombre . '%')
                        ->orWhere('rnc_cedula', 'like', '%' . $nombre . '%');
                });
            })
            ->with(['ventas' => function($q) {
                $q->whereIn('estado', ['pendiente', 'cuenta_abierta'])->latest();
            }])
            ->latest()
            ->paginate(10);

        return view('clientes.cuentas', compact('clientes'));
    }
}
