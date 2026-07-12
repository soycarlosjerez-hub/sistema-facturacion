<?php

namespace App\Http\Controllers;

use App\Exports\KardexExport;
use App\Models\AlmacenMovimiento;
use App\Models\Producto;
use App\Models\Almacen;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class KardexController extends Controller
{
    public function index(Request $request)
    {
        $productos = Producto::orderBy('nombre')->get();
        $almacenes = Almacen::orderBy('nombre')->get();

        $query = AlmacenMovimiento::with(['producto', 'almacen', 'user'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->filled('almacen_id')) {
            $query->where('almacen_id', $request->almacen_id);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->whereHas('producto', function($pq) use ($buscar) {
                    $pq->where('nombre', 'like', "%{$buscar}%");
                })->orWhere('nota', 'like', "%{$buscar}%")
                  ->orWhere('motivo', 'like', "%{$buscar}%");
            });
        }

        $movimientos = $query->paginate(20);

        return view('kardex.index', compact(
            'movimientos',
            'productos',
            'almacenes'
        ));
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new KardexExport(
                $request->producto_id,
                $request->almacen_id,
                $request->buscar
            ),
            'kardex.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $query = AlmacenMovimiento::with(['producto', 'almacen', 'user'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->filled('almacen_id')) {
            $query->where('almacen_id', $request->almacen_id);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->whereHas('producto', fn($pq) => $pq->where('nombre', 'like', "%{$buscar}%"))
                  ->orWhere('nota', 'like', "%{$buscar}%")
                  ->orWhere('motivo', 'like', "%{$buscar}%");
            });
        }

        $movimientos = $query->get();

        $pdf = Pdf::loadView('kardex.pdf', compact('movimientos'));
        return $pdf->stream('kardex.pdf');
    }
}
