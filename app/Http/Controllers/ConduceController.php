<?php

namespace App\Http\Controllers;

use App\Models\Conduce;
use App\Models\Venta;
use App\Services\ConduceService;
use App\Services\PrintService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ConduceController extends Controller
{
    public function __construct(
        protected ConduceService $conduceService,
        protected PrintService $printService
    ) {}

    public function index(Request $request)
    {
        return view('conduces.index', $this->conduceService->list($request->all()));
    }

    public function create(Request $request)
    {
        return view('conduces.create', $this->conduceService->getCreateData($request->integer('from_venta', null) ?: null));
    }

    public function store(Request $request)
    {
        $validated = $this->validateConduce($request);

        try {
            $conduce = $this->conduceService->create($validated);
            return redirect()->route('conduces.show', $conduce)
                ->with('success', "Conduce {$conduce->numero} creado correctamente.");
        } catch (\Throwable $e) {
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
        return view('conduces.edit', $this->conduceService->getEditData($conduce));
    }

    public function update(Request $request, Conduce $conduce)
    {
        $validated = $this->validateConduce($request);

        try {
            $this->conduceService->update($conduce, $validated);
            return redirect()->route('conduces.show', $conduce)
                ->with('success', "Conduce {$conduce->numero} actualizado.");
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(Conduce $conduce)
    {
        try {
            $numero = $conduce->numero;
            $this->conduceService->delete($conduce);
            return redirect()->route('conduces.index')
                ->with('success', "Conduce {$numero} eliminado.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cambiarEstado(Request $request, Conduce $conduce)
    {
        $validated = $request->validate([
            'estado' => 'required|in:' . implode(',', array_keys(Conduce::ESTADOS)),
        ]);

        try {
            $this->conduceService->cambiarEstado($conduce, $validated['estado']);
            return back()->with('success', "Estado cambiado a: " . Conduce::ESTADOS[$validated['estado']]['label']);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function entregar(Request $request, Conduce $conduce)
    {
        $validated = $request->validate([
            'recibido_por'         => 'required|string|max:255',
            'recibido_cedula'      => 'nullable|string|max:20',
            'items_recibidos'      => 'required|array',
            'items_recibidos.*'    => 'nullable|numeric|min:0',
        ]);

        try {
            $this->conduceService->entregar(
                $conduce,
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
        return Pdf::loadView('conduces.pdf', compact('conduce', 'empresa'))
            ->setPaper('letter', 'portrait')
            ->stream("conduce-{$conduce->numero}.pdf");
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
        return view('conduces.create', $this->conduceService->fromVenta($venta));
    }

    protected function validateConduce(Request $request): array
    {
        return $request->validate([
            'cliente_id'              => 'required|exists:clientes,id',
            'fecha'                   => 'required|date',
            'fecha_entrega'           => 'nullable|date|after_or_equal:fecha',
            'estado'                  => 'nullable|in:' . implode(',', array_keys(Conduce::ESTADOS)),
            'direccion_entrega'       => 'required|string|max:500',
            'referencia'              => 'nullable|string|max:255',
            'contacto_entrega'        => 'nullable|string|max:255',
            'telefono_entrega'        => 'nullable|string|max:30',
            'transportista'           => 'nullable|string|max:255',
            'vehiculo'                => 'nullable|string|max:100',
            'placa'                   => 'nullable|string|max:20',
            'chofer'                  => 'nullable|string|max:255',
            'chofer_cedula'           => 'nullable|string|max:20',
            'observaciones'           => 'nullable|string|max:2000',
            'venta_id'                => 'nullable|exists:ventas,id',
            'items'                   => 'required|array|min:1',
            'items.*.producto_id'     => 'nullable|exists:productos,id',
            'items.*.nombre'          => 'required|string|max:255',
            'items.*.codigo'          => 'nullable|string|max:100',
            'items.*.cantidad'        => 'required|numeric|min:0.01',
            'items.*.unidad'          => 'nullable|string|max:20',
            'items.*.peso'            => 'nullable|numeric|min:0',
        ], [
            'cliente_id.required'       => 'Selecciona un cliente.',
            'direccion_entrega.required' => 'La dirección de entrega es obligatoria.',
            'items.required'            => 'Agrega al menos un producto al conduce.',
            'items.min'                 => 'Agrega al menos un producto al conduce.',
            'items.*.cantidad.min'      => 'La cantidad debe ser mayor a 0.',
        ]);
    }
}
