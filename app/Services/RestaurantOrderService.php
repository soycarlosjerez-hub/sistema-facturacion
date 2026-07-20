<?php

namespace App\Services;

use App\Models\Almacen;
use App\Models\AlmacenMovimiento;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\DeliveryCompany;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\Reservacion;
use App\Models\SesionCaja;
use App\Models\SystemSetting;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class RestaurantOrderService
{
    private function restauranteValidaStock(): bool
    {
        $user = Auth::user();
        if (!$user || !$user->businessInstance) {
            return true;
        }
        $config = $user->businessInstance->configuracion ?? [];
        return ($config['restaurante_valida_stock'] ?? '1') === '1';
    }
    public function getIndexData(): array
    {
        $this->autoOcuparMesasReservadas();

        $mesas = Mesa::deSucursal()->with(['ordenActiva', 'reservacion', 'ubicacion'])->orderBy('numero')->get();
        $mesasAgrupadas = $mesas->groupBy(fn($m) => $m->ubicacion?->nombre ?? '__sin_ubicacion__');
        $cajas = Caja::where('activo', true)->orderBy('nombre')->get();
        $sesionActiva = SesionCaja::with('caja')
            ->where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();

        $servicioPorcentaje = (float) SystemSetting::get('servicio_porcentaje', 0);
        $servicioMinPersonas = (int) SystemSetting::get('servicio_min_personas', 8);
        $restauranteValidaStock = $this->restauranteValidaStock();

        return compact('mesas', 'mesasAgrupadas', 'cajas', 'sesionActiva', 'servicioPorcentaje', 'servicioMinPersonas', 'restauranteValidaStock');
    }

    public function autoOcuparMesasReservadas(): void
    {
        $reservaciones = Reservacion::deSucursal()
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->where('fecha_hora', '<=', now())
            ->whereHas('mesa', fn($q) => $q->where('estado', 'reservada')->whereDoesntHave('ordenActiva'))
            ->get();

        foreach ($reservaciones as $reservacion) {
            DB::beginTransaction();
            try {
                $mesa = $reservacion->mesa;
                $cliente = $reservacion->cliente;
                if (!$cliente && $reservacion->cliente_telefono) {
                    $cliente = Cliente::firstWhere('telefono', $reservacion->cliente_telefono);
                }
                if (!$cliente) {
                    $cliente = Cliente::create([
                        'nombre'   => $reservacion->cliente_nombre ?: 'Cliente ' . $reservacion->cliente_telefono,
                        'telefono' => $reservacion->cliente_telefono,
                        'email'    => $reservacion->cliente_email,
                    ]);
                }

                $sesion = SesionCaja::where('user_id', Auth::id())
                    ->where('estado', 'abierta')
                    ->latest('fecha_apertura')
                    ->first();

                Venta::create([
                    'user_id'        => Auth::id(),
                    'sucursal_id'    => session('sucursal_id'),
                    'mesa_id'        => $mesa->id,
                    'caja_id'        => $sesion?->caja_id,
                    'sesion_caja_id' => $sesion?->id,
                    'cliente_id'     => $cliente->id,
                    'tipo_venta_id'  => \App\Models\TipoVenta::RESTAURANTE,
                    'fecha'          => now(),
                    'subtotal'       => 0,
                    'impuestos'      => 0,
                    'total'          => 0,
                    'estado'         => 'abierta',
                    'tipo_orden'     => 'mesa',
                    'notas'          => 'Reservación automática - ' . $reservacion->cliente_nombre,
                    'tenant_id'      => Auth::user()->business_instance_id,
                ]);

                $mesa->update(['estado' => 'ocupada']);
                $reservacion->update(['estado' => 'cumplida']);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error auto-ocupando mesa reservada: ' . $e->getMessage());
            }
        }
    }

    public function abrirMesa(Mesa $mesa, ?int $clienteId, ?string $tipoOrden, ?int $deliveryCompanyId = null): array
    {
        if ($mesa->estado !== 'disponible' && $mesa->estado !== 'reservada') {
            return ['error' => 'La mesa no está disponible', 'code' => 422];
        }
        if ($mesa->estado === 'reservada') {
            $reservacionPendiente = Reservacion::where('mesa_id', $mesa->id)
                ->whereIn('estado', ['pendiente', 'confirmada'])
                ->latest('fecha_hora')
                ->first();
            if ($reservacionPendiente && $reservacionPendiente->fecha_hora > now()->addHour()) {
                return ['error' => 'La mesa tiene una reservación futura. Confirme o cancele la reservación antes de abrir.', 'code' => 422];
            }
        }

        $sesion = SesionCaja::where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();

        if (!$sesion) {
            return ['error' => 'No tienes una sesión de caja abierta', 'code' => 422];
        }

        $tipoOrden = $tipoOrden ?? 'mesa';

        if ($tipoOrden === 'delivery' && !$deliveryCompanyId) {
            return ['error' => 'Debes seleccionar una empresa de delivery', 'code' => 422];
        }

        DB::beginTransaction();
        try {
            $venta = Venta::create([
                'user_id'             => Auth::id(),
                'sucursal_id'         => session('sucursal_id'),
                'mesa_id'             => $mesa->id,
                'caja_id'             => $sesion->caja_id,
                'sesion_caja_id'      => $sesion->id,
                'cliente_id'          => $clienteId ?? Cliente::consumidorFinal()->id,
                'tipo_venta_id'       => \App\Models\TipoVenta::RESTAURANTE,
                'fecha'               => now(),
                'subtotal'            => 0,
                'impuestos'           => 0,
                'total'               => 0,
                'estado'              => 'abierta',
                'tipo_orden'          => $tipoOrden,
                'delivery_company_id' => $deliveryCompanyId,
                'tenant_id'           => Auth::user()->business_instance_id,
            ]);

            $mesa->update(['estado' => 'ocupada']);
            DB::commit();

            return ['orden' => $venta->load('detalles.producto')];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'code' => 500];
        }
    }
    public function agregarItem(Mesa $mesa, int $productoId, int $cantidad, ?string $notas, ?string $curso): array
    {
        $orden = $mesa->ordenActiva;
        if (!$orden) {
            return ['error' => 'La mesa no tiene una orden abierta', 'code' => 422];
        }
    
        $sucursalId = session('sucursal_id');
        $almacen = Almacen::where('sucursal_id', $sucursalId)->first();
        $almacenId = $almacen?->id ?? 1;
    
        $producto = Producto::findOrFail($productoId);
        $notas = $notas;
        $curso = $curso ?? 'fuerte';
    
        $detalleExistente = \App\Models\VentaDetalle::where('venta_id', $orden->id)
            ->where('producto_id', $producto->id)
            ->where('notas', $notas)
            ->where('curso', $curso)
            ->first();
    
        DB::beginTransaction();
        try {
            // Validar stock del solo si la config lo requiere
            if ($this->restauranteValidaStock() && $producto->stock < $cantidad) {
                DB::rollBack();
                return ['error' => "Stock insuficiente del producto. Disponible: {$producto->stock}", 'code' => 422];
            }
            
            // Validar stock de ingredientes solo si la config lo requiere
            if ($this->restauranteValidaStock() && $errorIngrediente = $this->checkIngredientsStock($producto, $cantidad)) {
                DB::rollBack();
                return ['error' => $errorIngrediente, 'code' => 422];
            }
    
            if ($detalleExistente) {
                $nuevaCantidad = $detalleExistente->cantidad + $cantidad;
                $detalleExistente->cantidad = $nuevaCantidad;
                $detalleExistente->subtotal = $producto->precio * $nuevaCantidad;
                $detalleExistente->save();
    
                $itbisItem = ($producto->itbis_porcentaje ?? 0) / 100 * $producto->precio * $cantidad;
                $orden->increment('subtotal', $producto->precio * $cantidad);
                $orden->increment('impuestos', $itbisItem);
                $orden->increment('total', ($producto->precio * $cantidad) + $itbisItem);
    
                $detalle = $detalleExistente->fresh();
            } else {
                $detalle = \App\Models\VentaDetalle::create([
                    'venta_id'        => $orden->id,
                    'producto_id'     => $producto->id,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $producto->precio,
                    'subtotal'        => $producto->precio * $cantidad,
                    'almacen_id'      => $almacenId,
                    'notas'           => $notas,
                    'curso'           => $curso,
                    'tenant_id'       => Auth::user()->business_instance_id,
                ]);
    
                $itbisItem = ($producto->itbis_porcentaje ?? 0) / 100 * $producto->precio * $cantidad;
                $orden->increment('subtotal', $producto->precio * $cantidad);
                $orden->increment('impuestos', $itbisItem);
                $orden->increment('total', ($producto->precio * $cantidad) + $itbisItem);
            }
    
            $producto->decrement('stock', $cantidad);
            
            foreach ($producto->ingredientes as $ingrediente) {
                $cantidadADeducir = $ingrediente->pivot->cantidad * $cantidad;
                $ingrediente->decrement('stock', $cantidadADeducir);
            }
    
            AlmacenMovimiento::create([
                'tenant_id'   => Auth::user()->business_instance_id,
                'producto_id' => $producto->id,
                'almacen_id'  => $almacenId,
                'tipo'        => 'salida',
                'cantidad'    => $cantidad,
                'nota'        => 'Pedido restaurante - Mesa #' . $mesa->numero,
                'user_id'     => Auth::id(),
            ]);
    
            DB::commit();
    
            return [
                'orden'   => $orden->fresh()->load('detalles.producto'),
                'detalle' => $detalle->load('producto'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'code' => 500];
        }
    }

    public function quitarItem(Mesa $mesa, VentaDetalle $detalle): array
    {
        $orden = $mesa->ordenActiva;
        if (!$orden || $detalle->venta_id !== $orden->id) {
            return ['error' => 'El detalle no pertenece a esta orden', 'code' => 422];
        }

        DB::beginTransaction();
        try {
            $subtotal = $detalle->subtotal;
            $itbisItem = ($detalle->producto->itbis_porcentaje ?? 0) / 100 * $subtotal;

            $orden->decrement('subtotal', $subtotal);
            $orden->decrement('impuestos', $itbisItem);
            $orden->decrement('total', $subtotal + $itbisItem);

            if ($detalle->producto) {
                $detalle->producto->increment('stock', $detalle->cantidad);
                
                foreach ($detalle->producto->ingredientes as $ingrediente) {
                    $cantidadAReducir = $ingrediente->pivot->cantidad * $detalle->cantidad;
                    $ingrediente->increment('stock', $cantidadAReducir);
                }
            }


            $detalle->delete();
            DB::commit();

            return ['orden' => $orden->fresh()->load('detalles.producto')];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'code' => 500];
        }
    }

    public function actualizarItem(Mesa $mesa, VentaDetalle $detalle, int $nuevaCantidad): array
    {
        $orden = $mesa->ordenActiva;
        if (!$orden || $detalle->venta_id !== $orden->id) {
            return ['error' => 'El detalle no pertenece a esta orden', 'code' => 422];
        }

        $precioUnitario = (float) $detalle->precio_unitario;
        $cantidadActual = $detalle->cantidad;
        $diferencia = $nuevaCantidad - $cantidadActual;

        $producto = $detalle->producto;
        if ($producto && $diferencia > 0 && $this->restauranteValidaStock() && $producto->stock < $diferencia) {
            return ['error' => "Stock insuficiente. Disponible: {$producto->stock}", 'code' => 422];
        }

        DB::beginTransaction();
        try {
            $nuevoSubtotal = $precioUnitario * $nuevaCantidad;
            $itbisPorcentaje = ($producto->itbis_porcentaje ?? 0) / 100;
            $itbisAnterior = $detalle->subtotal * $itbisPorcentaje;
            $itbisNuevo = $nuevoSubtotal * $itbisPorcentaje;

            $orden->increment('subtotal', $nuevoSubtotal - $detalle->subtotal);
            $orden->increment('impuestos', $itbisNuevo - $itbisAnterior);
            $orden->increment('total', ($nuevoSubtotal - $detalle->subtotal) + ($itbisNuevo - $itbisAnterior));

            $detalle->cantidad = $nuevaCantidad;
            $detalle->subtotal = $nuevoSubtotal;
            $detalle->save();

            if ($producto) {
                $producto->decrement('stock', $diferencia);
                
                foreach ($producto->ingredientes as $ingrediente) {
                    $cantidadADeducir = $ingrediente->pivot->cantidad * $diferencia;
                    $ingrediente->decrement('stock', $cantidadADeducir);
                }
            }


            DB::commit();

            return ['orden' => $orden->fresh()->load('detalles.producto')];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'code' => 500];
        }
    }

    public function cobrar(Mesa $mesa, array $data): array
    {
        $orden = $mesa->ordenActiva;
        if (!$orden) {
            return ['error' => 'La mesa no tiene una orden abierta', 'code' => 422];
        }

        $sesion = SesionCaja::where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();

        if (!$sesion) {
            return ['error' => 'No tienes una sesión de caja abierta', 'code' => 422];
        }

        $propina = (float)($data['propina'] ?? 0);
        $servicioPorcentaje = (float) SystemSetting::get('servicio_porcentaje', 0);
        $cargoServicio = (bool)($data['cargo_servicio'] ?? false) ? round($orden->total * $servicioPorcentaje / 100, 2) : 0;
        $totalConPropina = $orden->total + $propina + $cargoServicio;
        $metodo = $data['metodo_pago'];
        $isSplit = (bool)($data['split'] ?? false);

        if ($metodo === 'mixto' && !$isSplit) {
            $sumaPagos = (float)($data['monto_recibido'] ?? 0)
                       + (float)($data['monto_tarjeta'] ?? 0)
                       + (float)($data['monto_transferencia'] ?? 0);
            if ($sumaPagos < $totalConPropina) {
                return ['error' => "La suma de los pagos no cubre el total de RD$ " . number_format($totalConPropina, 2), 'code' => 422];
            }
        }

        DB::beginTransaction();
        try {
            $splitNotas = '';
            if ($isSplit) {
                $totales = $data['totales'] ?? [];
                $splitNotas = 'Cuenta dividida en ' . ($data['personas'] ?? 2) . ' personas';
            }

            $updateData = [
                'estado'  => 'completada',
                'fecha'   => now(),
                'propina' => $propina,
                'cargo_servicio' => $cargoServicio,
                'total'   => $totalConPropina,
                'notas'   => $splitNotas ? ($orden->notas ? $orden->notas . ' | ' . $splitNotas : $splitNotas) : $orden->notas,
            ];

            if ($orden->delivery_company_id) {
                $company = DeliveryCompany::find($orden->delivery_company_id);
                if ($company) {
                    $deliveryFee = round($orden->total * ($company->comision_porcentaje / 100), 2);
                    $updateData['delivery_fee'] = $deliveryFee;
                }
            }

            $orden->update($updateData);

            $mesa->update(['estado' => 'disponible']);

            if ($metodo === 'mixto') {
                foreach ([
                    ['metodo' => 'efectivo', 'monto' => $data['monto_recibido'] ?? 0],
                    ['metodo' => 'tarjeta', 'monto' => $data['monto_tarjeta'] ?? 0],
                    ['metodo' => 'transferencia', 'monto' => $data['monto_transferencia'] ?? 0],
                ] as $pago) {
                    if ($pago['monto'] > 0) {
                        Pago::create([
                            'tenant_id'      => Auth::user()->business_instance_id,
                            'venta_id'       => $orden->id,
                            'caja_id'        => $sesion->caja_id,
                            'sesion_caja_id' => $sesion->id,
                            'monto'          => $pago['monto'],
                            'metodo_pago'    => $pago['metodo'],
                            'nota'           => 'Pago restaurante - Mesa #' . $mesa->numero,
                            'fecha_pago'     => now(),
                        ]);
                    }
                }
            } else {
                Pago::create([
                    'tenant_id'      => Auth::user()->business_instance_id,
                    'venta_id'       => $orden->id,
                    'caja_id'        => $sesion->caja_id,
                    'sesion_caja_id' => $sesion->id,
                    'monto'          => $orden->total,
                    'metodo_pago'    => $metodo,
                    'nota'           => 'Pago restaurante - Mesa #' . $mesa->numero,
                    'fecha_pago'     => now(),
                ]);
            }

            if ($isSplit && !empty($data['split_persons'])) {
                foreach ($data['split_persons'] as $person) {
                    \App\Models\SplitBillPerson::create([
                        'venta_id'       => $orden->id,
                        'persona_num'    => $person['num'],
                        'persona_nombre' => $person['nombre'] ?? ('Persona ' . $person['num']),
                        'items'          => $person['items'] ?? [],
                        'subtotal'       => $person['subtotal'] ?? 0,
                        'tenant_id'      => Auth::user()->business_instance_id,
                    ]);
                }
            }

            DB::commit();

            $orden->load('detalles.producto', 'cliente');

            return [
                'success' => true,
                'venta'   => [
                    'id'          => $orden->id,
                    'total'       => $orden->total,
                    'mesa_numero' => $mesa->numero,
                    'mesa_nombre' => $mesa->nombre ?? 'Mesa ' . $mesa->numero,
                    'metodo_pago' => $metodo,
                    'cliente'     => $orden->cliente?->nombre ?? 'Consumidor Final',
                ],
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'code' => 500];
        }
    }

    public function anularOrden(Mesa $mesa, string $motivo): array
    {
        $orden = $mesa->ordenActiva;
        if (!$orden) {
            return ['error' => 'No hay orden activa', 'code' => 422];
        }

        $user = Auth::user();
        if (!$user->hasRole('admin') && $orden->total > 500) {
            return ['error' => 'Se requiere autorización de administrador para anular órdenes mayores a RD$500', 'code' => 422];
        }

        DB::beginTransaction();
        try {
            $itemsDevueltos = 0;
            $productosDevueltos = [];

            foreach ($orden->detalles as $detalle) {
                if ($detalle->producto) {
                    $detalle->producto->increment('stock', $detalle->cantidad);
                    $productosDevueltos[] = [
                        'nombre'        => $detalle->producto->nombre,
                        'cantidad'      => $detalle->cantidad,
                        'precio'        => $detalle->precio_unitario,
                    ];
                }

                foreach ($detalle->producto?->ingredientes ?? [] as $ingrediente) {
                    $ingrediente->increment('stock', $ingrediente->pivot->cantidad * $detalle->cantidad);
                }

                AlmacenMovimiento::create([
                    'tenant_id'   => Auth::user()->business_instance_id,
                    'producto_id' => $detalle->producto_id,
                    'almacen_id'  => $detalle->almacen_id,
                    'tipo'        => 'entrada',
                    'cantidad'    => $detalle->cantidad,
                    'nota'        => 'Anulación orden Mesa #' . $mesa->numero . ': ' . $motivo,
                    'user_id'     => Auth::id(),
                ]);
                $itemsDevueltos++;
            }

            $orden->update(['estado' => 'anulada', 'notas' => $motivo, 'total' => 0, 'subtotal' => 0, 'impuestos' => 0]);
            $mesa->update(['estado' => 'disponible']);
            DB::commit();

            return [
                'success' => true,
                'orden_id' => $orden->id,
                'mesa' => "Mesa #{$mesa->numero}",
                'motivo' => $motivo,
                'items_devueltos' => $itemsDevueltos,
                'productos' => $productosDevueltos,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => 'Error al anular: ' . $e->getMessage(), 'code' => 500];
        }
    }

    public function aplicarDescuento(Mesa $mesa, string $tipo, float $valor, string $motivo): array
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin');

        $orden = $mesa->ordenActiva;
        if (!$orden) {
            return ['error' => 'No hay orden activa', 'code' => 422];
        }

        if ($tipo === 'porcentaje') {
            if ($valor > 50 && !$isAdmin) {
                return ['error' => 'Descuento mayor a 50% requiere autorización de administrador', 'code' => 422];
            }
            $descuento = $orden->subtotal * ($valor / 100);
        } else {
            if ($valor > $orden->subtotal) {
                return ['error' => 'El descuento no puede exceder el subtotal', 'code' => 422];
            }
            $maxAuto = $orden->subtotal * 0.3;
            if ($valor > $maxAuto && !$isAdmin) {
                return ['error' => 'Descuento mayor a 30% requiere autorización de administrador', 'code' => 422];
            }
            $descuento = $valor;
        }

        $descuento = round($descuento, 2);
        $nuevoSubtotal = round($orden->subtotal - $descuento, 2);
        $nuevoTotal = round($nuevoSubtotal + $orden->impuestos, 2);

        $orden->update([
            'subtotal'         => $nuevoSubtotal,
            'total'            => $nuevoTotal,
            'descuento'        => $descuento,
            'descuento_tipo'   => $tipo,
            'descuento_motivo' => $motivo,
        ]);

        $orden->load('detalles.producto', 'cliente', 'usuario');
        return ['success' => true, 'orden' => $orden];
    }

    public function trasladarMesa(Mesa $origen, Mesa $destino): array
    {
        $orden = $origen->ordenActiva;
        if (!$orden) {
            return ['error' => 'La mesa origen no tiene orden activa', 'code' => 422];
        }
        if ($destino->ordenActiva) {
            return ['error' => 'La mesa destino ya tiene una orden activa', 'code' => 422];
        }

        $orden->update(['mesa_id' => $destino->id]);
        $origen->update(['estado' => 'disponible']);
        $destino->update(['estado' => 'ocupada']);

        return ['success' => true, 'orden' => $orden->fresh(['detalles.producto', 'cliente', 'usuario'])];
    }

    public function abrirCaja(int $cajaId, float $montoInicial): array
    {
        $caja = Caja::where('sucursal_id', session('sucursal_id'))->findOrFail($cajaId);

        if (!$caja->activo) return ['error' => 'Esta caja está inactiva', 'code' => 422];
        if ($caja->estado === 'abierta') return ['error' => 'La caja ya está abierta', 'code' => 422];

        $sesionActiva = SesionCaja::where('user_id', Auth::id())->where('estado', 'abierta')->first();
        if ($sesionActiva) {
            return ['error' => 'Ya tienes una sesión abierta en ' . $sesionActiva->caja->nombre, 'code' => 422];
        }

        DB::beginTransaction();
        try {
            $sesion = SesionCaja::create([
                'tenant_id'      => Auth::user()->business_instance_id,
                'caja_id'        => $caja->id,
                'user_id'        => Auth::id(),
                'fecha_apertura' => now(),
                'monto_inicial'  => $montoInicial,
                'estado'         => 'abierta',
            ]);
            $caja->update(['estado' => 'abierta']);
            DB::commit();

            return ['success' => true, 'sesion' => $sesion->load('caja')];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'code' => 500];
        }
    }

    public function crearCaja(string $nombre, ?string $codigo, ?string $ubicacion): Caja
    {
        return Caja::create([
            'tenant_id'    => Auth::user()->business_instance_id,
            'nombre'       => $nombre,
            'codigo'       => $codigo,
            'ubicacion'    => $ubicacion,
            'sucursal_id'  => session('sucursal_id'),
            'activo'       => true,
            'estado'       => 'cerrada',
        ]);
    }

    public function cerrarCaja(int $cajaId, float $montoDeclarado, array $cobros, ?string $notas): array
    {
        $sesion = SesionCaja::where('caja_id', $cajaId)
            ->where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->first();

        if (!$sesion) {
            return ['error' => 'No tienes una sesión abierta en esta caja', 'code' => 422];
        }

        $cobrosEfectivo = (float)($cobros['efectivo'] ?? 0);
        $cobrosTarjeta = (float)($cobros['tarjeta'] ?? 0);
        $cobrosTransferencia = (float)($cobros['transferencia'] ?? 0);
        $totalEsperado = $sesion->monto_inicial + $cobrosEfectivo;
        $descuadre = $montoDeclarado - $totalEsperado;

        DB::beginTransaction();
        try {
            $sesion->update([
                'fecha_cierre'         => now(),
                'ventas_efectivo'      => $cobrosEfectivo,
                'ventas_tarjeta'       => $cobrosTarjeta,
                'ventas_transferencia' => $cobrosTransferencia,
                'monto_declarado'      => $montoDeclarado,
                'descuadre'            => $descuadre,
                'estado'               => 'cerrada',
                'notas'                => $notas,
            ]);
            $sesion->caja->update(['estado' => 'cerrada']);
            DB::commit();

            return [
                'success'   => true,
                'descuadre' => $descuadre,
                'message'   => 'Caja cerrada. Descuadre: RD$ ' . number_format($descuadre, 2),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'code' => 500];
        }
    }

    public function resumenCierre(int $cajaId): array
    {
        $sesion = SesionCaja::with('caja', 'ventas.pagos')
            ->where('caja_id', $cajaId)
            ->where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->first();

        if (!$sesion) {
            return ['error' => 'No tienes una sesión abierta en esta caja', 'code' => 422];
        }

        $ventas = $sesion->ventas()->where('estado', 'completada')->get();
        $totalEfectivo = 0;
        $totalTarjeta = 0;
        $totalTransferencia = 0;

        foreach ($ventas as $v) {
            foreach ($v->pagos as $p) {
                switch ($p->metodo_pago) {
                    case 'efectivo': $totalEfectivo += $p->monto; break;
                    case 'tarjeta': $totalTarjeta += $p->monto; break;
                    case 'transferencia': $totalTransferencia += $p->monto; break;
                }
            }
        }

        return [
            'success'    => true,
            'sesion'     => $sesion,
            'total_ventas' => $ventas->count(),
            'efectivo'   => $totalEfectivo,
            'tarjeta'    => $totalTarjeta,
            'transferencia' => $totalTransferencia,
            'total'      => $totalEfectivo + $totalTarjeta + $totalTransferencia,
        ];
    }

    private function checkIngredientsStock(Producto $producto, int $cantidad): ?string
    {
        foreach ($producto->ingredientes as $ingrediente) {
            $cantidadRequerida = $ingrediente->pivot->cantidad * $cantidad;
            if ($ingrediente->stock < $cantidadRequerida) {
                return "Stock insuficiente del ingrediente '{$ingrediente->nombre}'. Disponible: {$ingrediente->stock}, Requerido: {$cantidadRequerida}";
            }
        }
        return null;
    }
}
