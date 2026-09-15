<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\DeliveryZone;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\VentaDetalle;
use App\Services\DeliveryService;
use App\Services\DriverAssignmentService;
use App\Services\Ecf\EcfService;
use App\Services\RestaurantOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdenController extends Controller
{
    public function __construct(
        protected RestaurantOrderService $orderService,
        protected EcfService $ecfService,
    ) {}

    private function restauranteValidaStock(): bool
    {
        $user = auth()->user();
        if (! $user || ! $user->businessInstance) {
            return true;
        }
        $config = $user->businessInstance->configuracion ?? [];

        return ($config['restaurante_valida_stock'] ?? '1') === '1';
    }

    public function getMesa(Mesa $mesa)
    {
        $mesa->load('ubicacion');
        $orden = $mesa->ordenActiva;
        if ($orden) {
            $orden->load('detalles.producto', 'cliente', 'usuario', 'deliveryCompany');
        }
        $reservacion = $mesa->reservacion;

        return response()->json(compact('mesa', 'orden', 'reservacion'));
    }

    public function catalogo()
    {
        $query = Producto::orderBy('nombre');

        if ($this->restauranteValidaStock()) {
            $query->where('stock', '>', 0);
        }

        $productos = $query->get(['id', 'nombre', 'precio', 'precio_compra', 'itbis_porcentaje', 'stock', 'codigo_barras', 'imagen', 'categoria_id']);

        $categorias = \App\Models\Category::orderBy('nombre')->get(['id', 'nombre']);

        return response()->json(compact('productos', 'categorias'));
    }

    public function buscarProducto(Request $request)
    {
        $termino = $request->input('q');
        if (strlen($termino) < 2) {
            return response()->json([]);
        }
        $query = Producto::where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
                ->orWhere('codigo_barras', 'like', "%{$termino}%");
        });

        if ($this->restauranteValidaStock()) {
            $query->where('stock', '>', 0);
        }

        $productos = $query->limit(20)
            ->get(['id', 'nombre', 'precio', 'precio_compra', 'itbis_porcentaje', 'stock', 'codigo_barras', 'imagen', 'categoria_id']);

        return response()->json($productos);
    }

    public function abrirMesa(Request $request, Mesa $mesa)
    {
        $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'tipo_orden' => 'nullable|in:mesa,delivery,para_llevar',
            'delivery_company_id' => 'nullable|exists:delivery_companies,id|required_if:tipo_orden,delivery',
        ]);

        $result = $this->orderService->abrirMesa(
            $mesa,
            $request->cliente_id,
            $request->tipo_orden,
            $request->delivery_company_id
        );

        if (isset($result['error'])) {
            return response()->json($result, $result['code']);
        }

        return response()->json($result);
    }

    public function agregarItem(Request $request, Mesa $mesa)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'notas' => 'nullable|string|max:200',
            'curso' => 'nullable|in:entrada,fuerte,postre,bebida',
        ]);

        $result = $this->orderService->agregarItem(
            $mesa,
            $request->producto_id,
            $request->cantidad,
            $request->notas,
            $request->curso
        );

        if (isset($result['error'])) {
            return response()->json($result, $result['code']);
        }

        return response()->json($result);
    }

    public function quitarItem(Mesa $mesa, VentaDetalle $detalle)
    {
        $result = $this->orderService->quitarItem($mesa, $detalle);

        if (isset($result['error'])) {
            return response()->json($result, $result['code']);
        }

        return response()->json($result);
    }

    public function actualizarItem(Request $request, Mesa $mesa, VentaDetalle $detalle)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1|max:999',
        ]);

        $result = $this->orderService->actualizarItem($mesa, $detalle, $request->cantidad);

        if (isset($result['error'])) {
            return response()->json($result, $result['code']);
        }

        return response()->json($result);
    }

    public function cobrar(Request $request, Mesa $mesa)
    {
        $request->validate([
            'metodo_pago' => 'required|string|in:efectivo,tarjeta,transferencia,mixto',
            'monto_recibido' => 'nullable|numeric|min:0',
            'monto_tarjeta' => 'nullable|numeric|min:0',
            'monto_transferencia' => 'nullable|numeric|min:0',
            'propina' => 'nullable|numeric|min:0',
            'admin_token' => 'nullable|string',
            'split' => 'nullable|boolean',
            'personas' => 'nullable|integer|min:2|max:10|required_if:split,true',
            'split_persons' => 'nullable|array|required_if:split,true',
            'split_persons.*.num' => 'required_with:split_persons|integer|min:1',
            'split_persons.*.nombre' => 'nullable|string|max:100',
            'split_persons.*.subtotal' => 'required_with:split_persons|numeric|min:0',
            'totales' => 'nullable|array|required_if:split,true',
        ]);

        $result = $this->orderService->cobrar($mesa, $request->all());

        if (isset($result['error'])) {
            return response()->json($result, $result['code']);
        }

        return response()->json($result);
    }

    public function toggleSinItbis(Request $request, Mesa $mesa, VentaDetalle $detalle)
    {
        $request->validate([
            'admin_token' => 'nullable|string',
        ]);

        $result = $this->orderService->toggleSinItbis($mesa, $detalle, $request->input('admin_token'));

        if (isset($result['error'])) {
            return response()->json($result, $result['code']);
        }

        return response()->json($result);
    }

    public function enviarCocina(Request $request, Mesa $mesa)
    {
        $request->validate([
            'detalle_id' => 'nullable|exists:venta_detalles,id',
        ]);

        $result = $this->orderService->enviarACocina($mesa, $request->input('detalle_id'));

        if (isset($result['error'])) {
            return response()->json($result, $result['code']);
        }

        return response()->json($result);
    }

    public function facturar(Request $request, Mesa $mesa)
    {
        $venta = \App\Models\Venta::with('cliente', 'detalles.producto')->findOrFail($request->input('venta_id'));

        if ($venta->mesa_id !== $mesa->id) {
            return response()->json(['error' => 'La venta no pertenece a esta mesa'], 422);
        }
        if ($venta->estado !== 'completada') {
            return response()->json(['error' => 'La orden debe estar pagada antes de facturar'], 422);
        }
        if ($venta->ecf) {
            return response()->json(['error' => 'Esta venta ya tiene un e-CF generado', 'ecf_id' => $venta->ecf->id], 422);
        }
        if ($venta->detalles()->where('sin_itbis', true)->exists()) {
            return response()->json(['error' => 'No se puede facturar un e-CF con líneas sin ITBIS. Aplica el ITBIS a todas las líneas antes de facturar.'], 422);
        }

        try {
            DB::beginTransaction();
            $venta->update(['tipo_comprobante' => 'ecf']);

            $ecf = $this->ecfService->generarEcf($venta);
            $firmado = $this->ecfService->firmar($ecf);
            $this->ecfService->enviar($firmado);
            DB::commit();

            return response()->json([
                'success' => true,
                'encf' => $ecf->encf,
                'ecf_id' => $ecf->id,
                'message' => "e-CF {$ecf->encf} generado y enviado a DGII",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error facturando orden restaurante: '.$e->getMessage());

            return response()->json(['error' => 'Error al facturar: '.$e->getMessage()], 500);
        }
    }

    public function anularOrden(Request $request, Mesa $mesa)
    {
        $request->validate(['motivo' => 'nullable|string|max:500']);
        $result = $this->orderService->anularOrden($mesa, $request->input('motivo', 'Anulación manual'));

        if (isset($result['error'])) {
            return response()->json($result, $result['code']);
        }

        return response()->json($result);
    }

    public function aplicarDescuento(Request $request, Mesa $mesa)
    {
        $request->validate([
            'tipo' => 'required|in:porcentaje,monto',
            'valor' => 'required|numeric|min:0',
            'motivo' => 'required|string|max:200',
        ]);

        $result = $this->orderService->aplicarDescuento($mesa, $request->tipo, (float) $request->valor, $request->motivo);

        if (isset($result['error'])) {
            return response()->json($result, $result['code']);
        }

        return response()->json($result);
    }

    public function trasladarMesa(Request $request, Mesa $mesa)
    {
        $request->validate(['destino_id' => 'required|exists:mesas,id|different:mesa']);
        $destino = Mesa::findOrFail($request->destino_id);

        $result = $this->orderService->trasladarMesa($mesa, $destino);

        if (isset($result['error'])) {
            return response()->json($result, $result['code']);
        }

        return response()->json($result);
    }

    public function historialMesa(Mesa $mesa)
    {
        $ordenes = \App\Models\Venta::deSucursal()->where('mesa_id', $mesa->id)
            ->where('estado', '!=', 'abierta')
            ->with('detalles.producto', 'pagos', 'cliente')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $html = view('restaurante._historial', compact('ordenes', 'mesa'))->render();

        return response()->json(compact('html'));
    }

    public function ticket(Mesa $mesa, Request $request)
    {
        $venta = \App\Models\Venta::with('detalles.producto', 'cliente', 'pagos', 'mesa')
            ->findOrFail($request->input('venta_id'));
        $empresa = (object) config('app.empresa', []);
        $paper = (int) $request->get('paper', 80);

        // Buscar impresora configurada
        $impresora = null;
        if (auth()->check()) {
            $impresora = \App\Models\Impresora::where('activo', true)
                ->where('tenant_id', auth()->user()->business_instance_id)
                ->where('auto_imprimir_ventas', true)
                ->orderBy('orden')
                ->first();
        }

        if ($paper === 80 && $impresora && $impresora->papel_tamano) {
            $paper = (int) preg_replace('/[^0-9]/', '', $impresora->papel_tamano);
            $paper = in_array($paper, [58, 80, 210]) ? $paper : 80;
        }

        return view('restaurante.ticket', compact('venta', 'mesa', 'empresa', 'paper', 'impresora'));
    }

    public function imprimirTicket(Request $request, Mesa $mesa)
    {
        $venta = \App\Models\Venta::findOrFail($request->input('venta_id'));

        return redirect()->route('restaurante.mesa.ticket', ['mesa' => $mesa, 'venta_id' => $venta->id]);
    }

    public function ticketText(Mesa $mesa, Request $request)
    {
        $venta = \App\Models\Venta::with('detalles.producto', 'cliente', 'pagos', 'mesa')
            ->findOrFail($request->input('venta_id'));
        $text = "MESA: {$mesa->numero}\n\n";
        $text .= "Ticket de venta #{$venta->id}\n";

        return response($text, 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Content-Disposition', "inline; filename=ticket-mesa-{$mesa->numero}.txt");
    }

    public function cajasDisponibles()
    {
        $cajas = Caja::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'codigo', 'estado']);

        return response()->json(['cajas' => $cajas]);
    }

    public function sesionActiva()
    {
        $sesion = \App\Models\SesionCaja::with('caja')
            ->where('user_id', auth()->id())
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();

        return response()->json(['sesion' => $sesion]);
    }

    public function abrirCaja(Request $request)
    {
        $data = $request->validate([
            'caja_id' => 'required|exists:cajas,id',
            'monto_inicial' => 'required|numeric|min:0',
        ]);

        $result = $this->orderService->abrirCaja($data['caja_id'], (float) $data['monto_inicial']);

        if (isset($result['error'])) {
            return response()->json($result, $result['code']);
        }

        return response()->json($result);
    }

    public function crearCaja(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'nullable|string|max:20|unique:cajas,codigo',
            'ubicacion' => 'nullable|string|max:100',
        ]);

        $caja = $this->orderService->crearCaja($data['nombre'], $data['codigo'] ?? null, $data['ubicacion'] ?? null);

        return response()->json(['success' => true, 'caja' => $caja]);
    }

    public function resumenCierre(Request $request)
    {
        $request->validate(['caja_id' => 'required|exists:cajas,id']);
        $result = $this->orderService->resumenCierre((int) $request->caja_id);

        if (isset($result['error'])) {
            return response()->json($result, $result['code']);
        }

        return response()->json($result);
    }

    public function cerrarCaja(Request $request)
    {
        $data = $request->validate([
            'caja_id' => 'required|exists:cajas,id',
            'monto_declarado' => 'required|numeric|min:0',
            'cobros_efectivo' => 'required|numeric|min:0',
            'cobros_tarjeta' => 'required|numeric|min:0',
            'cobros_transferencia' => 'required|numeric|min:0',
            'notas' => 'nullable|string|max:500',
        ]);

        $result = $this->orderService->cerrarCaja(
            (int) $data['caja_id'],
            (float) $data['monto_declarado'],
            [
                'efectivo' => (float) $data['cobros_efectivo'],
                'tarjeta' => (float) $data['cobros_tarjeta'],
                'transferencia' => (float) $data['cobros_transferencia'],
            ],
            $data['notas'] ?? null
        );

        if (isset($result['error'])) {
            return response()->json($result, $result['code']);
        }

        return response()->json($result);
    }

    public function cambiarEstado(Request $request, Mesa $mesa)
    {
        $request->validate(['estado' => 'required|string|in:disponible,ocupada,reservada,inactiva']);
        $mesa->update(['estado' => $request->estado]);

        return response()->json(['success' => true, 'mesa' => $mesa]);
    }

    public function savePosicion(Request $request, Mesa $mesa)
    {
        $data = $request->validate(['pos_x' => 'required|integer|min:0', 'pos_y' => 'required|integer|min:0']);
        $mesa->update($data);

        return response()->json(['success' => true]);
    }

    public function populares()
    {
        $productos = Producto::where('tiene_almacen', true)
            ->where('activo', true)
            ->where('precio', '>', 0)
            ->orderBy('ventas_count', 'desc')
            ->take(12)
            ->get(['id', 'nombre', 'precio', 'imagen', 'stock'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'precio' => (float) $p->precio,
                'imagen' => $p->imagen_url,
                'iniciales' => strtoupper(substr($p->nombre, 0, 2)),
                'stock' => (int) $p->stock,
            ]);

        return response()->json($productos);
    }

    public function saveAllPosiciones(Request $request)
    {
        $mesas = $request->validate([
            'mesas' => 'required|array',
            'mesas.*.id' => 'required|exists:mesas,id',
            'mesas.*.pos_x' => 'required|integer',
            'mesas.*.pos_y' => 'required|integer',
        ]);
        foreach ($mesas['mesas'] as $m) {
            Mesa::where('id', $m['id'])->update(['pos_x' => $m['pos_x'], 'pos_y' => $m['pos_y']]);
        }

        return response()->json(['success' => true]);
    }

    // ====== DELIVERY METHODS ======

    public function getDeliveryZones()
    {
        $zones = DeliveryZone::where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'radio_km', 'tarifa_base', 'tarifa_por_km', 'tiempo_estimado_minutos', 'minimo_para_envio_gratis']);

        return response()->json(['zones' => $zones]);
    }

    public function getAvailableDrivers()
    {
        $drivers = app(DriverAssignmentService::class)
            ->obtenerDriversDisponibles()
            ->map(fn ($d) => [
                'id' => $d->id,
                'nombre' => $d->nombreCompleto,
                'telefono' => $d->telefono,
                'ordenes_activas' => $d->ordenes_activas ?? 0,
            ]);

        return response()->json(['drivers' => $drivers]);
    }

    public function configurarDelivery(Request $request, Mesa $mesa)
    {
        $data = $request->validate([
            'direccion_entrega' => 'required|string|max:500',
            'telefono_contacto' => 'nullable|string|max:20',
            'delivery_zone_id' => 'nullable|exists:delivery_zones,id',
            'delivery_fee' => 'nullable|numeric|min:0',
        ]);

        $tenantId = auth()->user()->business_instance_id;

        $venta = $mesa->ventas()
            ->where('tenant_id', $tenantId)
            ->where('estado', 'abierta')
            ->latest()
            ->first();

        if (! $venta) {
            return response()->json(['success' => false, 'message' => 'No hay venta activa para esta mesa.'], 404);
        }

        $venta->update([
            'delivery_address' => $data['direccion_entrega'],
            'delivery_zone_id' => $data['delivery_zone_id'] ?? null,
            'delivery_fee' => $data['delivery_fee'] ?? 0,
        ]);

        if ($mesa->tipo_orden !== 'delivery') {
            $mesa->update(['tipo_orden' => 'delivery']);
        }

        return response()->json(['success' => true, 'message' => 'Delivery configurado.']);
    }

    public function asignarDriver(Request $request, Mesa $mesa)
    {
        $data = $request->validate([
            'driver_id' => 'required|exists:delivery_drivers,id',
        ]);

        $tenantId = auth()->user()->business_instance_id;

        $venta = $mesa->ventas()
            ->where('tenant_id', $tenantId)
            ->where('estado', 'abierta')
            ->latest()
            ->first();

        if (! $venta) {
            return response()->json(['success' => false, 'message' => 'No hay venta activa para esta mesa.'], 404);
        }

        $result = app(DriverAssignmentService::class)
            ->asignarDriverAVenta($venta->id, $data['driver_id']);

        if (isset($result['error'])) {
            return response()->json(['success' => false, 'message' => $result['error']], $result['code'] ?? 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Driver asignado.',
            'driver' => $result['driver'],
        ]);
    }

    public function verificarCobertura(Request $request)
    {
        $data = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'zona_id' => 'required|exists:delivery_zones,id',
        ]);

        $result = app(DeliveryService::class)
            ->verificarZonaCobertura($data['lat'], $data['lng'], $data['zona_id']);

        return response()->json($result);
    }

    public function calcularTarifaDelivery(Request $request)
    {
        $data = $request->validate([
            'distancia_km' => 'required|numeric|min:0',
            'zona_id' => 'required|exists:delivery_zones,id',
        ]);

        $result = app(DeliveryService::class)
            ->calcularTarifaDelivery($data['distancia_km'], $data['zona_id']);

        return response()->json($result);
    }
}
