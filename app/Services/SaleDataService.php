<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Cliente;

class SaleDataService
{
    /**
     * Prepares data for the sales creation view.
     * This includes formatting products and clients for frontend consumption (e.g., selection, pricing).
     */
    public function getSaleCreationData(): array
    {
        $clienteConsumidorFinal = Cliente::firstOrCreate(
            ['nombre' => 'Consumidor Final'],
            ['limite_credito' => 0, 'balance_pendiente' => 0, 'tipo_cliente' => 'consumo']
        );

        $clientes = Cliente::orderBy('nombre')->get();
        $tiposVenta = \App\Models\TipoVenta::orderBy('nombre')->get();
        $tipoVentaDefault = $tiposVenta->firstWhere('nombre', 'Contado') ?? $tiposVenta->first();

        $productos = Producto::orderBy('nombre')
            ->select('id', 'nombre', 'codigo_barras', 'precio', 'precio_compra', 'itbis_porcentaje', 'stock', 'ventas_count', 'unidad_medida', 'imagen')
            ->get();

        $almacenes = \App\Models\Almacen::orderBy('nombre')->get();

        // Prepare products for frontend (JS/Vue/React)
        $productosJs = $productos->map(fn($p) => [
            'id'            => (int) $p->id,
            'nombre'        => $p->nombre,
            'codigo_barras' => $p->codigo_barras,
            'precio'        => (float) $p->precio,
            'precio_compra' => (float) ($p->precio_compra ?? 0),
            'itbis_p'       => (float) ($p->itbis_porcentaje ?? 18),
            'stock'          => (int) $p->stock,
            'ventas_count'  => (int) ($p->ventas_count ?? 0),
            'unidad_medida' => $p->unidad_medida ?? 'Unidad',
            'imagen_url'    => $p->imagen_url,
        ]);

        $clientesJs = $clientes->map(fn($c) => [
            'id'       => (int) $c->id,
            'nombre'   => $c->nombre,
            'tipo'     => $c->tipo_cliente ?? 'consumo',
            'deuda'    => (float) ($c->balance_pendiente ?? 0),
            'es_final' => $c->id === $clienteConsumidorFinal->id,
        ]);

        return [
            'clientes'             => $clientes,
            'tiposVenta'           => $tiposVenta,
            'productos'            => $productos,
            'almacenes'            => $almacenes,
            'productosJs'          => $productosJs,
            'clientesJs'           => $clientesJs,
            'tipoVentaDefault'     => $tipoVentaDefault,
            'clienteConsumidorFinal' => $clienteConsumidorFinal,
        ];
    }
}
