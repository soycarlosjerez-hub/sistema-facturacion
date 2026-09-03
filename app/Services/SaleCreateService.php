<?php

namespace App\Services;

use App\Models\ArteObra;
use App\Models\Cliente;
use App\Models\Equipo;
use App\Models\NcfSequence;
use App\Models\Producto;
use App\Models\Caja;
use App\Models\CategoriaSub;
use App\Models\SesionCaja;
use App\Models\Pago;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Orden;
use App\Models\OrdenDetalle;
use App\Services\Ecf\EcfService;
use App\Services\NcfService;
use App\Models\SystemSetting;
use App\Services\SaleCalcService;
use App\Services\SaleStockService;
use App\Services\RetentionService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

/**
 * Servicio especializado en la creación de ventas.
 * 
 * Responsabilidad única: construir las líneas de la venta según el modo de negocio
 * (productos, obras de arte, equipos, lavados, mixtos), calcular totales,
 * y persistir el registro de venta junto con sus detalles y pagos.
 * 
 * Se encarga de la lógica de negocio específica del tipo de negocio (business_type)
 * para determinar cómo se construyen las líneas de venta.
 */
class SaleCreateService
{
    protected NcfService $ncfService;
    protected EcfService $ecfService;
    protected RetentionService $retentionService;
    protected SaleCalcService $calc;
    protected SaleStockService $stock;

    public function __construct(
        NcfService $ncfService,
        EcfService $ecfService,
        RetentionService $retentionService,
        SaleCalcService $calc,
        SaleStockService $stock
    ) {
        $this->ncfService = $ncfService;
        $this->ecfService = $ecfService;
        $this->retentionService = $retentionService;
        $this->calc = $calc;
        $this->stock = $stock;
    }

    public function createSale(array $data, SesionCaja $sesion): Venta
    {
        $sesion->loadMissing('caja');
        
        $tipoComprobante = $data['tipo_comprobante'] ?? 'sin';

        if (!$sesion->caja->esTipoComprobantePermitido($tipoComprobante)) {
            throw new \Exception("El tipo de comprobante '{$tipoComprobante}' no está permitido en este terminal.");
        }

        if (empty($data['cliente_id'])) {
            $consumidorFinal = Cliente::consumidorFinal(Auth::user()->business_instance_id);
            $data['cliente_id'] = $consumidorFinal->id;
        }

        $metodo = $data['metodo_pago'] ?? 'efectivo';
        $estado = match ($metodo) {
            'fiado' => 'pendiente',
            'cuenta_abierta' => 'cuenta_abierta',
            default => 'completada',
        };

        // --- Cálculo autoritativo server-side (F0.2/F0.3) ---
        $rolesAutorizados = ['admin', 'admin-business', 'root', 'gerente'];
        $puedeSobreescribirPrecio = auth()->user()->hasAnyRole($rolesAutorizados);

        $modoObras    = $this->facturaObrasArte();
        $modoEquipos  = $this->facturaEquipos();
        $modoLavados  = $this->facturaLavados();
        $modoProductosYServicios  = $this->facturaProductosYServicios();
        $modoProductosYEquipos    = $this->facturaProductosYEquipos();

        $productoIds = $data['producto_id'] ?? [];
        $obraIds     = $data['obra_id'] ?? [];
        $equipoIds   = $data['equipo_id'] ?? [];
        $cantidades  = $data['cantidad'] ?? [];
        $preciosCli  = $data['precio'] ?? [];
        $descuentoCli = $data['descuento'] ?? [];
        $tiposCli     = $data['descuento_tipo'] ?? [];
        $sinItbisCli  = $data['sin_itbis'] ?? [];

        // --- Validaciones para ventas con líneas sin ITBIS ---
        $tieneSinItbis = is_array($sinItbisCli) && in_array(true, array_map(fn ($v) => (bool) $v, $sinItbisCli), true);

        if ($tieneSinItbis) {
            $tipoComprobante = $data['tipo_comprobante'] ?? 'ncf';
            if ($tipoComprobante !== 'sin') {
                throw new \Exception('No se permite quitar el ITBIS en comprobantes fiscales (NCF/e-CF).');
            }
            $this->verificarTokenAdmin($data['admin_token'] ?? null);
        }

        $lineas = [];
        if ($modoObras) {
            foreach ($obraIds as $i => $obraId) {
                if (!$obraId) continue;
                $obra = ArteObra::find($obraId);
                if (!$obra) {
                    throw new \Exception('La obra #' . $obraId . ' no existe.');
                }
                $cantidad   = 1;
                $precioBD   = (float) $obra->precio_venta;
                $lineas[] = [
                    'id'       => $obraId,
                    'es_obra'  => true,
                    'nombre'   => $obra->titulo,
                    'cantidad' => $cantidad,
                    'precio'   => $precioBD,
                    'subtotal' => round($precioBD * $cantidad, 2),
                    'desc'     => 0,
                    'tipo'     => 'monto',
                    'itbis_p'  => (float) ($this->itbisPorcentajeInstancia()),
                    'sin_itbis' => (bool) ($sinItbisCli[$i] ?? false),
                ];
            }
        } elseif ($modoEquipos) {
            foreach ($equipoIds as $i => $equipoId) {
                if (!$equipoId) continue;
                $equipo = Equipo::find($equipoId);
                if (!$equipo) {
                    throw new \Exception('El equipo #' . $equipoId . ' no existe.');
                }
                if ($equipo->estado !== 'disponible') {
                    throw new \Exception('El equipo ' . $equipo->serial_imei . ' no est\u00e1 disponible para venta.');
                }
                $precioBD   = (float) $equipo->precio_venta;
                $precioCli  = (float) ($preciosCli[$i] ?? $precioBD);
                if (abs($precioCli - $precioBD) > 0.02 && !$puedeSobreescribirPrecio) {
                    throw new \Exception("No autorizado para modificar el precio del equipo " . $equipo->serial_imei . ".");
                }
                $precioBase = ($precioCli !== $precioBD && $puedeSobreescribirPrecio) ? $precioCli : $precioBD;
                $lineas[] = [
                    'id'        => $equipoId,
                    'es_equipo' => true,
                    'es_obra'   => false,
                    'nombre'    => $equipo->marca . ' ' . $equipo->modelo . ' (' . $equipo->serial_imei . ')',
                    'cantidad'  => 1,
                    'precio'    => $precioBase,
                    'subtotal'  => round($precioBase * 1, 2),
                    'desc'      => (float) ($descuentoCli[$i] ?? 0),
                    'tipo'      => $tiposCli[$i] ?? 'monto',
                    'itbis_p'   => (float) ($this->itbisPorcentajeInstancia()),
                    'sin_itbis' => (bool) ($sinItbisCli[$i] ?? false),
                    'serial_imei' => $equipo->serial_imei,
                    'marca'     => $equipo->marca,
                    'modelo'    => $equipo->modelo,
                    'color'     => $equipo->color,
                    'almacenamiento_gb' => $equipo->almacenamiento_gb,
                    'tipo_dispositivo' => $equipo->tipo_dispositivo,
                    'garantia_desde' => $equipo->garantia_desde,
                    'garantia_hasta' => $equipo->garantia_hasta,
                    'garantia_tipo' => $equipo->garantia_tipo,
                ];
            }
        } elseif ($modoLavados) {
            // MODO LAVADOS: servicios de lavado de carros
            foreach ($productoIds as $i => $productoId) {
                if (!$productoId) continue;
                // Para lavados, buscamos productos que tengan categoría de lavado
                // o usamos un campo especial. Por ahora, usamos la misma lógica de productos
                // pero identificados como servicios de lavado.
                $producto = Producto::find($productoId);
                if (!$producto) {
                    throw new \Exception('El producto #' . $productoId . ' no existe.');
                }
                $cantidad   = max(1, (int) ($cantidades[$i] ?? 1));
                $precioBD   = (float) $producto->precio;
                $precioCli  = (float) ($preciosCli[$i] ?? $precioBD);
                if (abs($precioCli - $precioBD) > 0.02 && !$puedeSobreescribirPrecio) {
                    throw new \Exception("No autorizado para modificar el precio de \"{$producto->nombre}\".");
                }
                $precioBase = ($precioCli !== $precioBD && $puedeSobreescribirPrecio) ? $precioCli : $precioBD;
                // Agregar marca de que es un servicio de lavado
                $lineas[] = [
                    'id'       => $productoId,
                    'es_obra'  => false,
                    'nombre'   => 'Lavado: ' . $producto->nombre,
                    'cantidad' => $cantidad,
                    'precio'   => $precioBase,
                    'subtotal' => round($precioBase * $cantidad, 2),
                    'desc'     => (float) ($descuentoCli[$i] ?? 0),
                    'tipo'     => $tiposCli[$i] ?? 'monto',
                    'itbis_p'  => (float) ($producto->itbis_porcentaje ?? 0),
                    'sin_itbis' => (bool) ($sinItbisCli[$i] ?? false),
                    'es_lavado' => true,  // Marcador para identificar en la vista/impresión
                ];
            }
        } elseif ($modoProductosYServicios) {
            // MODO MIXTO: productos + servicios de lavado
            // Procesar productos normales
            foreach ($productoIds as $i => $productoId) {
                if (!$productoId) continue;
                $producto = Producto::find($productoId);
                if (!$producto) {
                    throw new \Exception('El producto #' . $productoId . ' no existe.');
                }
                $cantidad   = max(1, (int) ($cantidades[$i] ?? 1));
                $precioBD   = (float) $producto->precio;
                $precioCli  = (float) ($preciosCli[$i] ?? $precioBD);
                if (abs($precioCli - $precioBD) > 0.02 && !$puedeSobreescribirPrecio) {
                    throw new \Exception("No autorizado para modificar el precio de \"{$producto->nombre}\".");
                }
                $precioBase = ($precioCli !== $precioBD && $puedeSobreescribirPrecio) ? $precioCli : $precioBD;
                $lineas[] = [
                    'id'       => $productoId,
                    'es_obra'  => false,
                    'nombre'   => $producto->nombre,
                    'cantidad' => $cantidad,
                    'precio'   => $precioBase,
                    'subtotal' => round($precioBase * $cantidad, 2),
                    'desc'     => (float) ($descuentoCli[$i] ?? 0),
                    'tipo'     => $tiposCli[$i] ?? 'monto',
                    'itbis_p'  => (float) ($producto->itbis_porcentaje ?? 0),
                    'sin_itbis' => (bool) ($sinItbisCli[$i] ?? false),
                ];
            }

            // Procesar servicios de lavado
            $servicioIds = $data['servicio_id'] ?? [];
            $cantidadesServ = $data['cantidad_servicio'] ?? [];
            $preciosServ = $data['precio_servicio'] ?? [];
            $descuentosServ = $data['descuento_servicio'] ?? [];
            $tiposServ = $data['descuento_tipo_servicio'] ?? [];
            $sinItbisServ = $data['sin_itbis_servicio'] ?? [];

            foreach ($servicioIds as $i => $servicioId) {
                $cantidad   = max(1, (int) ($cantidadesServ[$i] ?? 1));
                $precioBD   = (float) $servicio->precio;
                $precioCli  = (float) ($preciosServ[$i] ?? $precioBD);
                if (abs($precioCli - $precioBD) > 0.02 && !$puedeSobreescribirPrecio) {
                    throw new \Exception("No autorizado para modificar el precio del servicio \"{$servicio->nombre}\".");
                }
                $precioBase = ($precioCli !== $precioBD && $puedeSobreescribirPrecio) ? $precioCli : $precioBD;
                // Usar ITBIS del servicio o del sistema
                $itbisServicio = (float) ($servicio->itbis_porcentaje ?? $this->itbisPorcentajeInstancia());
                $lineas[] = [
                    'id'       => $servicioId,
                    'es_obra'  => false,
                    'nombre'   => 'Servicio: ' . $servicio->nombre,
                    'cantidad' => $cantidad,
                    'precio'   => $precioBase,
                    'subtotal' => round($precioBase * $cantidad, 2),
                    'desc'     => (float) ($descuentosServ[$i] ?? 0),
                    'tipo'     => $tiposServ[$i] ?? 'monto',
                    'itbis_p'  => $itbisServicio,
                    'sin_itbis' => (bool) ($sinItbisServ[$i] ?? false),
                    'es_lavado' => true,
                    'es_servicio' => true,
                ];
            }
        } elseif ($modoProductosYEquipos) {
            // MODO MIXTO: productos + equipos (IMEI/Serial)
            // Procesar productos normales
            foreach ($productoIds as $i => $productoId) {
                if (!$productoId) continue;
                $producto = Producto::find($productoId);
                if (!$producto) {
                    throw new \Exception('El producto #' . $productoId . ' no existe.');
                }
                $cantidad   = max(1, (int) ($cantidades[$i] ?? 1));
                $precioBD   = (float) $producto->precio;
                $precioCli  = (float) ($preciosCli[$i] ?? $precioBD);
                if (abs($precioCli - $precioBD) > 0.02 && !$puedeSobreescribirPrecio) {
                    throw new \Exception("No autorizado para modificar el precio de \"{$producto->nombre}\".");
                }
                $precioBase = ($precioCli !== $precioBD && $puedeSobreescribirPrecio) ? $precioCli : $precioBD;
                $lineas[] = [
                    'id'       => $productoId,
                    'es_obra'  => false,
                    'nombre'   => $producto->nombre,
                    'cantidad' => $cantidad,
                    'precio'   => $precioBase,
                    'subtotal' => round($precioBase * $cantidad, 2),
                    'desc'     => (float) ($descuentoCli[$i] ?? 0),
                    'tipo'     => $tiposCli[$i] ?? 'monto',
                    'itbis_p'  => (float) ($producto->itbis_porcentaje ?? 0),
                    'sin_itbis' => (bool) ($sinItbisCli[$i] ?? false),
                ];
            }

            // Procesar equipos (IMEI/Serial)
            $equipoIdsHibrido = $data['equipo_id'] ?? [];
            $preciosEquipo    = $data['precio_equipo'] ?? [];
            $descuentosEquipo = $data['descuento_equipo'] ?? [];
            $tiposEquipo      = $data['descuento_tipo_equipo'] ?? [];
            $sinItbisEquipo   = $data['sin_itbis_equipo'] ?? [];

            foreach ($equipoIdsHibrido as $i => $equipoId) {
                if (!$equipoId) continue;
                $equipo = Equipo::find($equipoId);
                if (!$equipo) {
                    throw new \Exception('El equipo #' . $equipoId . ' no existe.');
                }
                if ($equipo->estado !== 'disponible') {
                    throw new \Exception('El equipo ' . $equipo->serial_imei . ' no está disponible para venta.');
                }
                $precioBD   = (float) $equipo->precio_venta;
                $precioCliE = (float) ($preciosEquipo[$i] ?? $precioBD);
                if (abs($precioCliE - $precioBD) > 0.02 && !$puedeSobreescribirPrecio) {
                    throw new \Exception("No autorizado para modificar el precio del equipo " . $equipo->serial_imei . ".");
                }
                $precioBase = ($precioCliE !== $precioBD && $puedeSobreescribirPrecio) ? $precioCliE : $precioBD;
                $lineas[] = [
                    'id'        => $equipoId,
                    'es_equipo' => true,
                    'es_obra'   => false,
                    'nombre'    => $equipo->marca . ' ' . $equipo->modelo . ' (' . $equipo->serial_imei . ')',
                    'cantidad'  => 1,
                    'precio'    => $precioBase,
                    'subtotal'  => round($precioBase * 1, 2),
                    'desc'      => (float) ($descuentosEquipo[$i] ?? 0),
                    'tipo'      => $tiposEquipo[$i] ?? 'monto',
                    'itbis_p'   => (float) ($this->itbisPorcentajeInstancia()),
                    'sin_itbis' => (bool) ($sinItbisEquipo[$i] ?? false),
                    'serial_imei' => $equipo->serial_imei,
                    'marca'     => $equipo->marca,
                    'modelo'    => $equipo->modelo,
                    'color'     => $equipo->color,
                    'almacenamiento_gb' => $equipo->almacenamiento_gb,
                    'tipo_dispositivo' => $equipo->tipo_dispositivo,
                    'garantia_desde' => $equipo->garantia_desde,
                    'garantia_hasta' => $equipo->garantia_hasta,
                    'garantia_tipo' => $equipo->garantia_tipo,
                ];
            }
        } else {
            // MODO PRODUCTOS (predeterminado)
            foreach ($productoIds as $i => $productoId) {
                if (!$productoId) continue;
                $producto = Producto::find($productoId);
                if (!$producto) {
                    throw new \Exception('El producto #' . $productoId . ' no existe.');
                }
                $cantidad   = max(1, (int) ($cantidades[$i] ?? 1));
                $precioBD   = (float) $producto->precio;
                $precioCli  = (float) ($preciosCli[$i] ?? $precioBD);
                if (abs($precioCli - $precioBD) > 0.02 && !$puedeSobreescribirPrecio) {
                    throw new \Exception("No autorizado para modificar el precio de \"{$producto->nombre}\".");
                }
                $precioBase = ($precioCli !== $precioBD && $puedeSobreescribirPrecio) ? $precioCli : $precioBD;
                $lineas[] = [
                    'id'       => $productoId,
                    'es_obra'  => false,
                    'nombre'   => $producto->nombre,
                    'cantidad' => $cantidad,
                    'precio'   => $precioBase,
                    'subtotal' => round($precioBase * $cantidad, 2),
                    'desc'     => (float) ($descuentoCli[$i] ?? 0),
                    'tipo'     => $tiposCli[$i] ?? 'monto',
                    'itbis_p'  => (float) ($producto->itbis_porcentaje ?? 0),
                    'sin_itbis' => (bool) ($sinItbisCli[$i] ?? false),
                ];
            }
        }

        $subtotalTotal = (float) array_sum(array_column($lineas, 'subtotal'));
        $generalDescuento = max(0, (float) ($data['general_descuento'] ?? 0));

        $descuentosLinea = 0.0;
        foreach ($lineas as $linea) {
            if ($linea['desc'] <= 0) continue;
            $descuentosLinea += $linea['tipo'] === 'porcentaje'
                ? $linea['subtotal'] * min($linea['desc'], 100) / 100
                : $linea['desc'];
        }

        if ($subtotalTotal > 0) {
            $pctDescuento = (($descuentosLinea + $generalDescuento) / $subtotalTotal) * 100;
            if ($pctDescuento > 50 && !$puedeSobreescribirPrecio) {
                throw new \Exception('Descuentos superiores al 50% requieren autorización de administrador.');
            }
        }

        // Recalcular ITBIS sobre la base bruta autoritativa (descuento aplicado por línea)
        $itbisRecalculado = 0.0;
        foreach ($lineas as $line) {
            $descAplicado = $line['tipo'] === 'porcentaje'
                ? $line['subtotal'] * min($line['desc'], 100) / 100
                : $line['desc'];
            $baseFinal = max(0, $line['subtotal'] - $descAplicado);
            if ($generalDescuento > 0 && $subtotalTotal > 0) {
                $proporcion = $baseFinal / $subtotalTotal;
                $baseFinal = max(0, $baseFinal - ($generalDescuento * $proporcion));
            }
            $tasaItbis = $line['sin_itbis'] ? 0 : $line['itbis_p'];
            $itbisRecalculado += $baseFinal * ($tasaItbis / 100);
        }
        $itbisRecalculado = round($itbisRecalculado, 2);

        // Persistir arrays normalizados para procesarDetalles/procesarPago
        $data['obra_id']          = $modoObras ? array_column($lineas, 'id') : [];
        $data['equipo_id']        = $modoEquipos ? array_column($lineas, 'id') : [];
        $data['producto_id']      = ($modoObras || $modoEquipos) ? [] : array_column($lineas, 'id');
        $data['servicio_id']      = $modoProductosYServicios ? array_filter(array_column($lineas, 'id'), fn($id, $k) => $lineas[$k]['es_servicio'] ?? false, ARRAY_FILTER_USE_BOTH) : [];
        $data['cantidad']         = array_column($lineas, 'cantidad');
        $data['precio']           = array_column($lineas, 'precio');
        $data['subtotal']         = array_column($lineas, 'subtotal');
        $data['descuento']        = array_column($lineas, 'desc');
        $data['descuento_tipo']   = array_column($lineas, 'tipo');
        $data['itbis_porcentaje'] = array_column($lineas, 'itbis_p');
        $data['sin_itbis']        = array_map(fn ($v) => (int) $v, array_column($lineas, 'sin_itbis'));
        $data['subtotal_final']   = $subtotalTotal;
        $descuentosLinea += $generalDescuento;
        $data['total'] = round($subtotalTotal - $descuentosLinea + $itbisRecalculado, 2);

        return DB::transaction(function () use ($data, $sesion, $metodo, $estado, $descuentosLinea, $generalDescuento, $itbisRecalculado) {
            // Verificar que el tipo de comprobante está permitido por la caja de la sesión
            $tipoComprobante = $data['tipo_comprobante'] ?? 'ncf';
            $tiposPermitidos = $sesion->caja->allowed_comprobante_types ?? ['sin', 'ncf', 'ecf'];
            if (!in_array($tipoComprobante, $tiposPermitidos, true)) {
                throw new \Exception("El tipo de comprobante '{$tipoComprobante}' no está permitido en este terminal.");
            }

            $ncf = null;
            $ncfTipo = null;
            $ncfVencimiento = null;
            if (!empty($data['ncf_tipo'])) {
                $ncfTipo = $data['ncf_tipo'];
                $resultadoNcf = $this->ncfService->reservarNcfDentroTransaction($ncfTipo);
                $ncf = $resultadoNcf['ncf'];
                $ncfVencimiento = $resultadoNcf['fecha_vencimiento'];
            }

            $ventaExistente = null;
            if ($metodo === 'cuenta_abierta') {
                $ventaExistente = Venta::where('cliente_id', $data['cliente_id'])
                    ->where('estado', 'cuenta_abierta')
                    ->latest()
                    ->first();
            }

            if ($ventaExistente) {
                $venta = $ventaExistente;
                $venta->increment('subtotal', $data['subtotal_final'] ?? array_sum(array_map('floatval', $data['subtotal'])));
                $venta->increment('impuestos', $itbisRecalculado);
                $venta->increment('total', $data['total']);
                $venta->increment('descuento', $descuentosLinea);
                $venta->update(['fecha' => now()]);

                // NOTA: El balance del cliente se incrementa en procesarPago() para evitar doble incremento
            } else {
                $tipoComprobante = $data['tipo_comprobante'] ?? 'ncf';
                if ($tipoComprobante === 'ncf' && empty($data['ncf_tipo'])) {
                    $tipoComprobante = 'sin';
                }

                $venta = Venta::create([
                    'ncf'              => $ncf,
                    'ncf_tipo'         => $ncfTipo,
                    'ncf_vencimiento'  => $ncfVencimiento,
                    'tipo_comprobante' => $tipoComprobante,
                    'encf'             => null,
                    'user_id'          => Auth::id(),
                    'sucursal_id'      => session('sucursal_id'),
                    'caja_id'          => $sesion->caja_id,
                    'sesion_caja_id'   => $sesion->id,
                    'cliente_id'       => $data['cliente_id'],
                    'tipo_venta_id'    => $data['tipo_venta_id'],
                    'fecha'            => now(),
                    'impuestos'        => $itbisRecalculado,
                    'descuento'        => $descuentosLinea,
                    'general_descuento' => $generalDescuento,
                    'subtotal'         => $data['subtotal_final'] ?? array_sum(array_map('floatval', $data['subtotal'])),
                    'total'            => $data['total'],
                    'estado'           => $estado,
                    'propina'          => $data['propina'] ?? 0,
                    'delivery_fee'     => $data['delivery_fee'] ?? 0,
                    'driver_id'        => $data['driver_id'] ?? null,
                    'delivery_address' => $data['delivery_address'] ?? null,
                    'delivery_company_id' => $data['delivery_company_id'] ?? null,
                    'cargo_servicio'   => $data['cargo_servicio'] ?? 0,
                    'tenant_id'        => Auth::user()->business_instance_id,
                ]);
            }

            $this->procesarDetalles($venta, $data, $ventaExistente);
            $this->procesarPago($venta, $sesion, $metodo, $estado, $data);

            // Crear Orden y DeliveryTracking si es delivery con driver asignado
            $orden = null;
            if ((int) $venta->driver_id > 0) {
                $orden = Orden::create([
                    'ncf'                  => $venta->ncf,
                    'ncf_tipo'             => $venta->ncf_tipo,
                    'ncf_vencimiento'      => $venta->ncf_vencimiento,
                    'tipo_comprobante'     => $venta->tipo_comprobante,
                    'user_id'              => $venta->user_id,
                    'sucursal_id'          => $venta->sucursal_id,
                    'caja_id'              => $venta->caja_id,
                    'sesion_caja_id'       => $venta->sesion_caja_id,
                    'cliente_id'           => $venta->cliente_id,
                    'tipo_orden'           => 'delivery',
                    'driver_id'            => $venta->driver_id,
                    'delivery_company_id'  => $venta->delivery_company_id,
                    'tracking_status'      => 'creado',
                    'direccion_entrega'    => $venta->delivery_address,
                    'telefono_contacto'    => $venta->cliente->telefono ?? ($data['telefono_contacto'] ?? null),
                    'subtotal'             => $venta->subtotal,
                    'impuestos'            => $venta->impuestos,
                    'descuento'            => $venta->descuento,
                    'propina'              => $venta->propina,
                    'cargo_servicio'       => $venta->cargo_servicio,
                    'delivery_fee'         => $venta->delivery_fee,
                    'total'                => $venta->total,
                    'estado'               => 'pendiente',
                    'tenant_id'            => $venta->tenant_id,
                ]);

                // Copiar detalles de venta a orden_detalles
                foreach (VentaDetalle::where('venta_id', $venta->id)->get() as $vDetalle) {
                    OrdenDetalle::create([
                        'orden_id'         => $orden->id,
                        'producto_id'      => $vDetalle->producto_id,
                        'almacen_id'       => $vDetalle->almacen_id,
                        'cantidad'         => $vDetalle->cantidad,
                        'precio_unitario'  => $vDetalle->precio_unitario,
                        'subtotal'         => $vDetalle->subtotal,
                        'notas'            => $vDetalle->notas,
                        'tenant_id'        => $venta->tenant_id,
                    ]);
                }

                // Crear DeliveryTracking vinculado a la Orden
                \App\Models\DeliveryTracking::create([
                    'tenant_id'    => $venta->tenant_id,
                    'orden_id'     => $orden->id,
                    'driver_id'    => $venta->driver_id,
                    'status'       => \App\Models\DeliveryTracking::STATUS_CREADO,
                    'creado_por'   => Auth::id(),
                ]);
            }

            // Calcular y guardar retenciones ITBIS vendedor
            $cliente = $venta->cliente;
            if ($cliente) {
                $retenciones = $this->retentionService->calcularRetencionesVenta($venta, $cliente);
                $this->retentionService->guardarRetencionesVenta($venta, $retenciones);
            }

            Event::dispatch(new \App\Events\SaleCreated($venta));

            return $venta;
        });

        // Punto único de emisión e-CF: fuera de la transacción, tras el commit (F0.4)
        if (($data['tipo_comprobante'] ?? 'ncf') === 'ecf' && empty($venta->encf)) {
            $this->procesarEcf($venta);
        }

        return $venta;
    }

    public function facturaObrasArte(): bool
    {
        $user = Auth::user();
        $tipo = $user?->businessInstance?->businessType;
        return ($tipo?->config['facturacion_modo'] ?? 'productos') === 'obras_arte';
    }

    public function facturaEquipos(): bool
    {
        $user = Auth::user();
        $tipo = $user?->businessInstance?->businessType;
        return ($tipo?->config['facturacion_modo'] ?? 'productos') === 'equipos';
    }

    public function facturaLavados(): bool
    {
        $user = Auth::user();
        $tipo = $user?->businessInstance?->businessType;
        return ($tipo?->config['facturacion_modo'] ?? 'productos') === 'lavados';
    }

    public function facturaProductosYServicios(): bool
    {
        $user = Auth::user();
        $tipo = $user?->businessInstance?->businessType;
        return ($tipo?->config['facturacion_modo'] ?? 'productos') === 'productos_y_servicios';
    }

    public function facturaProductosYEquipos(): bool
    {
        $user = Auth::user();
        $tipo = $user?->businessInstance?->businessType;
        return ($tipo?->config['facturacion_modo'] ?? 'productos') === 'productos_y_equipos';
    }

    public function itbisPorcentajeInstancia(): float
    {
        $user = Auth::user();
        $config = $user?->businessInstance?->configuracion ?? [];
        return (float) ($config['itbis_porcentaje'] ?? SystemSetting::itbisDefault());
    }

    public function validaStock(): bool
    {
        $user = Auth::user();
        if (!$user?->businessInstance) return true;
        return ($user->businessInstance->configuracion['restaurante_valida_stock'] ?? '1') === '1';
    }

    private function verificarTokenAdmin(?string $token): void
    {
        if (empty($token)) {
            throw new \Exception('Se requiere autorización de un administrador para quitar el ITBIS.');
        }

        try {
            $payload = json_decode(Crypt::decryptString($token), true);
        } catch (\Throwable $e) {
            throw new \Exception('Token de autorización inválido o expirado. Solicita nuevamente la autorización del administrador.');
        }

        if (!is_array($payload) || empty($payload['email']) || empty($payload['tenant_id']) || empty($payload['exp'])) {
            throw new \Exception('Token de autorización inválido. Solicita nuevamente la autorización del administrador.');
        }

        if ((int) $payload['exp'] < now()->timestamp) {
            throw new \Exception('La autorización del administrador expiró. Solicítala nuevamente.');
        }

        if ((int) $payload['tenant_id'] !== (int) Auth::user()->business_instance_id) {
            throw new \Exception('La autorización no corresponde a este negocio.');
        }

        $rolesAdmin = ['admin', 'admin-business', 'root', 'gerente'];
        $admin = User::where('email', $payload['email'])->first();

        $esAdmin = $admin && (
            in_array($admin->role, $rolesAdmin)
            || $admin->hasAnyRole($rolesAdmin)
        );

        if (!$esAdmin) {
            throw new \Exception('El usuario autorizante ya no tiene rol de administrador.');
        }
    }

    public function getCreationData(): array
    {
        $tenantId = Auth::user()->business_instance_id;

        $isElevated = Auth::user()->hasAnyRole(['admin', 'owner', 'admin-business', 'root']);

        $sesionesActivas = SesionCaja::with('caja')
            ->where('estado', 'abierta');

        if (!$isElevated) {
            $sesionesActivas->where('user_id', Auth::id());
        }

        $sesiones = $sesionesActivas->latest('fecha_apertura')->get();

        if ($sesiones->isEmpty()) {
            return [
            'sesiones'           => collect(),
            'sesion'             => null,
            'clienteConsumidorFinal' => null,
            'clientes'           => collect(),
            'tiposVenta'         => collect(),
            'tipoVentaDefault'   => null,
            'almacenes'          => collect(),
            'cajas'              => collect(),
            'productosJs'        => collect(),
            'categoriasJs'       => collect(),
            'clientesJs'         => collect(),
            'ncfSequences'       => collect(),
            ];
        }

        $sesionIdSolicitada = (int) request()->query('sesion_id', 0);
        $sesion = $sesiones->firstWhere('id', $sesionIdSolicitada) ?? $sesiones->first();

        $clienteConsumidorFinal = Cliente::consumidorFinal($tenantId);

        $clientes   = Cliente::where('tenant_id', $tenantId)->orderBy('nombre')->get();
        $tiposVenta = \App\Models\TipoVenta::orderBy('nombre')->get();
        $tipoVentaDefault = $tiposVenta->firstWhere('nombre', 'Contado') ?? $tiposVenta->first();
        $almacenes = \App\Models\Almacen::where('tenant_id', $tenantId)->orderBy('nombre');
        if ($sucursalId = session('sucursal_id')) {
            $almacenes->where('sucursal_id', $sucursalId);
        }
        $almacenes = $almacenes->get();
        $cajas = Caja::where('tenant_id', $tenantId)->orderBy('nombre')->get();
        $ncfSequences = NcfSequence::where('tenant_id', $tenantId)
            ->where('activo', true)
            ->orderBy('prefijo')
            ->get();

        $productos = Producto::select('id', 'nombre', 'codigo_barras', 'precio', 'precio_compra', 'itbis_porcentaje', 'stock', 'ventas_count', 'unidad_medida', 'imagen')
            ->where('tenant_id', $tenantId)
            ->orderBy('nombre')
            ->get();

        $categorias = CategoriaSub::where('tenant_id', $tenantId)
            ->where('activa', true)
            ->with('parent')
            ->orderBy('orden')
            ->get();

        $productosJs = $productos->map(fn($p) => [
            'id'            => (int) $p->id,
            'nombre'        => $p->nombre,
            'codigo_barras' => $p->codigo_barras,
            'precio'        => (float) $p->precio,
            'precio_compra' => (float) ($p->precio_compra ?? 0),
            'itbis_p'       => (float) ($p->itbis_porcentaje ?? SystemSetting::itbisDefault()),
            'stock'          => (int) $p->stock,
            'ventas_count'  => (int) ($p->ventas_count ?? 0),
            'unidad_medida' => $p->unidad_medida ?? 'Unidad',
            'imagen_url'    => $p->imagen_url,
            'categoria_id'  => (int) ($p->categoria_id ?? 0),
        ]);

        $categoriasJs = $categorias->map(fn($c) => [
            'id'       => (int) $c->id,
            'nombre'   => $c->nombre,
            'slug'     => strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-záéíóúÁÉÍÚñÑ0-9\s]/', '', $c->nombre))),
            'padre'    => $c->parent?->nombre ? strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-záéíóúÁÉÍÚñÑ0-9\s]/', '', $c->parent->nombre))) : null,
            'padre_id' => (int) ($c->parent_id ?? 0),
            'hijos'    => $c->children->map(fn($h) => strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-záéíóúÁÉÍÚñÑ0-9\s]/', '', $h->nombre))))->values(),
        ]);

        $clientesJs = $clientes->map(fn($c) => [
            'id'       => (int) $c->id,
            'nombre'   => $c->nombre,
            'tipo'     => $c->tipo_cliente ?? 'consumo',
            'deuda'    => (float) ($c->balance_pendiente ?? 0),
            'es_final' => $c->id === ($clienteConsumidorFinal?->id ?? 0),
            'limite'   => (float) ($c->limite_credito ?? 0),
            'credito_disponible' => (float) ($c->credito_disponible ?? $c->limite_credito ?? 0),
        ]);

        $deliveryCompanies = \App\Models\DeliveryCompany::where('tenant_id', $tenantId)
            ->where('activo', true)
            ->orderBy('nombre_corto')
            ->get();

        $deliveryDrivers = \App\Models\DeliveryDriver::where('tenant_id', $tenantId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return [
            'sesiones'           => $sesiones,
            'sesion'             => $sesion,
            'clienteConsumidorFinal' => $clienteConsumidorFinal,
            'clientes'           => $clientes,
            'tiposVenta'         => $tiposVenta,
            'tipoVentaDefault'   => $tipoVentaDefault,
            'almacenes'          => $almacenes,
            'cajas'              => $cajas,
            'ncfSequences'       => $ncfSequences,
            'productosJs'        => $productosJs,
            'categoriasJs'       => $categoriasJs,
            'clientesJs'         => $clientesJs,
            'deliveryCompanies'  => $deliveryCompanies,
            'deliveryDrivers'    => $deliveryDrivers,
        ];
    }

    /**
     * Procesa las lineas de detalle de venta segun el modo de negocio.
     *
     * Crea los registros VentaDetalle y ejecuta las acciones de inventario
     * (reducir stock, marcar obra vendida, registrar venta de equipo, etc.).
     */
    protected function procesarDetalles(Venta $venta, array $data, ?Venta $ventaExistente): array
    {
        $tenantId = Auth::user()->business_instance_id;
        $sucursalId = session('sucursal_id');
        $almacen = \App\Models\Almacen::where('sucursal_id', $sucursalId)->first();
        $almacenId = $almacen?->id;

        $detalles = [];

        // --- Modo obras de arte ---
        $obraIds = $data['obra_id'] ?? [];
        if (!empty($obraIds)) {
            foreach ($obraIds as $i => $obraId) {
                if (!$obraId) continue;
                $detalle = $this->stock->marcarObraVendida(
                    $venta,
                    (int) $obraId,
                    (float) ($data['precio'][$i] ?? 0),
                    (float) ($data['subtotal'][$i] ?? 0),
                    (float) ($data['descuento'][$i] ?? 0),
                    $data['descuento_tipo'][$i] ?? 'monto',
                    (float) ($data['itbis_porcentaje'][$i] ?? 0),
                    (bool) ($data['sin_itbis'][$i] ?? false),
                    $tenantId
                );
                $detalles[] = $detalle;
            }
        }

        // --- Modo equipos ---
        $equipoIds = $data['equipo_id'] ?? [];
        if (!empty($equipoIds)) {
            foreach ($equipoIds as $i => $equipoId) {
                if (!$equipoId) continue;
                $resultado = $this->stock->registrarVentaEquipo(
                    $venta,
                    (int) $equipoId,
                    (float) ($data['precio'][$i] ?? 0),
                    (float) ($data['subtotal'][$i] ?? 0),
                    (float) ($data['descuento'][$i] ?? 0),
                    $data['descuento_tipo'][$i] ?? 'monto',
                    (float) ($data['itbis_porcentaje'][$i] ?? 0),
                    (bool) ($data['sin_itbis'][$i] ?? false),
                    $tenantId
                );
                $detalles[] = $resultado['detalle'];
            }
        }

        // --- Modo productos (y lavados, mixto, etc.) ---
        $productoIds = $data['producto_id'] ?? [];
        $isLavados = $this->facturaLavados();
        $isProductosYServicios = $this->facturaProductosYServicios();
        $validarStock = $this->validaStock();

        if (!empty($productoIds)) {
            foreach ($productoIds as $i => $productoId) {
                if (!$productoId) continue;

                if ($isLavados || $isProductosYServicios) {
                    // Servicios: no reducen stock
                    $detalle = VentaDetalle::create([
                        'venta_id'         => $venta->id,
                        'producto_id'      => (int) $productoId,
                        'cantidad'         => $data['cantidad'][$i] ?? 1,
                        'precio_unitario'  => (float) ($data['precio'][$i] ?? 0),
                        'subtotal'         => (float) ($data['subtotal'][$i] ?? 0),
                        'descuento'        => (float) ($data['descuento'][$i] ?? 0),
                        'descuento_tipo'   => $data['descuento_tipo'][$i] ?? 'monto',
                        'itbis_porcentaje' => (float) ($data['itbis_porcentaje'][$i] ?? 0),
                        'sin_itbis'        => (bool) ($data['sin_itbis'][$i] ?? false),
                        'almacen_id'       => null,
                        'tenant_id'        => $tenantId,
                        'tipo_linea'       => 'servicio',
                    ]);
                    $detalles[] = $detalle;
                } else {
                    // Productos normales: reducir stock
                    $cantidad = (int) ($data['cantidad'][$i] ?? 1);
                    $this->stock->reducirStockProducto(
                        $venta,
                        $ventaExistente,
                        (int) $productoId,
                        $cantidad,
                        $almacenId,
                        $this->calc,
                        $validarStock
                    );

                    $detalle = VentaDetalle::create([
                        'venta_id'         => $venta->id,
                        'producto_id'      => (int) $productoId,
                        'almacen_id'       => $almacenId,
                        'cantidad'         => $cantidad,
                        'precio_unitario'  => (float) ($data['precio'][$i] ?? 0),
                        'subtotal'         => (float) ($data['subtotal'][$i] ?? 0),
                        'descuento'        => (float) ($data['descuento'][$i] ?? 0),
                        'descuento_tipo'   => $data['descuento_tipo'][$i] ?? 'monto',
                        'itbis_porcentaje' => (float) ($data['itbis_porcentaje'][$i] ?? 0),
                        'sin_itbis'        => (bool) ($data['sin_itbis'][$i] ?? false),
                        'tenant_id'        => $tenantId,
                    ]);
                    $detalles[] = $detalle;
                }
            }
        }

        return $detalles;
    }

    /**
     * Procesa el pago de la venta segun el metodo de pago.
     */
    protected function procesarPago(Venta $venta, SesionCaja $sesion, string $metodo, string $estado, array $data): void
    {
        if (in_array($metodo, ['fiado', 'cuenta_abierta'], true)) {
            return;
        }

        // Pago mixto
        if ($metodo === 'mixto') {
            $pagosMixtos = [
                ['metodo' => 'efectivo', 'campo' => 'monto_recibido'],
                ['metodo' => 'tarjeta', 'campo' => 'monto_tarjeta'],
                ['metodo' => 'transferencia', 'campo' => 'monto_transferencia'],
            ];

            foreach ($pagosMixtos as $pagoMixto) {
                $monto = (float) ($data[$pagoMixto['campo']] ?? 0);
                if ($monto > 0) {
                    Pago::create([
                        'tenant_id'      => Auth::user()->business_instance_id,
                        'venta_id'       => $venta->id,
                        'caja_id'        => $sesion->caja_id,
                        'sesion_caja_id' => $sesion->id,
                        'monto'          => $monto,
                        'metodo_pago'    => $pagoMixto['metodo'],
                        'nota'           => 'PagoVenta #' . $venta->id . ' - ' . ucfirst($pagoMixto['metodo']),
                        'fecha_pago'     => now(),
                    ]);
                }
            }

            return;
        }

        $monto = (float) ($data['monto_recibido'] ?? $data['total'] ?? $venta->total);

        if ($monto > 0) {
            Pago::create([
                'tenant_id'      => Auth::user()->business_instance_id,
                'venta_id'       => $venta->id,
                'caja_id'        => $sesion->caja_id,
                'sesion_caja_id' => $sesion->id,
                'monto'          => $monto,
                'metodo_pago'    => $metodo,
                'nota'           => 'PagoVenta #' . $venta->id . ' - ' . ucfirst($metodo),
                'fecha_pago'     => now(),
            ]);
        }
    }

    /**
     * Genera, firma y envia el e-CF de la venta (fuera de la transaccion).
     */
    protected function procesarEcf(Venta $venta): ?\App\Models\EcfDocumento
    {
        try {
            $ecf = $this->ecfService->generarEcf($venta);
            $ecf = $this->ecfService->firmar($ecf);
            $ecf = $this->ecfService->enviar($ecf);

            $venta->update([
                'encf'              => $ecf->encf,
                'tipo_comprobante'  => 'ecf',
            ]);

            return $ecf;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error al procesar e-CF de venta #' . $venta->id, [
                'venta_id' => $venta->id,
                'error'    => $e->getMessage(),
            ]);

            return null;
        }
    }
}
