<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\SesionCaja;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GastoController extends Controller
{
    public function index(Request $request)
    {
        $query = Gasto::with('user')->latest('fecha_gasto');

        if ($sucursalId = session('sucursal_id')) {
            $query->where('sucursal_id', $sucursalId);
        }

        if ($request->filled('categoria')) {
            $query->ofCategoria($request->categoria);
        }
        if ($request->filled('desde')) {
            $query->whereDate('fecha_gasto', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha_gasto', '<=', $request->hasta);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('descripcion', 'like', "%{$s}%")
                  ->orWhere('notas', 'like', "%{$s}%")
                  ->orWhere('comprobante', 'like', "%{$s}%");
            });
        }

        $gastos = $query->paginate(20);
        $totalGastos = $query->sum('monto');
        $categorias = Gasto::categorias();
        $totalPorCategoria = Gasto::query()
            ->selectRaw('categoria, SUM(monto) as total')
            ->when($request->filled('desde'), fn($q) => $q->whereDate('fecha_gasto', '>=', $request->desde))
            ->when($request->filled('hasta'), fn($q) => $q->whereDate('fecha_gasto', '<=', $request->hasta))
            ->groupBy('categoria')
            ->pluck('total', 'categoria');

        return view('gastos.index', compact('gastos', 'totalGastos', 'categorias', 'totalPorCategoria'));
    }

    public function create()
    {
        $categorias = Gasto::categorias();
        return view('gastos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'descripcion' => 'required|string|max:500',
            'monto' => 'required|numeric|min:0.01',
            'categoria' => 'nullable|string|max:100',
            'notas' => 'nullable|string|max:2000',
            'fecha_gasto' => 'required|date',
            'metodo_pago' => 'nullable|string|max:50',
            'comprobante' => 'nullable|string|max:100',
        ]);

        $data['user_id'] = Auth::id();
        $data['sucursal_id'] = session('sucursal_id');

        $sesionActiva = SesionCaja::where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->latest()
            ->first();
        if ($sesionActiva) {
            $data['caja_id'] = $sesionActiva->caja_id;
            $data['sesion_caja_id'] = $sesionActiva->id;
        }

        Gasto::create($data);

        return redirect()->route('gastos.index')
            ->with('success', 'Gasto registrado correctamente.');
    }

    public function edit(Gasto $gasto)
    {
        $categorias = Gasto::categorias();
        return view('gastos.edit', compact('gasto', 'categorias'));
    }

    public function update(Request $request, Gasto $gasto)
    {
        $data = $request->validate([
            'descripcion' => 'required|string|max:500',
            'monto' => 'required|numeric|min:0.01',
            'categoria' => 'nullable|string|max:100',
            'notas' => 'nullable|string|max:2000',
            'fecha_gasto' => 'required|date',
            'metodo_pago' => 'nullable|string|max:50',
            'comprobante' => 'nullable|string|max:100',
        ]);

        $gasto->update($data);

        return redirect()->route('gastos.index')
            ->with('success', 'Gasto actualizado correctamente.');
    }

    public function destroy(Gasto $gasto)
    {
        $gasto->delete();
        return redirect()->route('gastos.index')
            ->with('success', 'Gasto eliminado correctamente.');
    }

    public function show(Gasto $gasto)
    {
        $gasto->load('user', 'caja');
        return view('gastos.show', compact('gasto'));
    }
}
