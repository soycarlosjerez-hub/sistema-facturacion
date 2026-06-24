<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Services\ClienteService;
use Illuminate\Http\Request;
use App\Rules\RncValido;

class ClienteController extends Controller
{
    public function __construct(
        protected ClienteService $clienteService
    ) {}

    public function index(Request $request)
    {
        $clientes = $this->clienteService->list($request->all());
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:255',
            'rnc_cedula'    => ['nullable', 'digits_between:9,11', new RncValido],
            'tipo_documento' => 'nullable|in:rnc,cedula,pasaporte',
            'email'         => 'nullable|email',
            'telefono'      => 'nullable',
            'direccion'     => 'nullable',
        ]);

        $this->clienteService->create($data);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load('ventas');
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:255',
            'rnc_cedula'    => ['nullable', 'digits_between:9,11', new RncValido],
            'tipo_documento' => 'nullable|in:rnc,cedula,pasaporte',
            'email'         => 'nullable|email',
            'telefono'      => 'nullable',
            'direccion'     => 'nullable',
        ]);

        $this->clienteService->update($cliente, $data);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente');
    }

    public function destroy(Cliente $cliente)
    {
        $this->clienteService->delete($cliente);
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado');
    }

    public function pdf(Request $request)
    {
        return $this->clienteService->pdf($request->all());
    }

    public function cuentas(Request $request)
    {
        $clientes = $this->clienteService->cuentasPendientes($request->input('buscar'));
        return view('clientes.cuentas', compact('clientes'));
    }
}
