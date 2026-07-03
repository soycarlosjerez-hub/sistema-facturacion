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
        if (!$request->filled('rnc_cedula') && !$request->filled('tipo_documento')) {
            $request->merge(['tipo_documento' => 'ninguno']);
        } elseif ($request->filled('rnc_cedula') && !$request->filled('tipo_documento')) {
            $clean = preg_replace('/[^0-9]/', '', $request->rnc_cedula);
            $request->merge(['tipo_documento' => strlen($clean) === 11 ? 'rnc' : 'cedula']);
        }

        $data = $request->validate([
            'nombre'            => 'required|string|max:255',
            'rnc_cedula'        => ['nullable', 'digits_between:9,11', new RncValido],
            'tipo_documento'    => 'nullable|in:rnc,cedula,pasaporte,ninguno',
            'tipo_cliente'      => 'nullable|in:credito_fiscal,consumo,gubernamental,especial',
            'email'             => 'nullable|email',
            'telefono'          => 'nullable',
            'whatsapp'          => 'nullable',
            'direccion'         => 'nullable',
            'ciudad'            => 'nullable|string|max:100',
            'provincia'         => 'nullable|string|max:100',
            'codigo_postal'     => 'nullable|string|max:10',
            'persona_contacto'  => 'nullable|string|max:150',
            'cargo_contacto'    => 'nullable|string|max:100',
            'limite_credito'    => 'nullable|numeric|min:0',
            'plazo_pago_dias'   => 'nullable|integer|min:0|max:365',
            'tasa_descuento_pct' => 'nullable|numeric|min:0|max:100',
            'moneda'            => 'nullable|in:RD,USD,EUR',
            'auto_bloquear_credito' => 'boolean',
            'notas_internas'    => 'nullable|string',
            'regimen_mensual'   => 'boolean',
            'nit'               => 'nullable|string|max:30',
            'segmento'          => 'nullable|in:micro,pequeno,mediano,grande,gobierno',
            'origen_cliente'    => 'nullable|in:referencia,web,walkin,publicidad,otro',
            'sector_actividad'  => 'nullable|string|max:100',
            'activo'            => 'boolean',
        ]);

        $data['activo'] = $request->boolean('activo');
        $data['auto_bloquear_credito'] = $request->boolean('auto_bloquear_credito');
        $data['regimen_mensual'] = $request->boolean('regimen_mensual');

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
        if (!$request->filled('rnc_cedula') && !$request->filled('tipo_documento')) {
            $request->merge(['tipo_documento' => 'ninguno']);
        } elseif ($request->filled('rnc_cedula') && !$request->filled('tipo_documento')) {
            $clean = preg_replace('/[^0-9]/', '', $request->rnc_cedula);
            $request->merge(['tipo_documento' => strlen($clean) === 11 ? 'rnc' : 'cedula']);
        }

        $data = $request->validate([
            'nombre'            => 'required|string|max:255',
            'rnc_cedula'        => ['nullable', 'digits_between:9,11', new RncValido],
            'tipo_documento'    => 'nullable|in:rnc,cedula,pasaporte,ninguno',
            'tipo_cliente'      => 'nullable|in:credito_fiscal,consumo,gubernamental,especial',
            'email'             => 'nullable|email',
            'telefono'          => 'nullable',
            'whatsapp'          => 'nullable',
            'direccion'         => 'nullable',
            'ciudad'            => 'nullable|string|max:100',
            'provincia'         => 'nullable|string|max:100',
            'codigo_postal'     => 'nullable|string|max:10',
            'persona_contacto'  => 'nullable|string|max:150',
            'cargo_contacto'    => 'nullable|string|max:100',
            'limite_credito'    => 'nullable|numeric|min:0',
            'plazo_pago_dias'   => 'nullable|integer|min:0|max:365',
            'tasa_descuento_pct' => 'nullable|numeric|min:0|max:100',
            'moneda'            => 'nullable|in:RD,USD,EUR',
            'auto_bloquear_credito' => 'boolean',
            'notas_internas'    => 'nullable|string',
            'regimen_mensual'   => 'boolean',
            'nit'               => 'nullable|string|max:30',
            'segmento'          => 'nullable|in:micro,pequeno,mediano,grande,gobierno',
            'origen_cliente'    => 'nullable|in:referencia,web,walkin,publicidad,otro',
            'sector_actividad'  => 'nullable|string|max:100',
            'activo'            => 'boolean',
        ]);

        $data['activo'] = $request->boolean('activo');
        $data['auto_bloquear_credito'] = $request->boolean('auto_bloquear_credito');
        $data['regimen_mensual'] = $request->boolean('regimen_mensual');

        $this->clienteService->update($cliente, $data);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente');
    }

    public function destroy(Cliente $cliente)
    {
        $this->clienteService->delete($cliente);
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado');
    }

    public function resumenCreditos(Request $request)
    {
        $resumen = $this->clienteService->resumenCreditos();
        $clientesEnExceso = Cliente::excedeCredito()
            ->with(['ventas' => fn($q) => $q->whereIn('estado', ['pendiente', 'cuenta_abierta'])])
            ->get();
        return view('clientes.creditos', compact('resumen', 'clientesEnExceso'));
    }

    public function recalcularBalances()
    {
        $this->clienteService->recalcularBalances();
        return redirect()->back()->with('success', 'Balances recalculados correctamente.');
    }

    public function toggleActivo(Cliente $cliente)
    {
        $cliente = $this->clienteService->toggleActivo($cliente);
        return response()->json([
            'success' => true,
            'activo'  => $cliente->activo,
            'label'   => $cliente->activo_label,
        ]);
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
