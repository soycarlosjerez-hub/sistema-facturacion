<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\EquipoVenta;
use Illuminate\Http\Request;

class EquipoReporteController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->business_instance_id;

        $desde = $request->input('desde', now()->startOfMonth()->format('Y-m-d'));
        $hasta = $request->input('hasta', now()->format('Y-m-d'));
        $marca = $request->input('marca', '');
        $tipoDispositivo = $request->input('tipo_dispositivo', '');
        $estadoEquipo = $request->input('estado_equipo', 'vendido');

        $query = EquipoVenta::with(['equipo', 'venta'])
            ->whereHas('venta', fn($q) => $q->where('tenant_id', $tenantId))
            ->where('created_at', '>=', $desde)
            ->where('created_at', '<=', $hasta . ' 23:59:59');

        if ($marca) {
            $query->whereHas('equipo', fn($q) => $q->where('marca', 'like', "%{$marca}%"));
        }

        if ($tipoDispositivo) {
            $query->whereHas('equipo', fn($q) => $q->where('tipo_dispositivo', $tipoDispositivo));
        }

        if ($estadoEquipo === 'disponible') {
            $query->whereDoesntHave('venta');
        } elseif ($estadoEquipo === 'vendido') {
            $query->whereHas('venta');
        }

        $totalVendidos = Equipo::where('tenant_id', $tenantId)->where('estado', 'vendido')->count();
        $totalDisponibles = Equipo::where('tenant_id', $tenantId)->where('estado', 'disponible')->count();
        $totalEnReparacion = Equipo::where('tenant_id', $tenantId)->where('estado', 'en_reparacion')->count();
        $totalEquipos = Equipo::where('tenant_id', $tenantId)->count();
        $totalIngresos = EquipoVenta::whereHas('venta', fn($q) => $q->where('tenant_id', $tenantId))
            ->where('created_at', '>=', $desde)
            ->where('created_at', '<=', $hasta . ' 23:59:59')
            ->sum('precio_vendido');

        $equipos = $query->orderByDesc('created_at')->paginate(20);

        $tiposDispositivo = Equipo::where('tenant_id', $tenantId)
            ->distinct()
            ->pluck('tipo_dispositivo')
            ->filter()
            ->toArray();

        $marcas = Equipo::where('tenant_id', $tenantId)
            ->distinct()
            ->pluck('marca')
            ->filter()
            ->toArray();

        return view('reportes.equipos', compact(
            'equipos', 'desde', 'hasta', 'marca', 'tipoDispositivo', 'estadoEquipo',
            'totalVendidos', 'totalDisponibles', 'totalEnReparacion', 'totalEquipos', 'totalIngresos',
            'tiposDispositivo', 'marcas'
        ));
    }

    public function ajaxData(Request $request)
    {
        $tenantId = Auth::user()->business_instance_id;

        $desde = $request->input('desde', now()->startOfMonth()->format('Y-m-d'));
        $hasta = $request->input('hasta', now()->format('Y-m-d'));
        $marca = $request->input('marca', '');
        $tipoDispositivo = $request->input('tipo_dispositivo', '');

        $query = EquipoVenta::with(['equipo', 'venta'])
            ->whereHas('venta', fn($q) => $q->where('tenant_id', $tenantId))
            ->where('created_at', '>=', $desde)
            ->where('created_at', '<=', $hasta . ' 23:59:59');

        if ($marca) {
            $query->whereHas('equipo', fn($q) => $q->where('marca', 'like', "%{$marca}%"));
        }

        if ($tipoDispositivo) {
            $query->whereHas('equipo', fn($q) => $q->where('tipo_dispositivo', $tipoDispositivo));
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('serial_imei', function($row) {
                return $row->equipo->serial_imei ?? '';
            })
            ->addColumn('serial_esn', function($row) {
                return $row->equipo->serial_esn ?? '';
            })
            ->addColumn('marca', function($row) {
                return $row->equipo->marca ?? '';
            })
            ->addColumn('modelo', function($row) {
                return $row->equipo->modelo ?? '';
            })
            ->addColumn('color', function($row) {
                return $row->equipo->color ?? '';
            })
            ->addColumn('tipo_dispositivo', function($row) {
                return $row->equipo->tipo_dispositivo ?? '';
            })
            ->addColumn('cliente', function($row) {
                return $row->venta->cliente->nombre ?? '';
            })
            ->addColumn('ncf', function($row) {
                return $row->venta->ncf ?? '';
            })
            ->addColumn('precio', function($row) {
                return '$' . number_format($row->precio_vendido, 2);
            })
            ->addColumn('fecha', function($row) {
                return $row->created_at->format('d/m/Y H:i');
            })
            ->addColumn('action', function($row) {
                return '<a href="' . route('ventas.show', $row->venta_id) . '" class="btn btn-sm btn-outline-primary" title="Ver venta"><i class="bi bi-eye"></i></a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function exportExcel(Request $request)
    {
        $tenantId = Auth::user()->business_instance_id;

        $desde = $request->input('desde', now()->startOfMonth()->format('Y-m-d'));
        $hasta = $request->input('hasta', now()->format('Y-m-d'));
        $marca = $request->input('marca', '');
        $tipoDispositivo = $request->input('tipo_dispositivo', '');

        $query = EquipoVenta::with(['equipo', 'venta'])
            ->whereHas('venta', fn($q) => $q->where('tenant_id', $tenantId))
            ->where('created_at', '>=', $desde)
            ->where('created_at', '<=', $hasta . ' 23:59:59');

        if ($marca) {
            $query->whereHas('equipo', fn($q) => $q->where('marca', 'like', "%{$marca}%"));
        }

        if ($tipoDispositivo) {
            $query->whereHas('equipo', fn($q) => $q->where('tipo_dispositivo', $tipoDispositivo));
        }

        $datos = $query->orderByDesc('created_at')->get()->map(function($row) {
            return [
                'IMEI' => $row->equipo->serial_imei ?? '',
                'ESN' => $row->equipo->serial_esn ?? '',
                'Marca' => $row->equipo->marca ?? '',
                'Modelo' => $row->equipo->modelo ?? '',
                'Color' => $row->equipo->color ?? '',
                'Tipo' => $row->equipo->tipo_dispositivo ?? '',
                'Cliente' => $row->venta->cliente->nombre ?? '',
                'NCF' => $row->venta->ncf ?? '',
                'Precio' => $row->precio_vendido,
                'Fecha' => $row->created_at->format('d/m/Y H:i'),
            ];
        });

        $filename = 'equipos_' . now()->format('Y-m-d_His') . '.xlsx';
        
        return (new \Maatwebsite\Excel\Excel())->download($datos->values()->toArray(), $filename);
    }

    public function exportPdf(Request $request)
    {
        $tenantId = Auth::user()->business_instance_id;

        $desde = $request->input('desde', now()->startOfMonth()->format('Y-m-d'));
        $hasta = $request->input('hasta', now()->format('Y-m-d'));
        $marca = $request->input('marca', '');
        $tipoDispositivo = $request->input('tipo_dispositivo', '');

        $query = EquipoVenta::with(['equipo', 'venta'])
            ->whereHas('venta', fn($q) => $q->where('tenant_id', $tenantId))
            ->where('created_at', '>=', $desde)
            ->where('created_at', '<=', $hasta . ' 23:59:59');

        if ($marca) {
            $query->whereHas('equipo', fn($q) => $q->where('marca', 'like', "%{$marca}%"));
        }

        if ($tipoDispositivo) {
            $query->whereHas('equipo', fn($q) => $q->where('tipo_dispositivo', $tipoDispositivo));
        }

        $equipos = $query->orderByDesc('created_at')->get();
        $totalIngresos = $equipos->sum('precio_vendido');

        return view('reportes.equipos-pdf', compact('equipos', 'totalIngresos', 'desde', 'hasta'));
    }
}
