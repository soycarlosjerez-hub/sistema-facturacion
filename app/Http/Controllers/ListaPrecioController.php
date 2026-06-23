<?php

namespace App\Http\Controllers;

use App\Models\ListaPrecio;
use App\Models\ListaPrecioItem;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListaPrecioController extends Controller
{
    public function index()
    {
        $listas = ListaPrecio::orderBy('nombre')->get();
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
        $listaPrecio->load('items.producto');
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

        $listaPrecio->update($data);

        return redirect()->route('listas-precio.index')->with('success', 'Lista de precios actualizada.');
    }

    public function destroy(ListaPrecio $listaPrecio)
    {
        $listaPrecio->items()->delete();
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

        DB::transaction(function () use ($request, $listaPrecio) {
            foreach ($request->precios as $item) {
                ListaPrecioItem::updateOrCreate(
                    [
                        'lista_precio_id' => $listaPrecio->id,
                        'producto_id' => $item['producto_id'],
                    ],
                    ['precio' => $item['precio']],
                    ['tenant_id' => Auth::user()->business_instance_id]
                );
            }
        });

        $count = count($request->precios);
        return redirect()->route('listas-precio.edit', $listaPrecio)
            ->with('success', "{$count} precios actualizados en la lista.");
    }

    public function quitarProducto(ListaPrecio $listaPrecio, ListaPrecioItem $item)
    {
        if ($item->lista_precio_id !== $listaPrecio->id) {
            return back()->with('error', 'El item no pertenece a esta lista.');
        }
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
}
