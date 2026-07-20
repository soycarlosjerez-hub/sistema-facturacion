<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrdenResource;
use App\Models\Cliente;
use App\Models\Orden;
use App\Services\OrdenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    private function resolverCliente(Request $request): array
    {
        $user = Auth::user();
        $tenantId = $user->business_instance_id;

        Log::info('[Orden API] resolverCliente inicio', [
            'tenant_id' => $tenantId,
            'cliente_id_request' => $request->input('cliente_id'),
            'cliente_rnc_cedula' => $request->input('cliente_rnc_cedula'),
            'cliente_nombre' => $request->input('cliente_nombre'),
            'nombre_cliente' => $request->input('nombre_cliente'),
        ]);

        // Caso 1: Se envió cliente_id → buscar por ID
        if ($request->filled('cliente_id')) {
            $cliente = Cliente::where('id', $request->cliente_id)
                ->where('tenant_id', $tenantId)
                ->first();

            if ($cliente) {
                Log::info('[Orden API] Cliente resuelto por ID', ['cliente_id' => $cliente->id, 'nombre' => $cliente->nombre]);
                return [
                    'cliente_id'       => $cliente->id,
                    'cliente_nombre'   => $cliente->nombre,
                    'cliente_telefono' => $request->input('cliente_telefono', $cliente->telefono),
                    'cliente_email'    => $request->input('cliente_email', $cliente->email),
                    'cliente_rnc_cedula' => $cliente->rnc_cedula,
                ];
            }

            Log::warning('[Orden API] cliente_id no encontrado o no pertenece al tenant', [
                'cliente_id' => $request->cliente_id,
                'tenant_id'  => $tenantId,
            ]);
        }

        // Caso 2: Buscar por RNC/Cédula (más preciso que nombre)
        $rncCedula = $request->input('cliente_rnc_cedula');
        if (!empty($rncCedula)) {
            $cliente = Cliente::where('rnc_cedula', $rncCedula)
                ->where('tenant_id', $tenantId)
                ->first();

            if ($cliente) {
                Log::info('[Orden API] Cliente resuelto por RNC/Cédula', [
                    'cliente_id' => $cliente->id, 'nombre' => $cliente->nombre, 'rnc_cedula' => $rncCedula,
                ]);
                return [
                    'cliente_id'       => $cliente->id,
                    'cliente_nombre'   => $cliente->nombre,
                    'cliente_telefono' => $request->input('cliente_telefono', $cliente->telefono),
                    'cliente_email'    => $request->input('cliente_email', $cliente->email),
                    'cliente_rnc_cedula' => $rncCedula,
                ];
            }
        }

        // Caso 3: Crear o buscar por nombre, guardando todos los datos
        $nombre = $request->input('cliente_nombre') ?: $request->input('nombre_cliente');
        $telefono = $request->input('cliente_telefono') ?: $request->input('telefono_contacto');
        $email = $request->input('cliente_email') ?: $request->input('correo_electronico');
        if (!empty($nombre)) {
            $cliente = Cliente::withoutGlobalScope('tenant')
                ->where('tenant_id', $tenantId)
                ->where('nombre', $nombre)
                ->first();

            if (!$cliente) {
                $cliente = Cliente::create([
                    'nombre'       => $nombre,
                    'tenant_id'    => $tenantId,
                    'telefono'     => $telefono,
                    'email'        => $email,
                    'rnc_cedula'   => $request->input('cliente_rnc_cedula'),
                    'tipo_cliente' => $request->input('tipo_cliente', 'consumo'),
                    'activo'       => true,
                ]);
                Log::info('[Orden API] Cliente CREADO', ['cliente_id' => $cliente->id, 'nombre' => $cliente->nombre, 'tenant_id' => $tenantId]);
            } else {
                Log::info('[Orden API] Cliente ENCONTRADO', ['cliente_id' => $cliente->id, 'nombre' => $cliente->nombre, 'tenant_id' => $tenantId]);
            }

            return [
                'cliente_id'       => $cliente->id,
                'cliente_nombre'   => $cliente->nombre,
                'cliente_telefono' => $cliente->telefono,
                'cliente_email'    => $cliente->email,
                'cliente_rnc_cedula' => $cliente->rnc_cedula,
            ];
        }

        // Caso 4: Sin datos de cliente → consumidor final
        $cliente = Cliente::consumidorFinal();
        Log::info('[Orden API] Sin datos de cliente, se usa Consumidor Final', ['cliente_id' => $cliente->id]);
        return [
            'cliente_id'         => $cliente->id,
            'cliente_nombre'     => $cliente->nombre,
            'cliente_telefono'   => $cliente->telefono,
            'cliente_email'      => $cliente->email,
            'cliente_rnc_cedula' => $cliente->rnc_cedula,
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_orden'       => 'required|string|in:mostrador,delivery,pickup',
            'cliente_id'         => 'nullable|integer|min:1',
            'cliente_nombre'     => 'nullable|string|max:200',
            'cliente_telefono'   => 'nullable|string|max:30',
            'cliente_email'      => 'nullable|email|max:200',
            'cliente_rnc_cedula' => 'nullable|string|max:50',
            'tipo_cliente'       => 'nullable|string|in:credito_fiscal,consumo,gubernamental,especial',
            'entrega_empresa_id' => 'nullable|exists:delivery_companies,id',
            'direccion_entrega'  => 'nullable|string',
            'telefono_contacto'  => 'nullable|string|max:30',
            'hora_retiro'        => 'nullable|date',
            'notas'              => 'nullable|string',
            'nombre_cliente'     => 'nullable|string|max:200',
            'correo_electronico' => 'nullable|email|max:200',
            'items'              => 'nullable|array',
            'items.*.producto_id' => 'required_with:items|integer|exists:productos,id',
            'items.*.cantidad'    => 'required_with:items|integer|min:1',
            'items.*.notas'       => 'nullable|string|max:200',
            'items.*.curso'       => 'nullable|string|in:entrada,fuerte,postre,bebida',
        ]);

        if ($request->filled('nombre_cliente') && ! $request->filled('cliente_nombre')) {
            $validated['cliente_nombre'] = $request->nombre_cliente;
        }
        if ($request->filled('telefono_contacto') && ! $request->filled('cliente_telefono')) {
            $validated['cliente_telefono'] = $request->telefono_contacto;
        }
        if ($request->filled('correo_electronico') && ! $request->filled('cliente_email')) {
            $validated['cliente_email'] = $request->correo_electronico;
        }

        $clienteData = $this->resolverCliente($request);
        $validated = array_merge($validated, $clienteData);

        $items = $validated['items'] ?? [];
        unset($validated['items']);

        $orden = DB::transaction(function () use ($validated, $items) {
            $orden = $this->ordenService->createOrden($validated);

            foreach ($items as $item) {
                $result = $this->ordenService->agregarItem(
                    $orden,
                    $item['producto_id'],
                    $item['cantidad'],
                    $item['notas'] ?? null,
                    $item['curso'] ?? null
                );

                if (isset($result['error'])) {
                    throw new \RuntimeException($result['error']);
                }
            }

            $orden->total = ($orden->subtotal ?? 0) + ($orden->impuestos ?? 0) - ($orden->descuento ?? 0);
            $orden->saveQuietly();

            return $orden->fresh()->load(['detalles.producto', 'cliente', 'usuario']);
        });

        return new OrdenResource($orden);
    }

    public function show(Orden $orden)
    {
        return new OrdenResource($orden->load(['detalles.producto', 'cliente', 'usuario', 'terminal', 'pagos', 'entregaEmpresa']));
    }

    public function update(Request $request, Orden $orden)
    {
        $validated = $request->validate([
            'cliente_id'         => 'nullable|integer|min:1',
            'cliente_nombre'     => 'nullable|string|max:200',
            'cliente_telefono'   => 'nullable|string|max:30',
            'cliente_email'      => 'nullable|email|max:200',
            'cliente_rnc_cedula' => 'nullable|string|max:50',
            'tipo_cliente'       => 'nullable|string|in:credito_fiscal,consumo,gubernamental,especial',
            'entrega_empresa_id' => 'nullable|exists:delivery_companies,id',
            'direccion_entrega'  => 'nullable|string',
            'telefono_contacto'  => 'nullable|string|max:30',
            'hora_retiro'        => 'nullable|date',
            'notas'              => 'nullable|string',
            'tipo_orden'         => 'nullable|string|in:mostrador,delivery,pickup',
        ]);

        if ($request->hasAny(['cliente_id', 'cliente_nombre', 'cliente_rnc_cedula'])) {
            $clienteData = $this->resolverCliente($request);
            $validated = array_merge($validated, $clienteData);
        }

        $fillable = (new Orden)->getFillable();
        $validated = array_intersect_key($validated, array_flip($fillable));

        $orden->update($validated);

        $orden->load('detalles');

        $orden->subtotal = $orden->detalles->sum('subtotal');
        $orden->total = ($orden->subtotal ?? 0) + ($orden->impuestos ?? 0) - ($orden->descuento ?? 0);
        $orden->saveQuietly();

        return new OrdenResource($orden->fresh()->load(['detalles.producto', 'cliente', 'usuario']));
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
