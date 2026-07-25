<?php

namespace App\Http\Controllers;

use App\Models\ClimatizacionFactura;
use App\Models\Mantenimiento;
use App\Models\Cliente;
use App\Models\ContratoMantenimiento;
use App\Models\OrdenEmergencia;
use App\Models\Instalacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClimatizacionFacturaController extends Controller
{
    public function index(Request $request)
    {
        $query = ClimatizacionFactura::query()
            ->with(['cliente', 'creadoPor'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('origen')) {
            $query->where('origen', $request->origen);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('cliente')) {
            $query->whereHas('cliente', fn($q) => $q->where('nombre', 'like', '%' . $request->cliente . '%'));
        }
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $facturas = $query->paginate(20)->withQueryString();
        $stats = [
            'total_mes' => ClimatizacionFactura::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->sum('total'),
            'pendientes' => ClimatizacionFactura::where('estado', 'borrador')->count(),
            'generadas' => ClimatizacionFactura::where('estado', 'generada')->count(),
        ];

        return view('climatizacion.facturas.index', compact('facturas', 'stats'));
    }

    public function show(ClimatizacionFactura $climatizacionFactura)
    {
        $climatizacionFactura->load(['cliente', 'creadoPor']);
        return view('climatizacion.facturas.show', compact('climatizacionFactura'));
    }

    public function crearDesdeMantenimiento(Mantenimiento $mantenimiento)
    {
        if ($mantenimiento->estado !== 'completado') {
            return back()->with('error', 'Solo se puede facturar un mantenimiento completado.');
        }

        if ($mantenimiento->total <= 0) {
            return back()->with('error', 'El mantenimiento no tiene montos para facturar.');
        }

        // Verificar si ya existe factura para este mantenimiento
        $existente = ClimatizacionFactura::where('origen', 'mantenimiento')
            ->where('origen_id', $mantenimiento->id)
            ->first();

        if ($existente) {
            return redirect()->route('climatizacion.facturas.show', $existente)
                ->with('info', 'Ya existe una factura generada para este mantenimiento.');
        }

        $cliente = $mantenimiento->cliente ?: new Cliente(['nombre' => 'Consumidor Final']);

        $detalle = [];
        if ($mantenimiento->costo_repuestos > 0) {
            // Detalle por repuestos individuales
            if ($mantenimiento->repuestos_usados && count($mantenimiento->repuestos_usados) > 0) {
                foreach ($mantenimiento->repuestos_usados as $repuesto) {
                    $detalle[] = [
                        'descripcion' => $repuesto['nombre'] ?? 'Repuesto',
                        'cantidad' => $repuesto['cantidad'] ?? 1,
                        'precio_unitario' => $repuesto['precio'] ?? 0,
                        'subtotal' => ($repuesto['cantidad'] ?? 1) * ($repuesto['precio'] ?? 0),
                    ];
                }
            } else {
                $detalle[] = [
                    'descripcion' => 'Repuestos - Mantenimiento ' . $mantenimiento->numero,
                    'cantidad' => 1,
                    'precio_unitario' => $mantenimiento->costo_repuestos,
                    'subtotal' => $mantenimiento->costo_repuestos,
                ];
            }
        }

        if ($mantenimiento->mano_de_obra > 0) {
            $detalle[] = [
                'descripcion' => 'Mano de Obra - Mantenimiento ' . $mantenimiento->numero,
                'cantidad' => 1,
                'precio_unitario' => $mantenimiento->mano_de_obra,
                'subtotal' => $mantenimiento->mano_de_obra,
            ];
        }

        $subtotal = $mantenimiento->costo_repuestos + $mantenimiento->mano_de_obra;
        $itbis = $subtotal * 0.18;
        $total = $subtotal + $itbis;

        try {
            DB::beginTransaction();

            $factura = ClimatizacionFactura::create([
                'business_instance_id' => $mantenimiento->business_instance_id,
                'cliente_id' => $mantenimiento->cliente_id,
                'created_by' => auth()->id(),
                'origen' => 'mantenimiento',
                'origen_id' => $mantenimiento->id,
                'referencia' => $mantenimiento->numero,
                'subtotal' => round($subtotal, 2),
                'itbis' => round($itbis, 2),
                'descuento' => 0,
                'total' => round($total, 2),
                'estado' => 'borrador',
                'detalle' => $detalle,
            ]);

            DB::commit();

            return redirect()->route('climatizacion.facturas.show', $factura)
                ->with('success', 'Factura borrador creada exitosamente. Total: RD$ ' . number_format($total, 2));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear factura: ' . $e->getMessage());
        }
    }

    public function generarDesdeContrato(ContratoMantenimiento $contrato)
    {
        if ($contrato->estado !== 'activo') {
            return back()->with('error', 'Solo se puede facturar cuotas de contratos activos.');
        }

        $existente = ClimatizacionFactura::where('origen', 'contrato_cuota')
            ->where('origen_id', $contrato->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->first();

        if ($existente) {
            return redirect()->route('climatizacion.facturas.show', $existente)
                ->with('info', 'Ya existe una factura de cuota para este contrato este mes.');
        }

        $valor = $contrato->valor_mensual;
        $itbis = $valor * 0.18;
        $total = $valor + $itbis;

        try {
            DB::beginTransaction();

            $factura = ClimatizacionFactura::create([
                'business_instance_id' => $contrato->business_instance_id,
                'cliente_id' => $contrato->cliente_id,
                'created_by' => auth()->id(),
                'origen' => 'contrato_cuota',
                'origen_id' => $contrato->id,
                'referencia' => $contrato->codigo,
                'subtotal' => round($valor, 2),
                'itbis' => round($itbis, 2),
                'descuento' => 0,
                'total' => round($total, 2),
                'estado' => 'borrador',
                'detalle' => [[
                    'descripcion' => 'Cuota ' . ucfirst($contrato->tipo_periodicidad) . ' - Contrato ' . $contrato->codigo,
                    'cantidad' => 1,
                    'precio_unitario' => $valor,
                    'subtotal' => $valor,
                ]],
            ]);

            DB::commit();

            return redirect()->route('climatizacion.facturas.show', $factura)
                ->with('success', 'Factura de cuota generada. Total: RD$ ' . number_format($total, 2));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al generar factura: ' . $e->getMessage());
        }
    }

    public function generarDesdeEmergencia(OrdenEmergencia $orden)
    {
        if (!in_array($orden->estado, ['resuelta', 'cerrada'])) {
            return back()->with('error', 'Solo se puede facturar una emergencia resuelta o cerrada.');
        }

        $costoFinal = $orden->costo_final ?? 0;
        if ($costoFinal <= 0) {
            return back()->with('error', 'La emergencia no tiene costo final definido.');
        }

        $existente = ClimatizacionFactura::where('origen', 'emergencia')
            ->where('origen_id', $orden->id)
            ->first();

        if ($existente) {
            return redirect()->route('climatizacion.facturas.show', $existente)
                ->with('info', 'Ya existe una factura para esta emergencia.');
        }

        $subtotal = $costoFinal;
        $itbis = $subtotal * 0.18;
        $total = $subtotal + $itbis;

        try {
            DB::beginTransaction();

            $factura = ClimatizacionFactura::create([
                'business_instance_id' => $orden->business_instance_id,
                'cliente_id' => $orden->cliente_id,
                'created_by' => auth()->id(),
                'origen' => 'emergencia',
                'origen_id' => $orden->id,
                'referencia' => $orden->codigo,
                'subtotal' => round($subtotal, 2),
                'itbis' => round($itbis, 2),
                'descuento' => 0,
                'total' => round($total, 2),
                'estado' => 'borrador',
                'detalle' => [[
                    'descripcion' => 'Servicio de Emergencia - ' . ($orden->tipo_falla ?? 'Sin especificar'),
                    'cantidad' => 1,
                    'precio_unitario' => $costoFinal,
                    'subtotal' => $costoFinal,
                ]],
            ]);

            DB::commit();

            return redirect()->route('climatizacion.facturas.show', $factura)
                ->with('success', 'Factura de emergencia generada. Total: RD$ ' . number_format($total, 2));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al generar factura: ' . $e->getMessage());
        }
    }

    public function generarDesdeInstalacion(Instalacion $instalacion)
    {
        if ($instalacion->estado !== 'completada') {
            return back()->with('error', 'Solo se puede facturar una instalación completada.');
        }

        if ($instalacion->total <= 0) {
            return back()->with('error', 'La instalación no tiene montos para facturar.');
        }

        $existente = ClimatizacionFactura::where('origen', 'instalacion')
            ->where('origen_id', $instalacion->id)
            ->first();

        if ($existente) {
            return redirect()->route('climatizacion.facturas.show', $existente)
                ->with('info', 'Ya existe una factura para esta instalación.');
        }

        $subtotal = $instalacion->total;
        $itbis = $subtotal * 0.18;
        $total = $subtotal + $itbis;

        $detalle = [];
        if ($instalacion->productos && $instalacion->productos->count() > 0) {
            foreach ($instalacion->productos as $producto) {
                $detalle[] = [
                    'descripcion' => $producto->nombre,
                    'cantidad' => $producto->pivot->cantidad ?? 1,
                    'precio_unitario' => $producto->pivot->precio_unitario ?? 0,
                    'subtotal' => ($producto->pivot->cantidad ?? 1) * ($producto->pivot->precio_unitario ?? 0),
                ];
            }
        } else {
            $detalle[] = [
                'descripcion' => 'Instalación de equipos - ' . $instalacion->numero,
                'cantidad' => 1,
                'precio_unitario' => $subtotal,
                'subtotal' => $subtotal,
            ];
        }

        try {
            DB::beginTransaction();

            $factura = ClimatizacionFactura::create([
                'business_instance_id' => $instalacion->business_instance_id,
                'cliente_id' => $instalacion->cliente_id,
                'created_by' => auth()->id(),
                'origen' => 'instalacion',
                'origen_id' => $instalacion->id,
                'referencia' => $instalacion->numero,
                'subtotal' => round($subtotal, 2),
                'itbis' => round($itbis, 2),
                'descuento' => 0,
                'total' => round($total, 2),
                'estado' => 'borrador',
                'detalle' => $detalle,
            ]);

            DB::commit();

            return redirect()->route('climatizacion.facturas.show', $factura)
                ->with('success', 'Factura de instalación generada. Total: RD$ ' . number_format($total, 2));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al generar factura: ' . $e->getMessage());
        }
    }

    public function anular(ClimatizacionFactura $climatizacionFactura)
    {
        if ($climatizacionFactura->estado === 'anulada') {
            return back()->with('error', 'Esta factura ya está anulada.');
        }

        $climatizacionFactura->update(['estado' => 'anulada']);

        return redirect()->route('climatizacion.facturas.index')
            ->with('success', 'Factura anulada correctamente.');
    }

    public function generar(ClimatizacionFactura $climatizacionFactura)
    {
        if ($climatizacionFactura->estado !== 'borrador') {
            return response()->json(['error' => 'Solo se pueden generar facturas en estado borrador.'], 422);
        }

        try {
            // Aquí iría la integración con el servicio DGII/e-CF
            // Por ahora se marca como generada
            $climatizacionFactura->update(['estado' => 'generada']);

            return response()->json([
                'success' => true,
                'message' => 'Factura generada exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar factura: ' . $e->getMessage(),
            ], 500);
        }
    }
}
