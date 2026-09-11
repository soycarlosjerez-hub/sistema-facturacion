<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\EcfDocumento;
use App\Models\Venta;

class PlantillaVariablesService
{
    public function paraVenta(Venta $venta): array
    {
        $sistema = \App\Models\SystemSetting::allCached();

        return [
            'empresa' => [
                'nombre' => \App\Models\SystemSetting::nombreEmpresaActual(),
                'rnc' => $sistema['empresa_rnc'] ?? '',
                'direccion' => $sistema['empresa_direccion'] ?? '',
                'telefono' => $sistema['empresa_telefono'] ?? '',
                'email' => $sistema['empresa_email'] ?? '',
                'slogan' => $sistema['sistema_slogan'] ?? '',
            ],
            'documento' => [
                'numero' => str_pad($venta->id, 5, '0', STR_PAD_LEFT),
                'ncf' => $venta->ncf ?? '',
                'ncf_tipo' => $venta->ncf_tipo ?? '',
                'ncf_vencimiento' => $venta->ncf_vencimiento?->format('d/m/Y'),
                'tipo_comprobante' => $venta->tipo_comprobante ?? 'NCF',
                'encf' => $venta->encf ?? '',
                'fecha_emision' => $venta->created_at->format('d/m/Y H:i'),
                'estado' => $venta->estado,
                'es_anulada' => $venta->trashed() || $venta->estado === 'anulada',
                'tipo_orden' => $venta->tipo_orden ?? '',
            ],
            'cliente' => [
                'nombre' => $venta->cliente?->nombre ?? 'Consumidor Final',
                'rnc_cedula' => $venta->cliente?->rnc_cedula ?? $venta->cliente?->documento ?? '',
                'direccion' => $venta->cliente?->direccion ?? '',
                'telefono' => $venta->cliente?->telefono ?? '',
            ],
            'productos' => $venta->detalles->map(function ($d) use ($sistema) {
                $precioUnitario = number_format($d->precio_unitario, 2);
                $subtotal = number_format($d->subtotal, 2);
                $cantidad = number_format($d->cantidad, 2);
                $descuento = number_format($d->descuento, 2);

                $itbisPorcentaje = $d->sin_itbis ? 0 : ($d->producto?->itbis_porcentaje ?? $d->itbis_porcentaje ?? ($sistema['impuesto_itbis'] ?? 18));

                $nombreProducto = '';
                if ($d->producto) {
                    $nombreProducto = $d->producto->nombre;
                } elseif ($d->obra) {
                    $nombreProducto = 'Obra: '.($d->obra->titulo ?? 'Sin título');
                } elseif ($d->servicio) {
                    $nombreProducto = $d->servicio->nombre ?? 'Servicio';
                } else {
                    $nombreProducto = $d->notas ?: 'Concepto';
                }

                return [
                    'nombre' => $nombreProducto,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotal,
                    'descuento' => $descuento,
                    'itbis_porcentaje' => $itbisPorcentaje,
                    'sin_itbis' => $d->sin_itbis,
                    'garantia_dias' => $d->producto?->garantia_dias ?? 0,
                    'garantia_meses' => $d->producto?->garantia_meses ?? 0,
                    'garantia_terminos' => $d->producto?->garantia_terminos ?? '',
                    'notas' => $d->notas ?? '',
                    'tipo_linea' => $d->tipo_linea ?? 'producto',
                    'curso' => $d->curso ?? '',
                ];
            }),
            'totales' => [
                'subtotal' => number_format($venta->subtotal, 2),
                'impuestos' => number_format($venta->impuestos, 2),
                'descuento' => number_format($venta->descuento, 2),
                'general_descuento' => number_format($venta->general_descuento, 2),
                'propina' => number_format($venta->propina, 2),
                'cargo_servicio' => number_format($venta->cargo_servicio, 2),
                'delivery_fee' => number_format($venta->delivery_fee, 2),
                'total' => number_format($venta->total, 2),
            ],
            'pagos' => $venta->pagos->map(function ($p) {
                return [
                    'metodo' => ucfirst(str_replace('_', ' ', $p->metodo_pago)),
                    'monto' => number_format($p->monto, 2),
                ];
            }),
            'sucursal' => [
                'nombre' => $venta->sucursal?->nombre ?? '',
            ],
            'caja' => [
                'nombre' => $venta->caja?->nombre ?? '',
            ],
            'usuario' => [
                'nombre' => $venta->usuario?->name ?? '',
            ],
            'notas' => $venta->notas ?? '',
            'descuento_motivo' => $venta->descuento_motivo ?? '',
        ];
    }

    public function paraEcf(EcfDocumento $ecf, ?string $qrUrl = null): array
    {
        $ventaData = $this->paraVenta($ecf->venta);
        $empresa = \App\Models\SystemSetting::allCached();

        $ventaData['documento'] = [
            'numero' => str_pad($ecf->venta->id, 5, '0', STR_PAD_LEFT),
            'ncf' => $ecf->venta->ncf ?? '',
            'ncf_tipo' => $ecf->venta->ncf_tipo ?? '',
            'ncf_vencimiento' => $ecf->venta->ncf_vencimiento?->format('d/m/Y'),
            'tipo_comprobante' => 'e-CF',
            'encf' => $ecf->encf ?? '',
            'fecha_emision' => $ecf->fecha_emision->format('d/m/Y H:i'),
            'estado' => $ecf->estado,
            'es_anulada' => $ecf->estado === 'anulado',
            'tipo_orden' => $ecf->venta->tipo_orden ?? '',
        ];

        $ventaData['ecf'] = [
            'encf' => $ecf->encf,
            'tipo_ecf' => $ecf->tipo_ecf,
            'tipo_nombre' => $ecf->tipo_nombre,
            'track_id_dgii' => $ecf->track_id_dgii ?? '',
            'firma_digital' => $ecf->firma_digital ?? '',
            'codigo_seguridad' => $ecf->codigo_seguridad ?? '',
            'fecha_aprobacion' => $ecf->fecha_aprobacion?->format('d/m/Y h:i:s A'),
            'mensaje_dgii' => $ecf->mensaje_dgii ?? '',
            'monto_gravado' => number_format($ecf->monto_gravado_total, 2),
            'monto_exento' => number_format($ecf->monto_exento_total, 2),
            'itbis_total' => number_format($ecf->itbis_total, 2),
            'monto_total' => number_format($ecf->monto_total, 2),
            'estado' => $ecf->estado,
            'estado_info' => $ecf->estado_info,
            'qr_url' => $qrUrl ?? '',
        ];

        return $ventaData;
    }

    public function paraCompra(Compra $compra): array
    {
        $sistema = \App\Models\SystemSetting::allCached();

        return [
            'empresa' => [
                'nombre' => \App\Models\SystemSetting::nombreEmpresaActual(),
                'rnc' => $sistema['empresa_rnc'] ?? '',
                'direccion' => $sistema['empresa_direccion'] ?? '',
                'telefono' => $sistema['empresa_telefono'] ?? '',
                'email' => $sistema['empresa_email'] ?? '',
                'slogan' => $sistema['sistema_slogan'] ?? '',
            ],
            'documento' => [
                'numero' => str_pad($compra->id, 5, '0', STR_PAD_LEFT),
                'folio' => $compra->folio ?? '',
                'fecha_emision' => $compra->fecha?->format('d/m/Y'),
                'estado' => 'completada',
            ],
            'proveedor' => [
                'nombre' => $compra->proveedor?->nombre ?? 'N/A',
                'rnc_cedula' => $compra->proveedor?->rnc_cedula ?? $compra->proveedor?->documento ?? '',
                'direccion' => $compra->proveedor?->direccion ?? '',
                'telefono' => $compra->proveedor?->telefono ?? '',
            ],
            'productos' => $compra->detalles->map(function ($d) {
                return [
                    'nombre' => $d->producto?->nombre ?? 'N/A',
                    'cantidad' => number_format($d->cantidad, 0),
                    'precio_unitario' => number_format($d->precio_unitario, 2),
                    'subtotal' => number_format($d->subtotal, 2),
                    'itbis_porcentaje' => $d->itbis_porcentaje ?? 18,
                ];
            }),
            'totales' => [
                'subtotal' => number_format($compra->subtotal, 2),
                'itbis' => number_format($compra->itbis_total, 2),
                'total' => number_format($compra->total, 2),
                'total_retenciones' => number_format($compra->total_retenciones, 2),
                'total_pagar' => number_format($compra->total_pagar, 2),
            ],
            'sucursal' => [
                'nombre' => $compra->sucursal?->nombre ?? '',
            ],
            'usuario' => [
                'nombre' => $compra->user?->name ?? '',
            ],
            'observaciones' => $compra->observaciones ?? '',
        ];
    }

    public function datosDemo(): array
    {
        return [
            'empresa' => [
                'nombre' => 'Mi Negocio Demo',
                'rnc' => '130-12345-6',
                'direccion' => 'Av. Winston Churchill, Santo Domingo',
                'telefono' => '(809) 555-1234',
                'email' => 'info@minegocio.do',
                'slogan' => 'Tu solución de confianza',
            ],
            'documento' => [
                'numero' => '00001',
                'ncf' => 'B0100000001',
                'ncf_tipo' => 'B01',
                'ncf_vencimiento' => '12/12/2027',
                'tipo_comprobante' => 'NCF',
                'encf' => '',
                'fecha_emision' => date('d/m/Y H:i'),
                'estado' => 'completada',
                'es_anulada' => false,
                'tipo_orden' => 'local',
            ],
            'cliente' => [
                'nombre' => 'Juan Pérez',
                'rnc_cedula' => '402-1234567-8',
                'direccion' => 'Calle El Sol #15',
                'telefono' => '(809) 555-9876',
            ],
            'productos' => collect([
                [
                    'nombre' => 'Producto de Ejemplo 1',
                    'cantidad' => '2.00',
                    'precio_unitario' => '500.00',
                    'subtotal' => '1000.00',
                    'descuento' => '0.00',
                    'itbis_porcentaje' => '18',
                    'sin_itbis' => false,
                    'garantia_dias' => 0,
                    'garantia_meses' => 0,
                    'garantia_terminos' => '',
                    'notas' => '',
                    'tipo_linea' => 'producto',
                    'curso' => '',
                ],
                [
                    'nombre' => 'Servicio de Ejemplo',
                    'cantidad' => '1.00',
                    'precio_unitario' => '2500.00',
                    'subtotal' => '2500.00',
                    'descuento' => '250.00',
                    'itbis_porcentaje' => '18',
                    'sin_itbis' => false,
                    'garantia_dias' => 30,
                    'garantia_meses' => '3',
                    'garantia_terminos' => 'Garantía de 3 meses por defectos de fábrica.',
                    'notas' => 'Servicio técnico especializado',
                    'tipo_linea' => 'servicio',
                    'curso' => '',
                ],
            ]),
            'totales' => [
                'subtotal' => '3500.00',
                'impuestos' => '630.00',
                'descuento' => '250.00',
                'general_descuento' => '0.00',
                'propina' => '0.00',
                'cargo_servicio' => '0.00',
                'delivery_fee' => '0.00',
                'total' => '3880.00',
            ],
            'pagos' => collect([
                ['metodo' => 'Efectivo', 'monto' => '2000.00'],
                ['metodo' => 'Tarjeta', 'monto' => '1880.00'],
            ]),
            'sucursal' => ['nombre' => 'Sucursal Principal'],
            'caja' => ['nombre' => 'Caja 1'],
            'usuario' => ['nombre' => 'Admin User'],
            'notas' => 'Gracias por su compra.',
            'descuento_motivo' => 'Promoción especial',
        ];
    }

    public function datosCompra(): array
    {
        return [
            'empresa' => [
                'nombre' => 'Mi Negocio Demo',
                'rnc' => '130-12345-6',
                'direccion' => 'Av. Winston Churchill, Santo Domingo',
                'telefono' => '(809) 555-1234',
                'email' => 'info@minegocio.do',
                'slogan' => 'Tu solución de confianza',
            ],
            'documento' => [
                'numero' => '00001',
                'folio' => 'F-001',
                'fecha_emision' => date('d/m/Y'),
                'estado' => 'completada',
            ],
            'proveedor' => [
                'nombre' => 'Distribuidora Demo S.A.',
                'rnc_cedula' => '131-00001-2',
                'direccion' => 'Zona Industrial, Sector 7',
                'telefono' => '(809) 555-4567',
            ],
            'productos' => collect([
                [
                    'nombre' => 'Producto de Ejemplo 1',
                    'cantidad' => '10',
                    'precio_unitario' => '500.00',
                    'subtotal' => '5000.00',
                    'itbis_porcentaje' => 18,
                ],
                [
                    'nombre' => 'Servicio de Ejemplo',
                    'cantidad' => '2',
                    'precio_unitario' => '2500.00',
                    'subtotal' => '5000.00',
                    'itbis_porcentaje' => 18,
                ],
            ]),
            'totales' => [
                'subtotal' => '10000.00',
                'itbis' => '1800.00',
                'total' => '11800.00',
                'total_retenciones' => '0.00',
                'total_pagar' => '11800.00',
            ],
            'sucursal' => ['nombre' => 'Sucursal Principal'],
            'usuario' => ['nombre' => 'Admin User'],
            'notas' => 'Compra de prueba para vista previa.',
        ];
    }

    public function datosEcf(): array
    {
        $base = $this->datosDemo();
        $base['documento']['tipo_comprobante'] = 'e-CF';
        $base['documento']['encf'] = 'E31000000001';
        $base['ecf'] = [
            'encf' => 'E31000000001',
            'tipo_ecf' => 'E31',
            'tipo_nombre' => 'Crédito Fiscal',
            'track_id_dgii' => '1234567890',
            'firma_digital' => 'abc123def456ghi789jkl012mno345pqr678stu901vwx234',
            'codigo_seguridad' => 'ABC123',
            'fecha_aprobacion' => date('d/m/Y h:i:s A'),
            'mensaje_dgii' => 'Procesado exitosamente',
            'monto_gravado' => '3,500.00',
            'monto_exento' => '0.00',
            'itbis_total' => '630.00',
            'monto_total' => '4,130.00',
            'estado' => 'aprobado',
            'estado_info' => ['label' => 'Aprobado', 'color' => 'success', 'icon' => 'bi-check-circle-fill'],
            'qr_url' => '',
        ];

        return $base;
    }

    public function paraHistorial($ventas, array $filtros = []): array
    {
        $sistema = \App\Models\SystemSetting::allCached();

        $subtotal = $ventas->sum('subtotal');
        $impuestos = $ventas->sum('impuestos');
        $total = $ventas->sum('total');

        return [
            'empresa' => [
                'nombre' => \App\Models\SystemSetting::nombreEmpresaActual(),
                'rnc' => $sistema['empresa_rnc'] ?? '',
                'direccion' => $sistema['empresa_direccion'] ?? '',
                'telefono' => $sistema['empresa_telefono'] ?? '',
                'email' => $sistema['empresa_email'] ?? '',
                'slogan' => $sistema['sistema_slogan'] ?? '',
            ],
            'filtros' => [
                'cliente' => $filtros['cliente'] ?? '',
                'desde' => $filtros['desde'] ?? '',
                'hasta' => $filtros['hasta'] ?? '',
            ],
            'ventas' => $ventas->map(function ($v) {
                return [
                    'id' => $v->id,
                    'ncf' => $v->ncf ?? '',
                    'cliente' => $v->cliente?->nombre ?? 'N/A',
                    'usuario' => $v->usuario?->name ?? 'N/A',
                    'sucursal' => $v->sucursal?->nombre ?? '',
                    'tipo' => $v->tipoVenta?->nombre ?? 'N/A',
                    'fecha' => $v->created_at->format('d/m/Y'),
                    'hora' => $v->created_at->format('h:i A'),
                    'subtotal' => number_format($v->subtotal, 2),
                    'impuestos' => number_format($v->impuestos, 2),
                    'total' => number_format($v->total, 2),
                    'estado' => $v->estado ?? 'completada',
                ];
            }),
            'resumen' => [
                'cantidad' => $ventas->count(),
                'subtotal' => number_format($subtotal, 2),
                'impuestos' => number_format($impuestos, 2),
                'total' => number_format($total, 2),
            ],
        ];
    }

    public function datosHistorial(): array
    {
        return [
            'empresa' => [
                'nombre' => 'Mi Negocio Demo',
                'rnc' => '130-12345-6',
                'direccion' => 'Av. Winston Churchill, Santo Domingo',
                'telefono' => '(809) 555-1234',
                'email' => 'info@minegocio.do',
                'slogan' => 'Tu solución de confianza',
            ],
            'filtros' => [
                'cliente' => '',
                'desde' => date('d/m/Y', strtotime('-30 days')),
                'hasta' => date('d/m/Y'),
            ],
            'ventas' => collect([
                ['id' => 1, 'ncf' => 'B0100000001', 'cliente' => 'Juan Pérez', 'usuario' => 'Admin', 'sucursal' => 'Principal', 'tipo' => 'Contado', 'fecha' => '10/09/2026', 'hora' => '10:30 AM', 'subtotal' => '1,250.00', 'impuestos' => '225.00', 'total' => '1,475.00', 'estado' => 'completada'],
                ['id' => 2, 'ncf' => 'B0100000002', 'cliente' => 'María López', 'usuario' => 'Cajero1', 'sucursal' => 'Principal', 'tipo' => 'Crédito', 'fecha' => '10/09/2026', 'hora' => '11:15 AM', 'subtotal' => '3,500.00', 'impuestos' => '630.00', 'total' => '4,130.00', 'estado' => 'completada'],
                ['id' => 3, 'ncf' => 'B0100000003', 'cliente' => 'Pedro García', 'usuario' => 'Admin', 'sucursal' => 'Sucursal 2', 'tipo' => 'Contado', 'fecha' => '09/09/2026', 'hora' => '02:45 PM', 'subtotal' => '800.00', 'impuestos' => '144.00', 'total' => '944.00', 'estado' => 'completada'],
            ]),
            'resumen' => [
                'cantidad' => 3,
                'subtotal' => '5,550.00',
                'impuestos' => '999.00',
                'total' => '6,549.00',
            ],
        ];
    }
}
