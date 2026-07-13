<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrdenResource;
use App\Models\Cliente;
use App\Models\Orden;
use App\Services\OrdenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrdenController extends Controller
{
    public function __construct(
        protected OrdenService $ordenService
    ) {}

    public function index(Request $request)
    {
        $query = Orden::deSucursal()->with(['detalles.producto', 'cliente', 'usuario', 'terminal'])
            ->when($request->tipo, fn($q) => $q->where('tipo_orden', $request->tipo))
            ->when($request->estado, fn($q) => $q->where('estado', $request->estado))
            ->when($request->cliente_id, fn($q) => $q->where('cliente_id', $request->cliente_id))
            ->when($request->fecha, fn($q) => $q->whereDate('created_at', $request->fecha));

        return OrdenResource::collection($query->orderBy('created_at', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_orden'       => 'required|string|in:mostrador,delivery,pickup',
            'cliente_id'       => 'nullable|exists:clientes,id',
            'cliente_nombre'   => 'nullable|string|max:200',
            'cliente_telefono' => 'nullable|string|max:30',
            'cliente_email'    => 'nullable|email|max:200',
            'entrega_empresa_id' => 'nullable|exists:delivery_companies,id',
            'direccion_entrega'  => 'nullable|string',
            'telefono_contacto'  => 'nullable|string|max:30',
            'hora_retiro'        => 'nullable|date',
            'notas'              => 'nullable|string',
        ]);

        if (empty($validated['cliente_id']) && !empty($validated['cliente_nombre'])) {
            $user = Auth::user();
            $cliente = Cliente::firstOrCreate(
                ['nombre' => $validated['cliente_nombre'], 'tenant_id' => $user->business_instance_id],
                ['telefono' => $validated['cliente_telefono'] ?? null, 'email' => $validated['cliente_email'] ?? null]
            );
            $validated['cliente_id'] = $cliente->id;
        }

        $orden = $this->ordenService->createOrden($validated);

        return new OrdenResource($orden->load(['detalles.producto', 'cliente', 'usuario']));
    }

    public function show(Orden $orden)
    {
        return new OrdenResource($orden->load(['detalles.producto', 'cliente', 'usuario', 'terminal', 'pagos', 'entregaEmpresa']));
    }

    public function update(Request $request, Orden $orden)
    {
        $validated = $request->validate([
            'cliente_id'       => 'nullable|exists:clientes,id',
            'cliente_nombre'   => 'nullable|string|max:200',
            'cliente_telefono' => 'nullable|string|max:30',
            'cliente_email'    => 'nullable|email|max:200',
            'entrega_empresa_id' => 'nullable|exists:delivery_companies,id',
            'direccion_entrega'  => 'nullable|string',
            'telefono_contacto'  => 'nullable|string|max:30',
            'hora_retiro'        => 'nullable|date',
            'notas'              => 'nullable|string',
        ]);

        if (empty($validated['cliente_id']) && !empty($validated['cliente_nombre'])) {
            $user = Auth::user();
            $cliente = Cliente::firstOrCreate(
                ['nombre' => $validated['cliente_nombre'], 'tenant_id' => $user->business_instance_id],
                ['telefono' => $validated['cliente_telefono'] ?? null, 'email' => $validated['cliente_email'] ?? null]
            );
            $validated['cliente_id'] = $cliente->id;
        }

        $orden->update($validated);

        return new OrdenResource($orden->load(['detalles.producto', 'cliente', 'usuario']));
    }

    public function destroy(Orden $orden)
    {
        $result = $this->ordenService->anular($orden, request('motivo', 'Anulada por usuario'));
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['code']);
        }
        return response()->json(['message' => 'Orden anulada.']);
    }
}
