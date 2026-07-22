<?php

namespace App\Http\Controllers;

use App\Models\ListaPrecio;
use App\Models\ListaPrecioItem;
use App\Models\ListaPrecioLog;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListaPrecioController extends Controller
{
    public function index()
    {
        $listas = ListaPrecio::withCount('items')->orderBy('nombre')->get();
        return view('listas-precio.index', compact('listas'));
    }

    public function create()
    {
        return view('listas-precio.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:20|unique:lista_precios,codigo',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'vigencia_desde' => 'nullable|date',
            'vigencia_hasta' => 'nullable|date|after_or_equal:vigencia_desde',
            'activa' => 'nullable|boolean',
        ]);

        $data['activa'] = $request->boolean('activa');
        $data['tenant_id'] = Auth::user()->business_instance_id;

        ListaPrecio::create($data);

        return redirect()->route('listas-precio.index')->with('success', 'Lista de precios creada exitosamente.');
    }

    public function show(ListaPrecio $listaPrecio)
    {
        $listaPrecio->load('items.producto');
        $productos = Producto::orderBy('nombre')->get();
        return view('listas-precio.show', compact('listaPrecio', 'productos'));
    }

    public function edit(ListaPrecio $listaPrecio)
    {
        $listaPrecio->load(['items.producto' => function ($query) {
            $query->select('id', 'nombre', 'codigo_barras', 'precio_compra', 'precio');
        }]);
        $productos = Producto::orderBy('nombre')->get();
        return view('listas-precio.edit', compact('listaPrecio', 'productos'));
    }

    public function update(Request $request, ListaPrecio $listaPrecio)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:20|unique:lista_precios,codigo,' . $listaPrecio->id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'vigencia_desde' => 'nullable|date',
            'vigencia_hasta' => 'nullable|date|after_or_equal:vigencia_desde',
            'activa' => 'nullable|boolean',
        ]);

        $data['activa'] = $request->boolean('activa');

        // Log changes in metadata fields
        $changesToLog = [];
        if ($listaPrecio->getRawOriginal('codigo') != $data['codigo']) {
            $changesToLog[] = 'codigo';
        }
        if ($listaPrecio->getRawOriginal('nombre') != $data['nombre']) {
            $changesToLog[] = 'nombre';
        }
        if ($listaPrecio->getRawOriginal('vigencia_desde')?->toDateString() != $data['vigencia_desde']?->toDateString()) {
            $changesToLog[] = 'vigencia';
        }
        if ($listaPrecio->getRawOriginal('vigencia_hasta')?->toDateString() != $data['vigencia_hasta']?->toDateString()) {
            $changesToLog[] = 'vigencia';
        }
        if ($listaPrecio->getRawOriginal('activa') != $data['activa']) {
            $changesToLog[] = 'activo';
        }

        if (!empty($changesToLog)) {
            ListaPrecioLog::create([
                'tenant_id' => Auth::user()->business_instance_id,
                'lista_precio_id' => $listaPrecio->id,
                'producto_id' => null,
                'precio_anterior' => null,
                'precio_nuevo' => null,
                'usuario_id' => Auth::id(),
                'cambio_en' => implode(', ', $changesToLog),
                'observacion' => 'Actualización de información general de la lista',
            ]);
        }

        $listaPrecio->update($data);

        return redirect()->route('listas-precio.index')->with('success', 'Lista de precios actualizada.');
    }

    public function destroy(ListaPrecio $listaPrecio)
    {
        // Soft delete: items are kept for historical reference
        $listaPrecio->delete();
        return redirect()->route('listas-precio.index')->with('success', 'Lista de precios eliminada.');
    }

    public function actualizarPrecios(Request $request, ListaPrecio $listaPrecio)
    {
        $request->validate([
            'precios' => 'required|array',
            'precios.*.producto_id' => 'required|exists:productos,id',
            'precios.*.precio' => 'required|numeric|min:0',
        ]);

        $count = 0;
        DB::transaction(function () use ($request, $listaPrecio, &$count) {
            foreach ($request->precios as $item) {
                $precioNuevo = (float) $item['precio'];
                $existing = ListaPrecioItem::where('lista_precio_id', $listaPrecio->id)
                    ->where('producto_id', $item['producto_id'])
                    ->first();

                $precioAnterior = $existing ? (float) $existing->precio : 0;

                if ($existing) {
                    $existing->update([
                        'precio' => $precioNuevo,
                        'tenant_id' => Auth::user()->business_instance_id,
                    ]);
                } else {
                    ListaPrecioItem::create([
                        'lista_precio_id' => $listaPrecio->id,
                        'producto_id' => $item['producto_id'],
                        'precio' => $precioNuevo,
                        'tenant_id' => Auth::user()->business_instance_id,
                    ]);
                }

                // Log price change if price was updated (not created)
                if ($existing && abs($precioAnterior - $precioNuevo) > 0.001) {
                    ListaPrecioLog::create([
                        'tenant_id' => Auth::user()->business_instance_id,
                        'lista_precio_id' => $listaPrecio->id,
                        'producto_id' => $item['producto_id'],
                        'precio_anterior' => $precioAnterior,
                        'precio_nuevo' => $precioNuevo,
                        'usuario_id' => Auth::id(),
                        'cambio_en' => 'precio',
                        'observacion' => 'Precio actualizado vía edición masiva',
                    ]);
                }

                $count++;
            }
        });

        return redirect()->route('listas-precio.edit', $listaPrecio)
            ->with('success', "{$count} precios actualizados en la lista.");
    }

    public function quitarProducto(ListaPrecio $listaPrecio, ListaPrecioItem $item)
    {
        if ($item->lista_precio_id !== $listaPrecio->id) {
            return back()->with('error', 'El item no pertenece a esta lista.');
        }

        // Log removal
        ListaPrecioLog::create([
            'tenant_id' => Auth::user()->business_instance_id,
            'lista_precio_id' => $listaPrecio->id,
            'producto_id' => $item->producto_id,
            'precio_anterior' => $item->precio,
            'precio_nuevo' => null,
            'usuario_id' => Auth::id(),
            'cambio_en' => 'precio',
            'observacion' => 'Producto removido de la lista',
        ]);

        $item->delete();
        return back()->with('success', 'Producto quitado de la lista.');
    }

    public function duplicar(ListaPrecio $listaPrecio)
    {
        $nuevoCodigo = substr($listaPrecio->codigo, 0, 15) . '-CPY';
        $nueva = ListaPrecio::create([
            'codigo' => $nuevoCodigo,
            'nombre' => $listaPrecio->nombre . ' (Copia)',
            'descripcion' => $listaPrecio->descripcion,
            'activa' => false,
            'tenant_id' => Auth::user()->business_instance_id,
        ]);

        foreach ($listaPrecio->items as $item) {
            ListaPrecioItem::create([
                'lista_precio_id' => $nueva->id,
                'producto_id' => $item->producto_id,
                'precio' => $item->precio,
                'tenant_id' => Auth::user()->business_instance_id,
            ]);
        }

        return redirect()->route('listas-precio.edit', $nueva)
            ->with('success', 'Lista duplicada exitosamente. Revisa y activa cuando esté lista.');
    }

    /**
     * Restore a soft-deleted lista precio (for future trash view).
     */
    public function restore(ListaPrecio $listaPrecio)
    {
        if (!$listaPrecio->trashed()) {
            return back()->with('error', 'Esta lista no está eliminada.');
        }
        $listaPrecio->restore();
        return back()->with('success', 'Lista de precios restaurada.');
    }

    /**
     * Price impact report for a list.
     */
    public function impacto(ListaPrecio $listaPrecio)
    {
        $listaPrecio->load('items.producto:id,nombre,precio_compra');

        $totalProductos = $listaPrecio->items->count();
        $sumaPreciosBase = 0;
        $sumaPreciosLista = 0;

        foreach ($listaPrecio->items as $item) {
            $precioBase = (float) ($item->producto->precio_compra ?? 0);
            $sumaPreciosBase += $precioBase;
            $sumaPreciosLista += (float) $item->precio;
        }

        $diferencia = $sumaPreciosLista - $sumaPreciosBase;
        $porcentajeDescuento = $sumaPreciosBase > 0
            ? (($diferencia / $sumaPreciosBase) * 100)
            : 0;

        return view('listas-precio.impacto', compact(
            'listaPrecio',
            'totalProductos',
            'sumaPreciosBase',
            'sumaPreciosLista',
            'diferencia',
            'porcentajeDescuento'
        ));
    }

    /**
     * View price change history for a list.
     */
    public function logs(ListaPrecio $listaPrecio)
    {
        $logs = $listaPrecio->logs()
            ->with(['producto:id,nombre,codigo_barras', 'usuario:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('listas-precio.logs', compact('listaPrecio', 'logs'));
    }
}
