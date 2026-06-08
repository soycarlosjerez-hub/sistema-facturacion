<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Conduce;
use App\Models\Producto;
use App\Models\Venta;
use App\Services\PrintService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConduceController extends Controller
{
    public function __construct(protected PrintService $printService) {}

    public function index(Request $request)
    {
        $query = Conduce::with(['cliente', 'user', 'items']);

        if ($sucursalId = session('sucursal_id')) {
            $query->where('sucursal_id', $sucursalId);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }
        if ($request->filled('q')) {
            $query->buscar($request->q);
        }

        $conduces = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $stats = [
            'total' => Conduce::count(),
            'borrador' => Conduce::where('estado', 'borrador')->count(),
            'en_transito' => Conduce::where('estado', 'en_transito')->count(),
            'entregados' => Conduce::where('estado', 'entregado')->count(),
            'entregados_hoy' => Conduce::where('estado', 'entregado')
                ->whereDate('fecha_recibido', today())
                ->count(),
            'vencidos' => Conduce::where('estado', 'vencido')->count(),
        ];

        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre', 'rnc_cedula']);
        $estados = Conduce::ESTADOS;

        return view('conduces.index', compact('conduces', 'stats', 'clientes', 'estados'));
    }

    public function create(Request $request)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get(['id', 'nombre', 'codigo_barras as codigo', 'stock', 'unidad_medida']);
        $productos->transform(function ($p) {
            $p->unidad = $p->unidad_medida ?? 'UND';
            return $p;
        });
        $venta = null;

        if ($request->has('from_venta')) {
            $venta = Venta::with('detalles.producto')->findOrFail($request->from_venta);
        }

        return view('conduces.create', compact('clientes', 'productos', 'venta'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateConduce($request);

        try {
            DB::beginTransaction();

            $conduce = Conduce::create([
                'numero' => Conduce::generarNumero(),
                'fecha' => $validated['fecha'],
                'fecha_entrega' => $validated['fecha_entrega'] ?? null,
                'cliente_id' => $validated['cliente_id'],
                'user_id' => Auth::id(),
                'sucursal_id' => session('sucursal_id'),
                'venta_id' => $validated['venta_id'] ?? null,
                'direccion_entrega' => $validated['direccion_entrega'],
                'referencia' => $validated['referencia'] ?? null,
                'contacto_entrega' => $validated['contacto_entrega'] ?? null,
                'telefono_entrega' => $validated['telefono_entrega'] ?? null,
                'transportista' => $validated['transportista'] ?? null,
                'vehiculo' => $validated['vehiculo'] ?? null,
                'placa' => $validated['placa'] ?? null,
                'chofer' => $validated['chofer'] ?? null,
                'chofer_cedula' => $validated['chofer_cedula'] ?? null,
                'observaciones' => $validated['observaciones'] ?? null,
                'estado' => $validated['estado'] ?? 'borrador',
            ]);

            foreach ($validated['items'] as $idx => $item) {
                $conduce->items()->create([
                    'producto_id' => $item['producto_id'] ?? null,
                    'nombre' => $item['nombre'],
                    'codigo' => $item['codigo'] ?? null,
                    'cantidad' => $item['cantidad'],
                    'unidad' => $item['unidad'] ?? 'UND',
                    'peso' => $item['peso'] ?? 0,
                    'orden' => $idx,
                ]);
            }

            $conduce->calcularTotales();
            $conduce->save();

            DB::commit();

            return redirect()->route('conduces.show', $conduce)
                ->with('success', "Conduce {$conduce->numero} creado correctamente.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creando conduce: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al crear el conduce: ' . $e->getMessage());
        }
    }

    public function show(Conduce $conduce)
    {
        $conduce->load(['cliente', 'user', 'items.producto', 'venta']);
        return view('conduces.show', compact('conduce'));
    }

    public function edit(Conduce $conduce)
    {
        if ($conduce->estado === 'entregado') {
            return back()->with('error', 'No se puede editar un conduce ya entregado.');
        }

        $conduce->load('items');
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get(['id', 'nombre', 'codigo_barras as codigo', 'stock', 'unidad_medida']);
        $productos->transform(function ($p) {
            $p->unidad = $p->unidad_medida ?? 'UND';
            return $p;
        });

        return view('conduces.edit', compact('conduce', 'clientes', 'productos'));
    }

    public function update(Request $request, Conduce $conduce)
    {
        if ($conduce->estado === 'entregado') {
            return back()->with('error', 'No se puede modificar un conduce ya entregado.');
        }

        $validated = $this->validateConduce($request);

        try {
            DB::beginTransaction();

            $conduce->update([
                'fecha' => $validated['fecha'],
                'fecha_entrega' => $validated['fecha_entrega'] ?? null,
                'cliente_id' => $validated['cliente_id'],
                'venta_id' => $validated['venta_id'] ?? null,
                'direccion_entrega' => $validated['direccion_entrega'],
                'referencia' => $validated['referencia'] ?? null,
                'contacto_entrega' => $validated['contacto_entrega'] ?? null,
                'telefono_entrega' => $validated['telefono_entrega'] ?? null,
                'transportista' => $validated['transportista'] ?? null,
                'vehiculo' => $validated['vehiculo'] ?? null,
                'placa' => $validated['placa'] ?? null,
                'chofer' => $validated['chofer'] ?? null,
                'chofer_cedula' => $validated['chofer_cedula'] ?? null,
                'observaciones' => $validated['observaciones'] ?? null,
                'estado' => $validated['estado'] ?? $conduce->estado,
            ]);

            $conduce->items()->delete();
            foreach ($validated['items'] as $idx => $item) {
                $conduce->items()->create([
                    'producto_id' => $item['producto_id'] ?? null,
                    'nombre' => $item['nombre'],
                    'codigo' => $item['codigo'] ?? null,
                    'cantidad' => $item['cantidad'],
                    'unidad' => $item['unidad'] ?? 'UND',
                    'peso' => $item['peso'] ?? 0,
                    'orden' => $idx,
                ]);
            }

            $conduce->calcularTotales();
            $conduce->save();

            DB::commit();

            return redirect()->route('conduces.show', $conduce)
                ->with('success', "Conduce {$conduce->numero} actualizado.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error actualizando conduce: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(Conduce $conduce)
    {
        if ($conduce->estado === 'entregado') {
            return back()->with('error', 'No se puede eliminar un conduce ya entregado.');
        }

        try {
            $numero = $conduce->numero;
            $conduce->delete();
            return redirect()->route('conduces.index')
                ->with('success', "Conduce {$numero} eliminado.");
        } catch (\Throwable $e) {
            Log::error('Error eliminando conduce: ' . $e->getMessage());
            return back()->with('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
    }

    public function cambiarEstado(Request $request, Conduce $conduce)
    {
        $validated = $request->validate([
            'estado' => 'required|in:' . implode(',', array_keys(Conduce::ESTADOS)),
        ]);

        try {
            $conduce->cambiarEstado($validated['estado']);
            return back()->with('success', "Estado cambiado a: " . Conduce::ESTADOS[$validated['estado']]['label']);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function entregar(Request $request, Conduce $conduce)
    {
        $validated = $request->validate([
            'recibido_por' => 'required|string|max:255',
            'recibido_cedula' => 'nullable|string|max:20',
            'items_recibidos' => 'required|array',
            'items_recibidos.*' => 'nullable|numeric|min:0',
        ]);

        try {
            $conduce->marcarEntregado(
                $validated['recibido_por'],
                $validated['recibido_cedula'] ?? null,
                $validated['items_recibidos']
            );
            return back()->with('success', "Entrega confirmada para el conduce {$conduce->numero}.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function pdf(Conduce $conduce, Request $request)
    {
        $conduce->load(['cliente', 'user', 'items', 'venta']);
        $empresa = $request->attributes->get('empresa') ?? (object) config('app.empresa', []);
        $pdf = Pdf::loadView('conduces.pdf', compact('conduce', 'empresa'))
            ->setPaper('letter', 'portrait');
        return $pdf->stream("conduce-{$conduce->numero}.pdf");
    }

    public function ticket(Conduce $conduce, Request $request)
    {
        $conduce->load(['cliente', 'items']);
        $paper = (int) $request->get('paper', 80) === 58 ? 58 : 80;
        $empresa = (object) config('app.empresa', []);
        return view('conduces.ticket', compact('conduce', 'paper', 'empresa'));
    }

    public function ticketText(Conduce $conduce)
    {
        $conduce->load(['cliente', 'items']);
        $empresa = (object) config('app.empresa', []);
        $text = $this->printService->renderConduceTicket($conduce, $empresa, 80);
        return response($text, 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Content-Disposition', "inline; filename=conduce-{$conduce->numero}.txt");
    }

    public function fromVenta(Venta $venta)
    {
        $venta->load('detalles.producto', 'cliente');
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get(['id', 'nombre', 'codigo_barras as codigo', 'stock', 'unidad_medida']);
        $productos->transform(function ($p) {
            $p->unidad = $p->unidad_medida ?? 'UND';
            return $p;
        });

        return view('conduces.create', [
            'clientes' => $clientes,
            'productos' => $productos,
            'venta' => $venta,
                'prefillItems' => $venta->detalles->map(fn($d) => [
                    'producto_id' => $d->producto_id,
                    'nombre' => $d->producto?->nombre ?? $d->nombre ?? 'Producto',
                    'codigo' => $d->producto?->codigo_barras,
                    'cantidad' => (float) $d->cantidad,
                    'unidad' => $d->producto?->unidad_medida ?? 'UND',
                    'peso' => 0,
                ])->toArray(),
        ]);
    }

    protected function validateConduce(Request $request): array
    {
        return $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'fecha_entrega' => 'nullable|date|after_or_equal:fecha',
            'estado' => 'nullable|in:' . implode(',', array_keys(Conduce::ESTADOS)),
            'direccion_entrega' => 'required|string|max:500',
            'referencia' => 'nullable|string|max:255',
            'contacto_entrega' => 'nullable|string|max:255',
            'telefono_entrega' => 'nullable|string|max:30',
            'transportista' => 'nullable|string|max:255',
            'vehiculo' => 'nullable|string|max:100',
            'placa' => 'nullable|string|max:20',
            'chofer' => 'nullable|string|max:255',
            'chofer_cedula' => 'nullable|string|max:20',
            'observaciones' => 'nullable|string|max:2000',
            'venta_id' => 'nullable|exists:ventas,id',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'nullable|exists:productos,id',
            'items.*.nombre' => 'required|string|max:255',
            'items.*.codigo' => 'nullable|string|max:100',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.unidad' => 'nullable|string|max:20',
            'items.*.peso' => 'nullable|numeric|min:0',
        ], [
            'cliente_id.required' => 'Selecciona un cliente.',
            'direccion_entrega.required' => 'La dirección de entrega es obligatoria.',
            'items.required' => 'Agrega al menos un producto al conduce.',
            'items.min' => 'Agrega al menos un producto al conduce.',
            'items.*.cantidad.min' => 'La cantidad debe ser mayor a 0.',
        ]);
    }
}
