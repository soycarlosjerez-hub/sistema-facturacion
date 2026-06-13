<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Sucursal;
use App\Models\Producto;
use App\Services\AlmacenService;
use App\Exports\AlmacenMovimientosExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AlmacenController extends Controller
{
    public function __construct(
        protected AlmacenService $almacenService
    ) {}

    public function index()
    {
        $almacenes = $this->almacenService->listarAlmacenes();
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

        $this->almacenService->createAlmacen($data);

        return redirect()->route('almacenes.index')
            ->with('success', 'Almacén creado correctamente.');
    }

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

        $this->almacenService->updateAlmacen($almacen, $data);

        return redirect()->route('almacenes.index')
            ->with('success', 'Almacén actualizado correctamente.');
    }

    public function destroy(Almacen $almacen)
    {
        $this->almacenService->deleteAlmacen($almacen);

        return redirect()->route('almacenes.index')
            ->with('success', 'Almacén eliminado correctamente.');
    }

    public function movimientos(Request $request)
    {
        $movimientos = $this->almacenService->listarMovimientos($request->all());

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
            'almacenes'   => $this->almacenService->almacenesSegunSucursal(),
            'productos'   => Producto::all(),
        ]);
    }

    public function createMovimiento()
    {
        return view('almacenes.movimientos-create', [
            'almacenes'  => $this->almacenService->almacenesSegunSucursal(),
            'productos'  => Producto::all(),
            'stocksData' => $this->almacenService->getStocksData(),
        ]);
    }

    public function storeMovimiento(Request $request)
    {
        $data = $this->validateMovimiento($request);
        $result = $this->almacenService->storeMovimiento($data);

        if (!$result['success']) {
            return redirect()->back()
                ->withErrors(['error' => $result['error']])
                ->withInput();
        }

        return redirect()->route('almacenes.movimientos')
            ->with('success', $result['message']);
    }

    public function exportMovimientosPdf()
    {
        $movimientos = \App\Models\AlmacenMovimiento::with(['producto','almacen','user'])->get();
        return Pdf::loadView('almacenes.movimientos-pdf', compact('movimientos'))
            ->download('movimientos_almacen.pdf');
    }

    public function inventarioAlmacen(Request $request)
    {
        $data = $this->almacenService->inventarioAlmacen($request->almacen_id, $request->buscar);
        return view('almacenes.inventario', $data);
    }

    public function exportMovimientosExcel(Request $request)
    {
        return Excel::download(
            new AlmacenMovimientosExport($request->all()),
            'movimientos_almacen.xlsx'
        );
    }

    protected function validateMovimiento(Request $request): array
    {
        if ($request->tipo === 'traslado') {
            return $request->validate([
                'producto_id'         => 'required|exists:productos,id',
                'almacen_origen_id'   => 'required|exists:almacenes,id',
                'almacen_destino_id'  => 'required|exists:almacenes,id|different:almacen_origen_id',
                'cantidad'            => 'required|integer|min:1',
                'nota'                => 'nullable|string|max:255',
            ]);
        }

        return $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'almacen_id'  => 'required|exists:almacenes,id',
            'tipo'        => 'required|in:entrada,salida',
            'cantidad'    => 'required|integer|min:1',
            'nota'        => 'nullable|string|max:255',
        ]);
    }
}
