<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\CotizacionItem;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\TipoVenta;
use App\Services\CotizacionEmailService;
use App\Services\PrintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CotizacionController extends Controller
{
    protected CotizacionEmailService $emailService;
    protected PrintService $printService;

    public function __construct(CotizacionEmailService $emailService, PrintService $printService)
    {
        $this->emailService = $emailService;
        $this->printService = $printService;
        $this->middleware('auth');
        $this->middleware('permission:cotizaciones.view')->only(['index', 'show', 'pdf', 'ticket']);
        $this->middleware('permission:cotizaciones.create')->only(['create', 'store']);
        $this->middleware('permission:cotizaciones.edit')->only(['edit', 'update', 'cambiarEstado', 'enviar']);
        $this->middleware('permission:cotizaciones.delete')->only(['destroy']);
    }

    /**
     * Listado de cotizaciones
     */
    public function index(Request $request)
    {
        $query = Cotizacion::with(['cliente', 'user', 'sucursal'])
            ->orderBy('id', 'desc');

        if ($sucursalId = session('sucursal_id')) {
            $query->where('sucursal_id', $sucursalId);
        }

        // Filtros
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('numero', 'like', "%{$buscar}%")
                  ->orWhereHas('cliente', function ($cq) use ($buscar) {
                      $cq->where('nombre', 'like', "%{$buscar}%")
                         ->orWhere('documento', 'like', "%{$buscar}%");
                  });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        if ($request->filled('vencidas')) {
            $query->vencidas();
        }

        $cotizaciones = $query->paginate(20)->withQueryString();

        // Estadísticas
        $stats = [
            'total' => Cotizacion::count(),
            'pendientes' => Cotizacion::whereIn('estado', ['borrador', 'enviada'])->count(),
            'aprobadas' => Cotizacion::where('estado', 'aprobada')->count(),
            'vencidas' => Cotizacion::vencidas()->count(),
            'convertidas' => Cotizacion::where('estado', 'convertida')->count(),
            'monto_total' => Cotizacion::whereIn('estado', ['borrador', 'enviada', 'aprobada'])->sum('total'),
        ];

        return view('cotizaciones.index', compact('cotizaciones', 'stats'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')
            ->get(['id', 'codigo_barras', 'nombre', 'precio', 'itbis_porcentaje', 'unidad_medida', 'stock', 'imagen']);
        
        $numero = Cotizacion::generarNumero();
        
        return view('cotizaciones.create', compact('clientes', 'productos', 'numero'));
    }

    /**
     * Guardar nueva cotización
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'fecha' => 'required|date',
            'fecha_validez' => 'required|date|after_or_equal:fecha',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'nullable|exists:productos,id',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
            'condiciones' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $cotizacion = Cotizacion::create([
                'numero' => Cotizacion::generarNumero(),
                'cliente_id' => $request->cliente_id,
                'user_id' => auth()->id(),
                'sucursal_id' => session('sucursal_id'),
                'fecha' => $request->fecha,
                'fecha_validez' => $request->fecha_validez,
                'estado' => $request->estado ?? 'borrador',
                'descuento' => $request->descuento ?? 0,
                'notas' => $request->notas,
                'condiciones' => $request->condiciones,
            ]);

            foreach ($request->items as $i => $itemData) {
                $producto = null;
                if (!empty($itemData['producto_id'])) {
                    $producto = Producto::find($itemData['producto_id']);
                }

                $item = new CotizacionItem([
                    'cotizacion_id' => $cotizacion->id,
                    'producto_id' => $itemData['producto_id'] ?? null,
                    'codigo' => $producto?->codigo_barras,
                    'nombre' => $itemData['nombre'] ?? $producto?->nombre ?? 'Item',
                    'descripcion' => $itemData['descripcion'] ?? null,
                    'unidad' => $itemData['unidad'] ?? $producto?->unidad_medida ?? 'Unidad',
                    'cantidad' => $itemData['cantidad'],
                    'precio_unitario' => $itemData['precio_unitario'],
                    'descuento' => $itemData['descuento'] ?? 0,
                    'itbis_porcentaje' => $itemData['itbis_porcentaje'] ?? ($producto?->itbis_porcentaje ?? 18),
                    'orden' => $i,
                ]);

                $item->calcular();
                $item->save();
            }

            $cotizacion->calcularTotales();

            DB::commit();

            return redirect()
                ->route('cotizaciones.show', $cotizacion)
                ->with('success', "Cotización {$cotizacion->numero} creada exitosamente");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear cotización: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Error al crear la cotización: ' . $e->getMessage());
        }
    }

    /**
     * Ver detalle de cotización
     */
    public function show(Cotizacion $cotizacione)
    {
        $cotizacione->load(['cliente', 'user', 'items.producto', 'venta']);
        
        return view('cotizaciones.show', [
            'cotizacion' => $cotizacione,
        ]);
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Cotizacion $cotizacione)
    {
        if (in_array($cotizacione->estado, ['convertida', 'anulada'])) {
            return back()->with('error', 'No se puede editar una cotización ' . $cotizacione->estado);
        }

        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')
            ->get(['id', 'codigo_barras', 'nombre', 'precio', 'itbis_porcentaje', 'unidad_medida', 'stock', 'imagen']);
        
        $cotizacione->load('items');
        
        return view('cotizaciones.edit', [
            'cotizacion' => $cotizacione,
            'clientes' => $clientes,
            'productos' => $productos,
        ]);
    }

    /**
     * Actualizar cotización
     */
    public function update(Request $request, Cotizacion $cotizacione)
    {
        if (in_array($cotizacione->estado, ['convertida', 'anulada'])) {
            return back()->with('error', 'No se puede editar una cotización ' . $cotizacione->estado);
        }

        $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'fecha' => 'required|date',
            'fecha_validez' => 'required|date|after_or_equal:fecha',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'nullable|exists:productos,id',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $cotizacione->update([
                'cliente_id' => $request->cliente_id,
                'fecha' => $request->fecha,
                'fecha_validez' => $request->fecha_validez,
                'descuento' => $request->descuento ?? 0,
                'notas' => $request->notas,
                'condiciones' => $request->condiciones,
            ]);

            // Eliminar items anteriores y crear nuevos
            $cotizacione->items()->delete();

            foreach ($request->items as $i => $itemData) {
                $producto = !empty($itemData['producto_id']) ? Producto::find($itemData['producto_id']) : null;

                $item = new CotizacionItem([
                    'cotizacion_id' => $cotizacione->id,
                    'producto_id' => $itemData['producto_id'] ?? null,
                    'codigo' => $producto?->codigo_barras,
                    'nombre' => $itemData['nombre'] ?? $producto?->nombre ?? 'Item',
                    'descripcion' => $itemData['descripcion'] ?? null,
                    'unidad' => $itemData['unidad'] ?? $producto?->unidad_medida ?? 'Unidad',
                    'cantidad' => $itemData['cantidad'],
                    'precio_unitario' => $itemData['precio_unitario'],
                    'descuento' => $itemData['descuento'] ?? 0,
                    'itbis_porcentaje' => $itemData['itbis_porcentaje'] ?? ($producto?->itbis_porcentaje ?? 18),
                    'orden' => $i,
                ]);

                $item->calcular();
                $item->save();
            }

            $cotizacione->calcularTotales();

            DB::commit();

            return redirect()
                ->route('cotizaciones.show', $cotizacione)
                ->with('success', 'Cotización actualizada');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar cotización: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar cotización
     */
    public function destroy(Cotizacion $cotizacione)
    {
        if ($cotizacione->estado === 'convertida') {
            return back()->with('error', 'No se puede eliminar una cotización convertida en venta');
        }

        try {
            $numero = $cotizacione->numero;
            $cotizacione->delete();
            
            return redirect()
                ->route('cotizaciones.index')
                ->with('success', "Cotización {$numero} eliminada");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado de la cotización
     */
    public function cambiarEstado(Request $request, Cotizacion $cotizacione)
    {
        $request->validate([
            'estado' => 'required|in:borrador,enviada,aprobada,rechazada,vencida,anulada',
        ]);

        $estadoAnterior = $cotizacione->estado;
        $estadoNuevo = $request->estado;
        $enviarEmail = $request->boolean('enviar_email') 
            || ($estadoAnterior !== 'enviada' && $estadoNuevo === 'enviada');
        
        $cotizacione->update(['estado' => $estadoNuevo]);

        $mensaje = 'Estado actualizado a: ' . $cotizacione->estado_label;

        if ($enviarEmail) {
            $resultadoEmail = $this->emailService->enviar(
                $cotizacione, 
                $request->input('mensaje_email', '')
            );
            if ($resultadoEmail['success']) {
                $mensaje .= ' | Email enviado a ' . $resultadoEmail['destinatario'];
            } else {
                $mensaje .= ' | ⚠️ No se pudo enviar email: ' . $resultadoEmail['error'];
            }
        }

        return back()->with('success', $mensaje);
    }

    /**
     * Enviar cotización por email al cliente
     */
    public function enviar(Request $request, Cotizacion $cotizacione)
    {
        $request->validate([
            'email_destino' => 'nullable|email',
            'mensaje' => 'nullable|string|max:1000',
            'incluir_pdf' => 'nullable|boolean',
        ]);

        $resultado = $this->emailService->enviar(
            $cotizacione,
            $request->input('mensaje', ''),
            $request->input('email_destino'),
            $request->boolean('incluir_pdf', true)
        );

        if ($resultado['success']) {
            return back()->with('success', 
                "Cotización enviada por email a {$resultado['destinatario']}" 
                . (isset($resultado['mail_id']) && $resultado['mail_id'] ? " (ID: {$resultado['mail_id']})" : '')
            );
        }

        return back()->with('error', 'Error al enviar email: ' . $resultado['error']);
    }

    /**
     * Convertir cotización en venta
     */
    public function convertirAVenta(Request $request, Cotizacion $cotizacione)
    {
        if (!$cotizacione->puede_convertirse) {
            return back()->with('error', 'Esta cotización no puede ser convertida a venta');
        }

        try {
            DB::beginTransaction();

            $tipoVenta = TipoVenta::where('nombre', 'Contado')->first() ?? TipoVenta::first();
            
            if (!$tipoVenta) {
                throw new \Exception('No hay tipos de venta configurados');
            }

            $almacenId = \App\Models\Almacen::first()?->id ?? 1;

            $venta = Venta::create([
                'cliente_id' => $cotizacione->cliente_id,
                'user_id' => auth()->id() ?? $cotizacione->user_id,
                'tipo_venta_id' => $tipoVenta->id,
                'caja_id' => $this->getCajaActualId(),
                'fecha' => now(),
                'subtotal' => (float) $cotizacione->subtotal,
                'descuento' => (float) ($cotizacione->descuento ?? 0),
                'impuestos' => (float) $cotizacione->itbis,
                'total' => (float) $cotizacione->total,
                'tipo_comprobante' => 'ecf',
                'estado' => 'completada',
            ]);

            foreach ($cotizacione->items as $item) {
                $subtotalItem = ($item->cantidad * $item->precio_unitario) - $item->descuento;
                
                $venta->detalles()->create([
                    'producto_id' => $item->producto_id,
                    'almacen_id' => $almacenId,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $item->precio_unitario,
                    'subtotal' => $subtotalItem,
                ]);
            }

            $cotizacione->update([
                'estado' => 'convertida',
                'venta_id' => $venta->id,
                'convertida_en' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('ventas.show', $venta)
                ->with('success', "Cotización convertida a venta #{$venta->id}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al convertir cotización: ' . $e->getMessage());
            return back()->with('error', 'Error al convertir: ' . $e->getMessage());
        }
    }

    /**
     * Obtener el ID de la caja actual del usuario
     */
    private function getCajaActualId(): ?int
    {
        try {
            $sesion = \App\Models\SesionCaja::where('user_id', auth()->id() ?? 0)
                ->where('estado', 'abierta')
                ->latest('id')
                ->first();
            
            return $sesion?->caja_id;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Vista de impresión
     */
    public function pdf(Cotizacion $cotizacione)
    {
        $cotizacione->load(['cliente', 'user', 'items']);
        $cotizacione->calcularTotales();
        
        $pdf = \PDF::loadView('cotizaciones.pdf', compact('cotizacione'));
        return $pdf->stream("cotizacion-{$cotizacione->numero}.pdf");
    }

    /**
     * Vista imprimible optimizada para ticket (80mm o 58mm)
     */
    public function ticket(Request $request, Cotizacion $cotizacione)
    {
        $cotizacione->load(['cliente', 'user', 'items']);
        $paperWidth = (int) $request->input('paper', 80);
        $autoPrint = $request->boolean('autoprint', true);
        
        return view('cotizaciones.ticket', [
            'cotizacion' => $cotizacione,
            'paperWidth' => $paperWidth,
            'autoPrint' => $autoPrint,
        ]);
    }

    /**
     * Generar ticket como texto plano (para descarga o impresión directa)
     */
    public function ticketText(Request $request, Cotizacion $cotizacione)
    {
        $cotizacione->load(['cliente', 'user', 'items']);
        $paperWidth = (int) $request->input('paper', 80);
        $format = $request->input('format', 'txt'); // txt o escpos
        
        $content = $this->printService->renderCotizacionTicket($cotizacione, $paperWidth);
        
        if ($format === 'escpos') {
            $content = $this->printService->toEscPos($content);
            $filename = "cotizacion-{$cotizacione->numero}.bin";
            $mimeType = 'application/octet-stream';
        } else {
            $filename = "cotizacion-{$cotizacione->numero}.txt";
            $mimeType = 'text/plain';
        }
        
        return response($content, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    /**
     * Búsqueda de productos para autocomplete
     */
    public function buscarProductos(Request $request)
    {
        $buscar = $request->get('q', '');
        
        $productos = Producto::where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('codigo_barras', 'like', "%{$buscar}%");
            })
            ->limit(20)
            ->get(['id', 'codigo_barras', 'nombre', 'precio', 'itbis_porcentaje', 'unidad_medida', 'stock', 'imagen']);
        
        return response()->json($productos->map(function ($p) {
            return [
                'id' => $p->id,
                'codigo' => $p->codigo_barras,
                'nombre' => $p->nombre,
                'precio' => (float) $p->precio,
                'itbis_porcentaje' => (float) $p->itbis_porcentaje,
                'unidad' => $p->unidad_medida ?? 'Unidad',
                'stock' => (float) $p->stock,
                'imagen' => $p->imagen_url,
            ];
        }));
    }
}
