<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\OrdenReparacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipoController extends Controller
{
    /**
     * Display a listing of equipment.
     */
    public function index(Request $request)
    {
        $query = Equipo::query()
            ->with(['producto', 'proveedor'])
            ->select('equipos.*');

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('marca')) {
            $query->where('marca', 'like', "%{$request->marca}%");
        }
        if ($request->filled('disponibilidad')) {
            switch ($request->disponibilidad) {
                case 'disponible':
                    $query->where('estado', 'disponible');
                    break;
                case 'no_disponible':
                    $query->whereNotIn('estado', ['disponible']);
                    break;
            }
        }

        // Búsqueda
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('serial_imei', 'like', "%{$search}%")
                    ->orWhere('serial_esn', 'like', "%{$search}%")
                    ->orWhere('marca', 'like', "%{$search}%")
                    ->orWhere('modelo', 'like', "%{$search}%")
                    ->orWhere('color', 'like', "%{$search}%");
            });
        }

        // Soporte DataTables AJAX
        if ($request->ajax() || $request->wantsJson()) {
            $total = $query->copy()->count();
            $equipos = $query->latest()->paginate(
                request('length', 10),
                ['*'],
                'page',
                request('start', 0)
            );

            $rows = $equipos->map(function ($equipo) {
                $badgeColor = match ($equipo->estado) {
                    'disponible' => 'success',
                    'vendido' => 'primary',
                    'en_reparacion' => 'warning',
                    'dañado' => 'danger',
                    'reservado' => 'info',
                    'mantenimiento' => 'secondary',
                    default => 'dark',
                };

                return [
                    'DT_RowIndex' => $equipo->id,
                    'serial_imei' => $equipo->serial_imei,
                    'marca' => $equipo->marca,
                    'modelo' => $equipo->modelo,
                    'color' => $equipo->color ?? '-',
                    'almacenamiento' => ($equipo->almacenamiento_gb ?? '-') . ' GB',
                    'precio_venta' => number_format($equipo->precio_venta ?? 0, 2),
                    'garantia_tipo' => $equipo->garantia_tipo ?? '-',
                    'garantia_activa' => $equipo->garantia_activa ? 'Sí' : 'No',
                    'estado' => $equipo->estado,
                    'estado_label' => $equipo->estado_label ?? ucfirst($equipo->estado),
                    'badge_color' => $badgeColor,
                    'acciones' => $this->getAccionesHtml($equipo),
                ];
            });

            return response()->json([
                'draw' => (int) request('draw', 1),
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $rows,
            ]);
        }

        $equipos = $query->latest()->paginate(20)->withQueryString();
        $estados = [
            'disponible' => 'Disponible',
            'vendido' => 'Vendido',
            'en_reparacion' => 'En Reparación',
            'dañado' => 'Dañado',
            'reservado' => 'Reservado',
            'mantenimiento' => 'Mantenimiento',
        ];
        $marcas = Equipo::whereNotNull('marca')
            ->where('marca', '!=', '')
            ->distinct()
            ->orderBy('marca')
            ->pluck('marca');

        return view('equipos.index', compact('equipos', 'estados', 'marcas'));
    }

    /**
     * Show the form for creating a new equipment.
     */
    public function create()
    {
        $categorias = Producto::select('categoria_id')->distinct()->pluck('categoria_id');
        $categorias = \App\Models\Categoria::whereIn('id', $categorias)->orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre')->get();

        return view('equipos.create', compact('categorias', 'proveedores'));
    }

    /**
     * Store a newly registered equipment.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'serial_imei'       => 'required|string|max:50|unique:equipos,serial_imei',
            'serial_esn'        => 'nullable|string|max:50',
            'marca'             => 'required|string|max:100',
            'modelo'            => 'required|string|max:200',
            'almacenamiento_gb' => 'nullable|integer|min:0',
            'color'             => 'nullable|string|max:50',
            'estado'            => 'required|in:disponible,vendido,en_reparacion,dañado,reservado,mantenimiento',
            'precio_compra'     => 'nullable|numeric|min:0',
            'precio_venta'      => 'required|numeric|min:0',
            'comprado_a_proveedor_id' => 'nullable|exists:proveedores,id',
            'fecha_compra'      => 'nullable|date',
            'factura_compra'    => 'nullable|string|max:100',
            'garantia_desde'    => 'nullable|date',
            'garantia_hasta'    => 'nullable|date|after_or_equal:garantia_desde',
            'garantia_tipo'     => 'nullable|in:fabrica,extendida',
            'bloqueado_icloud'  => 'boolean',
            'bloqueado_fr'      => 'boolean',
            'observaciones'     => 'nullable|string|max:2000',
        ]);

        $data['bloqueado_icloud'] = $request->has('bloqueado_icloud') ? true : false;
        $data['bloqueado_fr'] = $request->has('bloqueado_fr') ? true : false;

        try {
            $equipo = Equipo::create($data);

            return redirect()->route('equipos.show', $equipo)
                ->with('success', 'Equipo registrado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al registrar equipo: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified equipment.
     */
    public function show(Equipo $equipo)
    {
        $equipo->load(['producto', 'proveedor', 'ordenesReparacion', 'garantias']);
        return view('equipos.show', compact('equipo'));
    }

    /**
     * Show the form for editing the specified equipment.
     */
    public function edit(Equipo $equipo)
    {
        $categorias = \App\Models\Categoria::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre')->get();

        return view('equipos.edit', compact('equipo', 'categorias', 'proveedores'));
    }

    /**
     * Update the specified equipment.
     */
    public function update(Request $request, Equipo $equipo)
    {
        $data = $request->validate([
            'serial_imei'       => 'required|string|max:50|unique:equipos,serial_imei,' . $equipo->id,
            'serial_esn'        => 'nullable|string|max:50',
            'marca'             => 'required|string|max:100',
            'modelo'            => 'required|string|max:200',
            'almacenamiento_gb' => 'nullable|integer|min:0',
            'color'             => 'nullable|string|max:50',
            'estado'            => 'required|in:disponible,vendido,en_reparacion,dañado,reservado,mantenimiento',
            'precio_compra'     => 'nullable|numeric|min:0',
            'precio_venta'      => 'required|numeric|min:0',
            'comprado_a_proveedor_id' => 'nullable|exists:proveedores,id',
            'fecha_compra'      => 'nullable|date',
            'factura_compra'    => 'nullable|string|max:100',
            'garantia_desde'    => 'nullable|date',
            'garantia_hasta'    => 'nullable|date|after_or_equal:garantia_desde',
            'garantia_tipo'     => 'nullable|in:fabrica,extendida',
            'bloqueado_icloud'  => 'boolean',
            'bloqueado_fr'      => 'boolean',
            'observaciones'     => 'nullable|string|max:2000',
        ]);

        $data['bloqueado_icloud'] = $request->has('bloqueado_icloud') ? true : false;
        $data['bloqueado_fr'] = $request->has('bloqueado_fr') ? true : false;

        try {
            $equipo->update($data);

            return redirect()->route('equipos.show', $equipo)
                ->with('success', 'Equipo actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar equipo: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified equipment.
     */
    public function destroy(Equipo $equipo)
    {
        // Solo eliminar si no está en órdenes de reparación activas
        $ordenesActivas = $equipo->ordenesReparacion()
            ->whereNotIn('estado', ['entregado', 'cancelado'])
            ->count();

        if ($ordenesActivas > 0) {
            return back()->with('error', 'No se puede eliminar: el equipo tiene órdenes de reparación activas.');
        }

        try {
            $equipo->delete();
            return redirect()->route('equipos.index')
                ->with('success', 'Equipo eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar equipo: ' . $e->getMessage());
        }
    }

    /**
     * Toggle equipment reservation status.
     */
    public function toggleReservar(Equipo $equipo)
    {
        try {
            if ($equipo->estado === 'reservado') {
                $equipo->update(['estado' => 'disponible']);
                $message = 'Reserva cancelada. Equipo disponible.';
            } elseif ($equipo->estado === 'disponible') {
                $equipo->update(['estado' => 'reservado']);
                $message = 'Equipo reservado correctamente.';
            } else {
                return back()->with('error', 'Solo se pueden reservar equipos disponibles.');
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar estado de reserva: ' . $e->getMessage());
        }
    }

    /**
     * AJAX search by IMEI for POS-like quick lookup.
     */
    public function buscarPorImei(Request $request)
    {
        $q = $request->get('q', '');

        if (strlen($q) < 4) {
            return response()->json([]);
        }

        $equipos = Equipo::where(function ($query) use ($q) {
                $query->where('serial_imei', 'like', "%{$q}%")
                    ->orWhere('serial_esn', 'like', "%{$q}%");
            })
            ->select('id', 'serial_imei', 'serial_esn', 'marca', 'modelo', 'estado', 'precio_venta')
            ->limit(10)
            ->get();

        return response()->json($equipos);
    }

    /**
     * Export equipment list to Excel.
     */
    public function exportarExcel()
    {
        $equipos = Equipo::with(['producto', 'proveedor'])
            ->select('equipos.*')
            ->get();

        // Using Laravel Excel or simple CSV export
        $filename = 'equipos_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Headers
        fputcsv($output, [
            'ID', 'Serial/IMEI', 'ESN', 'Marca', 'Modelo', 'Color',
            'Almacenamiento', 'Estado', 'Precio Compra', 'Precio Venta',
            'Garantía Tipo', 'Garantía Desde', 'Garantía Hasta',
            'Bloqueado iCloud', 'Bloqueado FR', 'Observaciones'
        ]);

        foreach ($equipos as $equipo) {
            fputcsv($output, [
                $equipo->id,
                $equipo->serial_imei,
                $equipo->serial_esn ?? '',
                $equipo->marca,
                $equipo->modelo,
                $equipo->color ?? '',
                $equipo->almacenamiento_gb ?? '',
                $equipo->estado_label ?? $equipo->estado,
                number_format($equipo->precio_compra ?? 0, 2),
                number_format($equipo->precio_venta ?? 0, 2),
                $equipo->garantia_tipo ?? '',
                $equipo->garantia_desde?->format('Y-m-d') ?? '',
                $equipo->garantia_hasta?->format('Y-m-d') ?? '',
                $equipo->bloqueado_icloud ? 'Sí' : 'No',
                $equipo->bloqueado_fr ? 'Sí' : 'No',
                $equipo->observaciones ?? '',
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Generate HTML actions for DataTables.
     */
    private function getAccionesHtml(Equipo $equipo): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('equipos.show', $equipo) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';
        $html .= '<a href="' . route('equipos.edit', $equipo) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';

        // Reservar toggle
        if (in_array($equipo->estado, ['disponible', 'reservado'])) {
            $html .= '<a href="' . route('equipos.toggle-reservar', $equipo) . '" class="btn btn-outline-'
                . ($equipo->estado === 'reservado' ? 'secondary' : 'info') . '" title="'
                . ($equipo->estado === 'reservado' ? 'Cancelar Reserva' : 'Reservar') . '">'
                . '<i class="bi bi-' . ($equipo->estado === 'reservado' ? 'bookmark-x' : 'bookmark') . '"></i></a>';
        }

        // Delete (only if no active repair orders)
        $ordenesActivas = $equipo->ordenesReparacion()
            ->whereNotIn('estado', ['entregado', 'cancelado'])
            ->count();

        if ($ordenesActivas === 0) {
            $html .= '<form action="' . route('equipos.destroy', $equipo) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar este equipo?\');">';
            $html .= '@csrf @method("DELETE")';
            $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
            $html .= '</form>';
        }

        $html .= '</div>';
        return $html;
    }
}
