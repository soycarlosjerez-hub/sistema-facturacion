<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use App\Services\DevolucionService;
use Illuminate\Http\Request;

class DevolucionController extends Controller
{
    public function __construct(
        protected DevolucionService $devolucionService
    ) {}

    public function index(Request $request)
    {
        $devoluciones = $this->devolucionService->list($request->all());
        return view('devoluciones.index', compact('devoluciones'));
    }

    public function create(Request $request)
    {
        return view('devoluciones.create', $this->devolucionService->getCreateData($request->integer('venta_id')));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'venta_id'                       => 'nullable|exists:ventas,id',
            'cliente_id'                     => 'required|exists:clientes,id',
            'fecha'                          => 'required|date',
            'motivo'                         => 'required|string|min:5',
            'tipo'                           => 'required|in:parcial,total',
            'items'                          => 'required|array|min:1',
            'items.*.producto_id'            => 'required|exists:productos,id',
            'items.*.cantidad'               => 'required|numeric|min:0.01',
            'items.*.precio_unitario'        => 'required|numeric|min:0',
            'items.*.itbis_porcentaje'       => 'nullable|numeric|min:0|max:100',
            'items.*.motivo'                 => 'nullable|string|max:500',
        ]);

        try {
            $devolucion = $this->devolucionService->create($data);
            return redirect()->route('devoluciones.show', $devolucion)
                ->with('success', 'Devolución registrada. Revisa y confirma para completar el reintegro de stock.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al registrar devolución: ' . $e->getMessage());
        }
    }

    public function show(Devolucion $devolucion)
    {
        $devolucion->load(['venta', 'cliente', 'user', 'detalles.producto', 'notaCredito']);
        return view('devoluciones.show', compact('devolucion'));
    }

    public function confirmar(Devolucion $devolucion)
    {
        try {
            $this->devolucionService->confirmar($devolucion);
            return redirect()->route('devoluciones.show', $devolucion)
                ->with('success', 'Devolución completada. Stock reintegrado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function generarNotaCredito(Devolucion $devolucion)
    {
        try {
            $this->devolucionService->generarNotaCredito($devolucion);
            return redirect()->route('devoluciones.show', $devolucion)
                ->with('success', 'Nota de Crédito E34 generada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Devolucion $devolucion)
    {
        try {
            $this->devolucionService->delete($devolucion);
            return redirect()->route('devoluciones.index')->with('success', 'Devolución eliminada.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function buscarVenta(Request $request)
    {
        return response()->json($this->devolucionService->buscarVenta($request->get('q', '')));
    }
}
