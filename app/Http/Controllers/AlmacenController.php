<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\AlmacenMovimiento;
use App\Models\Producto;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\AlmacenMovimientosExport;

class AlmacenController extends Controller
{
    /* =======================
     |  RESOURCE: ALMACENES
     =======================*/

    public function index()
    {
        $query = Almacen::with('sucursal');
        $isAdmin = Auth::user()?->hasRole('admin');
        $sucursalId = session('sucursal_id');
        if (!$isAdmin && $sucursalId) {
            $query->where('sucursal_id', $sucursalId);
        }
        $almacenes = $query->latest()->paginate(10);
        $sucursales = Sucursal::orderBy('nombre')->get();
        return view('almacenes.index', compact('almacenes', 'sucursales'));
    }

    public function create()
    {
        $sucursales = Sucursal::orderBy('nombre')->get();
        return view('almacenes.create', compact('sucursales'));
    }

    public function store(Request $request)
    {
        $sucursalId = $request->sucursal_id ?? session('sucursal_id');
        $data = $request->validate([
            'nombre'      => 'required|string|max:255|unique:almacenes,nombre,NULL,id,sucursal_id,' . ($sucursalId ?? 'NULL'),
            'ubicacion'   => 'nullable|string|max:255',
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ]);

        Almacen::create($data);

        return redirect()->route('almacenes.index')
            ->with('success', 'Almacén creado correctamente.');
    }

    /** 🔥 show() EXISTE (aunque no se use mucho) */
    public function show(Almacen $almacen)
    {
        return view('almacenes.show', compact('almacen'));
    }

    public function edit(Almacen $almacen)
    {
        $sucursales = Sucursal::orderBy('nombre')->get();
        return view('almacenes.edit', compact('almacen', 'sucursales'));
    }

    public function update(Request $request, Almacen $almacen)
    {
        $sucursalId = $request->sucursal_id ?? session('sucursal_id');
        $data = $request->validate([
            'nombre'      => 'required|string|max:255|unique:almacenes,nombre,' . $almacen->id . ',id,sucursal_id,' . ($sucursalId ?? 'NULL'),
            'ubicacion'   => 'nullable|string|max:255',
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ]);

        $almacen->update($data);

        return redirect()->route('almacenes.index')
            ->with('success', 'Almacén actualizado correctamente.');
    }

    public function destroy(Almacen $almacen)
    {
        $almacen->delete();

        return redirect()->route('almacenes.index')
            ->with('success', 'Almacén eliminado correctamente.');
    }

    /* =======================
     |  MOVIMIENTOS
     =======================*/

    public function movimientos(Request $request)
    {
        $query = AlmacenMovimiento::with(['producto', 'almacen', 'user']);

        $isAdmin = Auth::user()?->hasRole('admin');
        $sucursalId = session('sucursal_id');
        if (!$isAdmin && $sucursalId) {
            $query->whereHas('almacen', fn($q) => $q->where('sucursal_id', $sucursalId));
        }

        if ($request->filled('producto')) {
            $query->whereHas('producto', fn ($q) =>
                $q->where('nombre', 'like', "%{$request->producto}%")
            );
        }

        if ($request->filled('almacen')) {
            $query->where('almacen_id', $request->almacen);
        }

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $movimientos = $query->latest()->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('almacenes._movimientos-table', compact('movimientos'))->render(),
                'pagination' => $movimientos->lastPage() > 1
                    ? $movimientos->withQueryString()->links()->toHtml()
                    : '',
            ]);
        }

        return view('almacenes.movimientos', [
            'movimientos' => $movimientos,
            'almacenes'   => $this->almacenesSegunSucursal(),
            'productos'   => Producto::all(),
        ]);
    }

    public function createMovimiento()
    {
        return view('almacenes.movimientos-create', [
            'almacenes' => $this->almacenesSegunSucursal(),
            'productos' => Producto::all(),
        ]);
    }

    private function almacenesSegunSucursal()
    {
        $query = Almacen::orderBy('nombre');
        $isAdmin = Auth::user()?->hasRole('admin');
        $sucursalId = session('sucursal_id');
        if (!$isAdmin && $sucursalId) {
            $query->where('sucursal_id', $sucursalId);
        }
        return $query->get();
    }

    public function storeMovimiento(Request $request)
    {
        $data = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'almacen_id'  => 'required|exists:almacenes,id',
            'tipo'        => 'required|in:entrada,salida',
            'cantidad'    => 'required|integer|min:1',
            'nota'        => 'nullable|string|max:255',
        ]);

        $isAdmin = Auth::user()?->hasRole('admin');
        $almacen = Almacen::findOrFail($data['almacen_id']);

        if (!$isAdmin) {
            $sucursalId = session('sucursal_id');
            if (!$sucursalId || $almacen->sucursal_id !== (int)$sucursalId) {
                return redirect()->back()
                    ->withErrors(['error' => 'Solo puedes gestionar movimientos en almacenes de tu sucursal.'])
                    ->withInput();
            }
        }

        if ($data['tipo'] === 'salida') {
            $stockActual = AlmacenMovimiento::where('producto_id', $data['producto_id'])
                ->where('almacen_id', $data['almacen_id'])
                ->selectRaw('GREATEST(SUM(CASE WHEN tipo="entrada" THEN cantidad ELSE -cantidad END), 0) as stock')
                ->value('stock');

            if ($stockActual < $data['cantidad']) {
                return redirect()->back()
                    ->withErrors(['error' => "Stock insuficiente en este almacén. Disponible: {$stockActual}, solicitado: {$data['cantidad']}."])
                    ->withInput();
            }
        }

        DB::beginTransaction();

        try {
            $producto = Producto::findOrFail($data['producto_id']);

            AlmacenMovimiento::create([
                'producto_id' => $data['producto_id'],
                'almacen_id'  => $data['almacen_id'],
                'user_id'     => Auth::id(),
                'tipo'        => $data['tipo'],
                'cantidad'    => $data['cantidad'],
                'nota'        => $data['nota'],
            ]);

            $data['tipo'] === 'entrada'
                ? $producto->increment('stock', $data['cantidad'])
                : $producto->decrement('stock', $data['cantidad']);

            DB::commit();

            return redirect()->route('almacenes.movimientos')
                ->with('success', 'Movimiento registrado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /* =======================
     |  EXPORTS
     =======================*/

    public function exportMovimientosPdf()
    {
        $movimientos = AlmacenMovimiento::with(['producto','almacen','user'])->get();
        return Pdf::loadView('almacenes.movimientos-pdf', compact('movimientos'))
            ->download('movimientos_almacen.pdf');
    }

    public function inventarioAlmacen(Request $request)
    {
        $user = Auth::user();
        $sucursalId = session('sucursal_id');

        $almacenes = Almacen::when(
            !$user?->hasRole('admin') && $sucursalId,
            fn($q) => $q->where('sucursal_id', $sucursalId)
        )->orderBy('nombre')->get();

        $almacenId = $request->almacen_id;
        $buscar = $request->buscar;

        $stocks = AlmacenMovimiento::query()
            ->selectRaw('producto_id, almacen_id, GREATEST(SUM(CASE WHEN tipo="entrada" THEN cantidad ELSE -cantidad END), 0) as stock')
            ->groupBy('producto_id', 'almacen_id')
            ->get()
            ->groupBy('almacen_id');

        $productos = Producto::orderBy('nombre')->get();

        return view('almacenes.inventario', compact('almacenes', 'almacenId', 'buscar', 'stocks', 'productos'));
    }

    public function exportMovimientosExcel(Request $request)
    {
        return Excel::download(
            new AlmacenMovimientosExport($request->all()),
            'movimientos_almacen.xlsx'
        );
    }
}
