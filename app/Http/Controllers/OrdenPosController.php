<?php

namespace App\Http\Controllers;

use App\Enums\OrdenTipo;
use App\Models\Cliente;
use App\Models\Orden;
use App\Models\Producto;
use App\Services\OrdenService;
use App\Services\OrdenPaymentService;
use App\Services\OrdenNotificationService;
use App\Services\PrintService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrdenPosController extends Controller
{
    public function __construct(
        protected OrdenService $ordenService,
        protected OrdenPaymentService $paymentService,
        protected OrdenNotificationService $notificationService,
        protected PrintService $printService,
    ) {}

    public function index()
    {
        $data = $this->ordenService->getIndexData();
        return view('ordenes.index', $data);
    }

    public function create()
    {
        return view('ordenes.create', [
            'tipos' => OrdenTipo::cases(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_orden' => 'required|string|in:mostrador,delivery,pickup',
            'cliente_id' => 'nullable|exists:clientes,id',
            'cliente_nombre' => 'nullable|string|max:200',
            'cliente_telefono' => 'nullable|string|max:30',
            'cliente_email' => 'nullable|email|max:200',
            'direccion_entrega' => 'nullable|string',
            'telefono_contacto' => 'nullable|string|max:30',
            'hora_retiro' => 'nullable|date',
            'entrega_empresa_id' => 'nullable|exists:delivery_companies,id',
            'notas' => 'nullable|string',
            'items' => 'nullable|json',
        ]);

        if (empty($validated['cliente_id']) && !empty($validated['cliente_nombre'])) {
            $cliente = Cliente::firstOrCreate(
                ['nombre' => $validated['cliente_nombre']],
                ['telefono' => $validated['cliente_telefono'] ?? null, 'email' => $validated['cliente_email'] ?? null]
            );
            $validated['cliente_id'] = $cliente->id;
        }

        $orden = $this->ordenService->createOrden($validated);

        $items = json_decode($request->input('items', '[]'), true);
        foreach ($items as $item) {
            $result = $this->ordenService->agregarItem(
                $orden,
                $item['producto_id'],
                $item['cantidad'] ?? 1,
                $item['notas'] ?? null,
                $item['curso'] ?? null
            );
            if (isset($result['error'])) {
                return redirect()->back()->with('error', "Error al agregar {$item['producto_id']}: {$result['error']}");
            }
        }

        return redirect()->route('ordenes.show', $orden)
            ->with('success', 'Orden creada correctamente.');
    }

    public function show(Orden $orden)
    {
        $orden->load(['detalles.producto', 'cliente', 'usuario', 'pagos', 'entregaEmpresa']);

        return view('ordenes.show', [
            'orden' => $orden,
        ]);
    }

    public function update(Request $request, Orden $orden)
    {
        $validated = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'direccion_entrega' => 'nullable|string',
            'telefono_contacto' => 'nullable|string|max:30',
            'hora_retiro' => 'nullable|date',
            'notas' => 'nullable|string',
        ]);

        $orden->update($validated);

        return redirect()->back()->with('success', 'Orden actualizada.');
    }

    public function destroy(Orden $orden)
    {
        $result = $this->ordenService->anular($orden, request('motivo', 'Anulada por usuario'));
        if (isset($result['error'])) {
            return redirect()->back()->with('error', $result['error']);
        }
        return redirect()->route('ordenes.index')->with('success', 'Orden anulada.');
    }

    public function buscarProducto(Request $request)
    {
        $term = $request->get('q', '');
        $query = Producto::where('activo', true)
            ->where(function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('codigo_barras', 'like', "%{$term}%");
            });

        if ($this->restauranteValidaStock()) {
            $query->where('stock', '>', 0);
        }

        $productos = $query->orderBy('nombre')
            ->limit(20)
            ->get(['id', 'nombre', 'precio', 'codigo_barras', 'stock', 'imagen'])
            ->map(fn($p) => [
                'id'           => $p->id,
                'nombre'       => $p->nombre,
                'precio'       => (float) $p->precio,
                'codigo_barras'=> $p->codigo_barras,
                'stock'        => (int) $p->stock,
                'imagen_url'   => $p->imagen_url,
                'tiene_imagen' => $p->tiene_imagen,
            ]);

        return response()->json($productos);
    }

    public function agregarItem(Request $request, Orden $orden)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'notas' => 'nullable|string|max:200',
            'curso' => 'nullable|string',
        ]);

        $result = $this->ordenService->agregarItem(
            $orden,
            $validated['producto_id'],
            $validated['cantidad'],
            $validated['notas'] ?? null,
            $validated['curso'] ?? null
        );

        if (isset($result['error'])) {
            return redirect()->back()->with('error', $result['error']);
        }

        if ($request->ajax()) {
            return response()->json($result);
        }

        return redirect()->back()->with('success', 'Producto agregado.');
    }

    public function quitarItem(Request $request, Orden $orden, $detalleId)
    {
        $detalle = \App\Models\OrdenDetalle::findOrFail($detalleId);
        $result = $this->ordenService->quitarItem($orden, $detalle);

        if (isset($result['error'])) {
            return $request->ajax()
                ? response()->json($result, $result['code'])
                : redirect()->back()->with('error', $result['error']);
        }

        if ($request->ajax()) {
            return response()->json($result);
        }

        return redirect()->back()->with('success', 'Producto eliminado.');
    }

    public function cobrar(Request $request, Orden $orden)
    {
        $validated = $request->validate([
            'metodo_pago' => 'required|string|in:efectivo,tarjeta,transferencia,mixto',
            'monto_recibido' => 'nullable|numeric|min:0',
            'monto_tarjeta' => 'nullable|numeric|min:0',
            'monto_transferencia' => 'nullable|numeric|min:0',
            'propina' => 'nullable|numeric|min:0',
            'cargo_servicio' => 'nullable|boolean',
        ]);

        $result = $this->paymentService->procesarPago($orden, $validated);

        if (isset($result['error'])) {
            return redirect()->back()->with('error', $result['error']);
        }

        $this->notificationService->sendConfirmation($orden);

        return redirect()->route('ordenes.show', $orden)
            ->with('success', 'Orden cobrada correctamente.');
    }

    public function cambiarEstado(Request $request, Orden $orden)
    {
        $validated = $request->validate([
            'estado' => 'required|string',
        ]);

        $result = $this->ordenService->cambiarEstado($orden, $validated['estado']);
        if (isset($result['error'])) {
            return redirect()->back()->with('error', $result['error']);
        }

        if ($validated['estado'] === 'lista') {
            $this->notificationService->sendReadyForPickup($orden);
        } elseif ($validated['estado'] === 'en_camino') {
            $this->notificationService->sendOnTheWay($orden);
        }

        return redirect()->back()->with('success', 'Estado actualizado.');
    }

    public function ticket(Orden $orden)
    {
        $pdf = Pdf::loadView('ordenes.ticket', [
            'orden' => $orden->load('detalles.producto', 'cliente'),
        ]);

        return $pdf->stream("orden-{$orden->id}-ticket.pdf");
    }

    public function imprimir(Orden $orden)
    {
        try {
            $this->printService->printOrden($orden);
            return redirect()->back()->with('success', 'Impresión enviada.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al imprimir: ' . $e->getMessage());
        }
    }

    private function restauranteValidaStock(): bool
    {
        $user = auth()->user();
        if (!$user || !$user->businessInstance) {
            return true;
        }
        $config = $user->businessInstance->configuracion ?? [];
        return ($config['restaurante_valida_stock'] ?? '1') === '1';
    }
}
