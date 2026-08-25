<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Lavadero;
use App\Models\LavaderoCita;
use App\Models\LavaderoServicio;
use App\Models\Lavador;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\SesionCaja;
use App\Models\SystemSetting;
use App\Models\Vehiculo;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LavaderoController extends Controller
{
    public function index()
    {
        $servicios = LavaderoServicio::activos()->orderBy('orden')->get();
        return view('lavadero.index', compact('servicios'));
    }

    public function dashboard()
    {
        $ultimosServicios = $this->getUltimosServicios();
        $alertasInventario = $this->getAlertasInventario();

        return view('lavadero.dashboard', compact('ultimosServicios', 'alertasInventario'));
    }

    public function getDashboardData(Request $request)
    {
        $data = app(LavaderoDashboardService::class)->getDashboardData($request->sucursal_id ?? null);

        return response()->json([
            'vehiculos_hoy'      => $data['kpi']['ventas_count_hoy'] ?? 0,
            'ingresos_hoy'       => $data['kpi']['ventas_hoy'] ?? 0,
            'alimentos_hoy'      => $data['ventas_mixtas']['subtotal'] ?? 0,
            'accesorios_hoy'     => $data['ventas_mixtas']['subtotal'] ?? 0,
            'baches_ocupados'    => $data['kpi']['baches_ocupados'] ?? 0,
            'baches_total'       => ($data['kpi']['baches_ocupados'] ?? 0) + ($data['kpi']['vehiculos_proceso'] ?? 0),
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────

    private function getUltimosServicios(): array
    {
        $servicios = LavaderoServicio::where('activo', true)
            ->orderBy('orden')
            ->limit(10)
            ->get();

        return $servicios->map(function ($s) {
            return (object) [
                'cliente'  => null,
                'placa'    => null,
                'servicio' => $s->nombre,
                'lavador'  => null,
                'estado'   => 'pendiente',
            ];
        })->toArray();
    }

    private function getAlertasInventario(): array
    {
        $tenantId = auth()->user()->business_instance_id;

        $productos = Producto::where('tenant_id', $tenantId)
            ->whereNotNull('stock_minimo')
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->select('id', 'nombre', 'stock', 'stock_minimo', 'linea_negocio')
            ->orderBy('stock')
            ->limit(15)
            ->get();

        return $productos->toArray();
    }

    // ── Clientes / Vehículos ────────────────────────────────

    public function buscarCliente(Request $request)
    {
        $q = $request->get('q');
        $clientes = Cliente::where('nombre', 'like', "%{$q}%")
            ->orWhere('rnc_cedula', 'like', "%{$q}%")
            ->orWhere('telefono', 'like', "%{$q}%")
            ->limit(10)->get();
        return response()->json($clientes);
    }

    public function buscarVehiculo(Request $request)
    {
        $q = $request->get('q');
        $vehiculos = Vehiculo::with('cliente')
            ->where('placa', 'like', "%{$q}%")
            ->orWhere('marca', 'like', "%{$q}%")
            ->orWhere('modelo', 'like', "%{$q}%")
            ->limit(10)->get();
        return response()->json($vehiculos);
    }

    public function historialVehiculo(Vehiculo $vehiculo)
    {
        $ventas = Venta::with('detalles', 'pagos')
            ->where('vehiculo_id', $vehiculo->id)
            ->where('estado', '!=', 'abierta')
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        return response()->json([
            'vehiculo' => $vehiculo->load('cliente'),
            'ventas'   => $ventas,
            'total'    => $ventas->sum('total'),
            'visitas'  => $ventas->count(),
        ]);
    }

    public function servicios()
    {
        return response()->json(LavaderoServicio::activos()->orderBy('orden')->get());
    }

    public function createCliente(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:30',
            'rnc_cedula' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $cliente = Cliente::create($data);
        return response()->json($cliente);
    }

    public function createVehiculo(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'placa' => 'nullable|string|max:20',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'anio' => 'nullable|integer|min:1900|max:2099',
            'color' => 'nullable|string|max:50',
        ]);

        $vehiculo = Vehiculo::create($data);
        return response()->json($vehiculo->load('cliente'));
    }

    // ── Cobro / Lavadero ────────────────────────────────────

    public function cobrar(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'vehiculo_id' => 'nullable|exists:vehiculos,id',
            'metodo_pago' => 'required|string|in:efectivo,tarjeta,transferencia',
            'servicios' => 'required|array|min:1',
            'servicios.*.id' => 'required|exists:lavadero_servicios,id',
            'servicios.*.nombre' => 'required|string',
            'servicios.*.precio' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'lavador_ids' => 'nullable|array',
            'lavador_ids.*' => 'exists:lavadores,id',
            'cita_id' => 'nullable|exists:lavadero_citas,id',
            'citas_id' => 'nullable|exists:lavadero_citas,id',
        ]);

        try {
            DB::beginTransaction();

            $tenantId = auth()->user()->business_instance_id;
            $subtotal = collect($data['servicios'])->sum('precio');
            $itbis = $subtotal * (SystemSetting::itbisDefault() / 100);
            $total = $subtotal + $itbis;

            // Sesión de caja
            $isElevated = in_array(auth()->user()->role, ['admin', 'owner', 'admin-business', 'root'])
                || auth()->user()->hasAnyRole(['admin', 'owner', 'admin-business', 'root']);

            $sesionBuilder = SesionCaja::where('estado', 'abierta');
            if (!$isElevated) {
                $sesionBuilder->where('user_id', auth()->id());
            }
            $sesionActiva = $sesionBuilder->first();

            // 1. Registro en la tabla Lavadero (folio, estado, fecha_ingreso)
            $folio = $this->generarFolioLavadero();
            $lavadero = Lavadero::create(array_merge([
                'folio'          => $folio,
                'cliente_id'     => $data['cliente_id'],
                'vehiculo_id'    => $data['vehiculo_id'] ?? null,
                'sucursal_id'    => session('sucursal_id'),
                'user_id'        => auth()->id(),
                'tenant_id'      => $tenantId,
                'fecha_ingreso'  => now(),
                'estado'         => 'en_proceso',
                'servicio'       => collect($data['servicios'])->pluck('nombre')->join(', '),
                'total'          => $total,
            ], $request->only('notas') ? ['notas' => $request->input('notas')] : []));

            // 2. Venta + detalles (registro contable)
            $venta = Venta::create([
                'user_id'         => auth()->id(),
                'cliente_id'      => $data['cliente_id'],
                'vehiculo_id'     => $data['vehiculo_id'] ?? null,
                'tipo_venta_id'   => 1,
                'sucursal_id'     => session('sucursal_id'),
                'caja_id'         => $sesionActiva?->caja_id,
                'sesion_caja_id'  => $sesionActiva?->id,
                'fecha'           => now(),
                'subtotal'        => $subtotal,
                'impuestos'       => $itbis,
                'total'           => $total,
                'estado'          => 'pagada',
                'notas'           => 'Lavado de vehículo',
                'tenant_id'       => $tenantId,
            ]);

            // Producto y almacen del tenant (no globales)
            $prodId = Producto::where('tenant_id', $tenantId)->value('id');
            $almId  = \App\Models\Almacen::where('tenant_id', $tenantId)->value('id');

            foreach ($data['servicios'] as $s) {
                VentaDetalle::create([
                    'venta_id'         => $venta->id,
                    'producto_id'      => $prodId,
                    'almacen_id'       => $almId,
                    'cantidad'         => 1,
                    'precio_unitario'  => $s['precio'],
                    'subtotal'         => $s['precio'],
                    'notas'            => $s['nombre'],
                    'tenant_id'        => $tenantId,
                ]);
            }

            // 3. Pago
            Pago::create([
                'tenant_id'       => $tenantId,
                'venta_id'        => $venta->id,
                'caja_id'         => $sesionActiva?->caja_id,
                'sesion_caja_id'  => $sesionActiva?->id,
                'monto'           => $total,
                'metodo_pago'     => $data['metodo_pago'],
                'fecha_pago'      => now(),
            ]);

            // 4. Asignar lavadores
            if (!empty($data['lavador_ids'])) {
                $this->syncLavadoresEnVenta($venta, $data['lavador_ids'], $subtotal);
            }

            // 5. Actualizar cita vinculada (si existe)
            $citaId = $data['cita_id'] ?? $data['citas_id'];
            if ($citaId) {
                LavaderoCita::where('id', $citaId)
                    ->where('tenant_id', $tenantId)
                    ->update(['estado' => 'completada']);
            }

            DB::commit();

            return response()->json([
                'success'  => true,
                'venta_id' => $venta->id,
                'folio'    => $folio,
                'total'    => $total,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al procesar el cobro: ' . $e->getMessage()], 500);
        }
    }

    private function generarFolioLavadero(): string
    {
        $today = now()->format('Ymd');
        $last = Lavadero::where('folio', 'like', "LV{$today}%")
            ->orderBy('folio', 'desc')
            ->value('folio');

        if ($last) {
            $num = (int) substr($last, strlen('LV' . $today)) + 1;
        } else {
            $num = 1;
        }

        return sprintf('LV%s%04d', $today, $num);
    }

    public function asignarLavadores(Request $request, Venta $venta)
    {
        $data = $request->validate([
            'lavador_ids' => 'nullable|array',
            'lavador_ids.*' => 'exists:lavadores,id',
        ]);

        $subtotal = $venta->subtotal;
        $this->syncLavadoresEnVenta($venta, $data['lavador_ids'] ?? [], $subtotal);

        return back()->with('success', 'Lavadores asignados correctamente');
    }

    private function syncLavadoresEnVenta(Venta $venta, array $lavadorIds, float $subtotal): void
    {
        $pivotData = [];
        $tenantId = auth()->user()->business_instance_id;

        $lavadores = Lavador::where('tenant_id', $tenantId)
            ->whereIn('id', $lavadorIds)
            ->get();

        foreach ($lavadores as $lavador) {
            $pct = $lavador->porcentaje;
            $comision = $subtotal * ($pct / 100);
            $pivotData[$lavador->id] = [
                'porcentaje_aplicado' => $pct,
                'comision'            => $comision,
            ];
        }

        $venta->lavadores()->sync($pivotData);
    }
}
