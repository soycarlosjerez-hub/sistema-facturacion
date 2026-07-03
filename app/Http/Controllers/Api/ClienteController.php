<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClienteResource;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::with(['ventas', 'cotizaciones'])
            ->when($request->search, fn ($q) => $q->where(function ($inner) use ($request) {
                $inner->where('nombre', 'like', '%' . $request->search . '%')
                    ->orWhere('rnc_cedula', 'like', '%' . $request->search . '%')
                    ->orWhere('rnc', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            }))
            ->when($request->tipo_cliente, fn ($q) => $q->where('tipo_cliente', $request->tipo_cliente))
            ->when($request->has_credit_balance, fn ($q) => $q->where('balance_pendiente', '>', 0));

        return ClienteResource::collection($query->orderBy('nombre')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'            => 'required|string|max:255',
            'email'             => 'nullable|email|max:255',
            'telefono'          => 'nullable|string|max:20',
            'whatsapp'          => 'nullable|string|max:20',
            'direccion'         => 'nullable|string|max:500',
            'ciudad'            => 'nullable|string|max:100',
            'provincia'         => 'nullable|string|max:100',
            'codigo_postal'     => 'nullable|string|max:10',
            'rnc_cedula'        => 'nullable|string|max:20',
            'rnc'               => 'nullable|string|max:20',
            'tipo_documento'    => 'nullable|string|max:20',
            'tipo_cliente'      => 'nullable|string|max:20',
            'limite_credito'    => 'nullable|numeric|min:0',
            'plazo_pago_dias'   => 'nullable|integer|min:0|max:365',
            'tasa_descuento_pct'=> 'nullable|numeric|min:0|max:100',
            'moneda'            => 'nullable|in:RD,USD,EUR',
            'auto_bloquear_credito' => 'boolean',
            'notas_internas'    => 'nullable|string',
            'regimen_mensual'   => 'boolean',
            'nit'               => 'nullable|string|max:30',
            'persona_contacto'  => 'nullable|string|max:150',
            'cargo_contacto'    => 'nullable|string|max:100',
            'segmento'          => 'nullable|in:micro,pequeno,mediano,grande,gobierno',
            'origen_cliente'    => 'nullable|in:referencia,web,walkin,publicidad,otro',
            'sector_actividad'  => 'nullable|string|max:100',
            'tenant_id'         => 'required|exists:tenants,id',
        ]);

        $validated['auto_bloquear_credito'] = $request->boolean('auto_bloquear_credito');
        $validated['regimen_mensual'] = $request->boolean('regimen_mensual');

        $cliente = Cliente::create($validated);

        return new ClienteResource($cliente->load(['ventas', 'cotizaciones']));
    }

    public function show(Cliente $cliente)
    {
        return new ClienteResource($cliente->load(['ventas', 'cotizaciones']));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre'            => 'sometimes|string|max:255',
            'email'             => 'sometimes|email|max:255',
            'telefono'          => 'nullable|string|max:20',
            'whatsapp'          => 'nullable|string|max:20',
            'direccion'         => 'nullable|string|max:500',
            'ciudad'            => 'nullable|string|max:100',
            'provincia'         => 'nullable|string|max:100',
            'codigo_postal'     => 'nullable|string|max:10',
            'rnc_cedula'        => 'sometimes|string|max:20',
            'rnc'               => 'sometimes|string|max:20',
            'tipo_documento'    => 'nullable|string|max:20',
            'tipo_cliente'      => 'nullable|string|max:20',
            'limite_credito'    => 'sometimes|numeric|min:0',
            'plazo_pago_dias'   => 'nullable|integer|min:0|max:365',
            'tasa_descuento_pct'=> 'nullable|numeric|min:0|max:100',
            'moneda'            => 'nullable|in:RD,USD,EUR',
            'auto_bloquear_credito' => 'boolean',
            'notas_internas'    => 'nullable|string',
            'regimen_mensual'   => 'boolean',
            'nit'               => 'nullable|string|max:30',
            'persona_contacto'  => 'nullable|string|max:150',
            'cargo_contacto'    => 'nullable|string|max:100',
            'segmento'          => 'nullable|in:micro,pequeno,mediano,grande,gobierno',
            'origen_cliente'    => 'nullable|in:referencia,web,walkin,publicidad,otro',
            'sector_actividad'  => 'nullable|string|max:100',
            'tenant_id'         => 'sometimes|exists:tenants,id',
        ]);

        $validated['auto_bloquear_credito'] = $request->boolean('auto_bloquear_credito');
        $validated['regimen_mensual'] = $request->boolean('regimen_mensual');

        $cliente->update($validated);

        return new ClienteResource($cliente->load(['ventas', 'cotizaciones']));
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return response()->json(['message' => 'Cliente eliminado.']);
    }
}
