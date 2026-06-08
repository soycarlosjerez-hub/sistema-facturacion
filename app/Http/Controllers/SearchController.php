<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->input('q', '');
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');

        $results = [];

        if ($isAdmin || $user->can('productos.view')) {
            $productos = Producto::where('nombre', 'like', "%{$q}%")
                ->orWhere('codigo_barras', 'like', "%{$q}%")
                ->limit(5)
                ->get(['id', 'nombre', 'codigo_barras', 'precio', 'stock'])
                ->map(fn($p) => [
                    'type' => 'producto',
                    'label' => $p->nombre,
                    'sub' => 'RD$ ' . number_format($p->precio, 2) . ' · Stock: ' . $p->stock,
                    'url' => route('productos.show', $p),
                    'icon' => 'bi-box-seam',
                    'badge' => $p->codigo_barras,
                ]);
            $results = array_merge($results, $productos->toArray());
        }

        if ($isAdmin || $user->can('clientes.view')) {
            $clientes = Cliente::where('nombre', 'like', "%{$q}%")
                ->orWhere('rnc_cedula', 'like', "%{$q}%")
                ->orWhere('telefono', 'like', "%{$q}%")
                ->limit(5)
                ->get(['id', 'nombre', 'rnc_cedula', 'telefono'])
                ->map(fn($c) => [
                    'type' => 'cliente',
                    'label' => $c->nombre,
                    'sub' => $c->rnc_cedula ? 'RNC: ' . $c->rnc_cedula : ($c->telefono ? 'Tel: ' . $c->telefono : ''),
                    'url' => route('clientes.show', $c),
                    'icon' => 'bi-people',
                    'badge' => '',
                ]);
            $results = array_merge($results, $clientes->toArray());
        }

        if ($isAdmin || $user->can('ventas.view') || $user->can('ventas.view.own')) {
            $query = Venta::with('cliente:id,nombre');
            if (!$isAdmin && !$user->can('ventas.view')) {
                $query->where('user_id', $user->id);
            }
            $ventas = $query->where(function ($subq) use ($q) {
                    $subq->where('id', 'like', "%{$q}%")
                      ->orWhereHas('cliente', fn($cq) => $cq->where('nombre', 'like', "%{$q}%"));
                })
                ->limit(5)
                ->get()
                ->map(fn($v) => [
                    'type' => 'venta',
                    'label' => 'Venta #' . str_pad($v->id, 5, '0', STR_PAD_LEFT),
                    'sub' => $v->cliente?->nombre ?? 'Consumidor Final',
                    'url' => route('ventas.show', $v),
                    'icon' => 'bi-receipt',
                    'badge' => 'RD$ ' . number_format($v->total, 0),
                ]);
            $results = array_merge($results, $ventas->toArray());
        }

        if ($isAdmin || $user->can('compras.view')) {
            $compras = Compra::with('proveedor:id,nombre')
                ->where(function ($subq) use ($q) {
                    $subq->whereHas('proveedor', fn($cq) => $cq->where('nombre', 'like', "%{$q}%"))
                          ->orWhere('id', 'like', "%{$q}%");
                })
                ->limit(5)
                ->get()
                ->map(fn($c) => [
                    'type' => 'compra',
                    'label' => 'Compra #' . str_pad($c->id, 5, '0', STR_PAD_LEFT),
                    'sub' => $c->proveedor?->nombre ?? '—',
                    'url' => route('compras.show', $c),
                    'icon' => 'bi-cart-check',
                    'badge' => 'RD$ ' . number_format($c->total, 0),
                ]);
            $results = array_merge($results, $compras->toArray());
        }

        if ($isAdmin || $user->can('proveedores.view')) {
            $proveedores = Proveedor::where('nombre', 'like', "%{$q}%")
                ->orWhere('rnc', 'like', "%{$q}%")
                ->limit(5)
                ->get(['id', 'nombre', 'rnc'])
                ->map(fn($p) => [
                    'type' => 'proveedor',
                    'label' => $p->nombre,
                    'sub' => $p->rnc ? 'RNC: ' . $p->rnc : '',
                    'url' => route('proveedores.index'),
                    'icon' => 'bi-truck',
                    'badge' => '',
                ]);
            $results = array_merge($results, $proveedores->toArray());
        }

        return response()->json($results);
    }
}
