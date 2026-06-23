<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\LavaderoServicio;
use App\Models\Vehiculo;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Pago;
use App\Models\SesionCaja;
use App\Models\Lavador;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LavaderoController extends Controller
{
    public function index()
    {
        $servicios = LavaderoServicio::activos()->orderBy('orden')->get();
        return view('lavadero.index', compact('servicios'));
    }

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
        ]);

        try {
            DB::beginTransaction();

            $subtotal = collect($data['servicios'])->sum('precio');
            $itbis = $subtotal * 0.18;
            $total = $subtotal + $itbis;

            $sesionActiva = SesionCaja::where('user_id', auth()->id())
                ->where('estado', 'abierta')
                ->first();

            $venta = Venta::create([
                'user_id' => auth()->id(),
                'cliente_id' => $data['cliente_id'],
                'vehiculo_id' => $data['vehiculo_id'] ?? null,
                'tipo_venta_id' => 1,
                'sucursal_id' => session('sucursal_id'),
                'caja_id' => $sesionActiva?->caja_id,
                'sesion_caja_id' => $sesionActiva?->id,
                'fecha' => now(),
                'subtotal' => $subtotal,
                'impuestos' => $itbis,
                'total' => $total,
                'estado' => 'pagada',
                'notas' => 'Lavado de vehículo',
            ]);

            $prodId = \App\Models\Producto::value('id');
            $almId = \App\Models\Almacen::value('id');

            foreach ($data['servicios'] as $s) {
                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $prodId,
                    'almacen_id' => $almId,
                    'cantidad' => 1,
                    'precio_unitario' => $s['precio'],
                    'subtotal' => $s['precio'],
                    'notas' => $s['nombre'],
                ]);
            }

            Pago::create([
                'tenant_id' => auth()->user()->business_instance_id,
                'venta_id' => $venta->id,
                'caja_id' => $sesionActiva?->caja_id,
                'sesion_caja_id' => $sesionActiva?->id,
                'monto' => $total,
                'metodo_pago' => $data['metodo_pago'],
                'fecha_pago' => now(),
            ]);

            if (!empty($data['lavador_ids'])) {
                $this->syncLavadoresEnVenta($venta, $data['lavador_ids'], $subtotal);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'venta_id' => $venta->id,
                'total' => $total,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al procesar el cobro: ' . $e->getMessage()], 500);
        }
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
        $lavadores = Lavador::whereIn('id', $lavadorIds)->get();

        foreach ($lavadores as $lavador) {
            $pct = $lavador->porcentaje;
            $comision = $subtotal * ($pct / 100);
            $pivotData[$lavador->id] = [
                'porcentaje_aplicado' => $pct,
                'comision' => $comision,
            ];
        }

        $venta->lavadores()->sync($pivotData);
    }
}
