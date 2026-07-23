<?php

namespace App\Http\Controllers;

use App\Models\ServicioDomotica;
use App\Models\InstalacionEquipoDomotico;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Tecnico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServicioDomoticaController extends Controller
{
    /**
     * Display a listing of smart home services.
     */
    public function index(Request $request)
    {
        $query = ServicioDomotica::query()
            ->with(['cliente', 'tecnico', 'instalaciones.producto']);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_servicio')) {
            $query->where('tipo_servicio', $request->tipo_servicio);
        }
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        // Búsqueda
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%")
                    ->orWhere('numero_proyecto', 'like', "%{$search}%")
                    ->orWhereHas('cliente', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%")
                            ->orWhere('rnc_cedula', 'like', "%{$search}%");
                    });
            });
        }

        // Soporte DataTables AJAX
        if ($request->ajax() || $request->wantsJson()) {
            $total = $query->copy()->count();
            $services = $query->latest()->paginate(
                request('length', 10),
                ['*'],
                'page',
                request('start', 0)
            );

            $rows = $services->map(function ($service) {
                return [
                    'DT_RowIndex' => $service->id,
                    'numero_proyecto' => $service->numero_proyecto,
                    'titulo' => $service->titulo,
                    'cliente' => $service->cliente ? $service->cliente->nombre : '-',
                    'tipo_servicio' => $service->tipo_servicio_label ?? ucfirst($service->tipo_servicio),
                    'tecnico' => $service->tecnico ? $service->tecnico->nombre : 'Sin asignar',
                    'total' => number_format($service->total ?? 0, 2),
                    'estado' => $service->estado,
                    'estado_label' => $service->estado_label ?? ucfirst($service->estado),
                    'fecha_programada' => $service->fecha_programada ? $service->fecha_programada->format('d/m/Y') : '',
                    'acciones' => $this->getAccionesHtml($service),
                ];
            });

            return response()->json([
                'draw' => (int) request('draw', 1),
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $rows,
            ]);
        }

        $services = $query->latest()->paginate(20)->withQueryString();
        $tiposServicio = [
            'camaras_seguridad' => 'Cámaras de Seguridad',
            'alarmas' => 'Alarmas',
            'control_acceso' => 'Control de Acceso',
            'redes' => 'Redes',
            'automatizacion' => 'Automatización',
            'sonido' => 'Sonido',
            'iluminacion' => 'Iluminación',
            'otro' => 'Otro',
        ];
        $clientes = Cliente::orderBy('nombre')->get();

        return view('domotica.index', compact('services', 'tiposServicio', 'clientes'));
    }

    /**
     * Show the form for creating a new smart home service.
     */
    public function create(Request $request)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $tecnicos = Tecnico::activos()->orderBy('nombre')->get();
        $productos = Producto::activos()->orderBy('nombre')->get();

        // Si viene cliente_id por query, preseleccionarlo
        $clientePreselect = $request->integer('cliente_id');

        return view('domotica.create', compact('clientes', 'tecnicos', 'productos', 'clientePreselect'));
    }

    /**
     * Store a newly created smart home service.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id'              => 'required|exists:clientes,id',
            'titulo'                  => 'required|string|max:255',
            'descripcion'             => 'nullable|string|max:2000',
            'tipo_servicio'           => 'required|in:camaras_seguridad,alarmas,control_acceso,redes,automatizacion,sonido,iluminacion,otro',
            'direccion_instalacion'   => 'nullable|string|max:500',
            'tecnico_id'              => 'nullable|exists:tecnicos,id',
            'presupuesto'             => 'nullable|numeric|min:0',
            'descuento'               => 'nullable|numeric|min:0',
            'fecha_programada'        => 'nullable|date',
            'notas'                   => 'nullable|string|max:2000',
        ]);

        // Generar número de proyecto
        $year = date('Y');
        $ultimo = ServicioDomotica::where('numero_proyecto', 'like', "SD-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($ultimo) {
            $num = (int) substr($ultimo->numero_proyecto, -6) + 1;
        } else {
            $num = 1;
        }

        $data['numero_proyecto'] = sprintf('SD-%s-%06d', $year, $num);
        $data['equipo_asignado_id'] = $data['tecnico_id'] ?? null;
        $data['estado'] = 'pendiente';

        try {
            $servicio = ServicioDomotica::create($data);

            if (isset($data['presupuesto']) && $data['presupuesto'] > 0) {
                $servicio->calcularTotales();
            }

            return redirect()->route('domotica.show', $servicio)
                ->with('success', 'Servicio de domótica creado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear el servicio: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified smart home service.
     */
    public function show(ServicioDomotica $servicio)
    {
        $servicio->load(['cliente', 'tecnico', 'instalaciones.producto']);
        return view('domotica.show', compact('servicio'));
    }

    /**
     * Show the form for editing the specified smart home service.
     */
    public function edit(ServicioDomotica $servicio)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $tecnicos = Tecnico::activos()->orderBy('nombre')->get();
        $productos = Producto::activos()->orderBy('nombre')->get();

        return view('domotica.edit', compact('servicio', 'clientes', 'tecnicos', 'productos'));
    }

    /**
     * Update the specified smart home service.
     */
    public function update(Request $request, ServicioDomotica $servicio)
    {
        $data = $request->validate([
            'cliente_id'              => 'required|exists:clientes,id',
            'titulo'                  => 'required|string|max:255',
            'descripcion'             => 'nullable|string|max:2000',
            'tipo_servicio'           => 'required|in:camaras_seguridad,alarmas,control_acceso,redes,automatizacion,sonido,iluminacion,otro',
            'direccion_instalacion'   => 'nullable|string|max:500',
            'tecnico_id'              => 'nullable|exists:tecnicos,id',
            'presupuesto'             => 'nullable|numeric|min:0',
            'descuento'               => 'nullable|numeric|min:0',
            'fecha_programada'        => 'nullable|date',
            'notas'                   => 'nullable|string|max:2000',
        ]);

        $data['equipo_asignado_id'] = $data['tecnico_id'] ?? null;

        try {
            $servicio->update($data);

            if (isset($data['presupuesto']) && $data['presupuesto'] > 0) {
                $servicio->calcularTotales();
            }

            return redirect()->route('domotica.show', $servicio)
                ->with('success', 'Servicio de domótica actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar el servicio: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar el estado de un servicio de domótica.
     */
    public function cambiarEstado(Request $request, ServicioDomotica $servicio)
    {
        $data = $request->validate([
            'estado' => 'required|in:pendiente,programado,en_curso,completado,cancelado',
        ]);

        try {
            $servicio->update(['estado' => $data['estado']]);

            if ($data['estado'] === 'completado') {
                $servicio->update(['fecha_completada' => now()]);
            }

            return back()->with('success', "Estado cambiado a '{$servicio->estado_label}'.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    /**
     * Marcar un servicio como completado.
     */
    public function completar(ServicioDomotica $servicio)
    {
        try {
            $servicio->update([
                'estado' => 'completado',
                'fecha_completada' => now(),
            ]);

            return redirect()->route('domotica.show', $servicio)
                ->with('success', 'Servicio marcado como completado.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al completar el servicio: ' . $e->getMessage());
        }
    }

    /**
     * Agregar un equipo/equipment a la instalación.
     */
    public function agregarEquipo(Request $request, ServicioDomotica $servicio)
    {
        $data = $request->validate([
            'producto_id'             => 'required|exists:productos,id',
            'cantidad'                => 'required|integer|min:1',
            'precio_unitario'         => 'required|numeric|min:0',
            'ubicacion_instalacion'   => 'nullable|string|max:255',
            'observaciones'           => 'nullable|string|max:500',
        ]);

        try {
            InstalacionEquipoDomotico::create(array_merge($data, [
                'servicio_domotica_id' => $servicio->id,
                'estado' => 'pendiente',
            ]));

            return back()->with('success', 'Equipo agregado a la instalación correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al agregar el equipo: ' . $e->getMessage());
        }
    }

    /**
     * Quitar un equipo de la instalación.
     */
    public function quitarEquipo(Request $request, InstalacionEquipoDomotico $instalacion)
    {
        try {
            $instalacion->delete();

            return back()->with('success', 'Equipo retirado de la instalación correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al retirar el equipo: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un servicio de domótica.
     */
    public function destroy(ServicioDomotica $servicio)
    {
        // Solo eliminar si no está completado ni facturado
        if (in_array($servicio->estado, ['completado', 'cancelado'])) {
            // Permitir eliminación solo si no hay factura asociada
            try {
                $servicio->delete();
                return redirect()->route('domotica.index')
                    ->with('success', 'Servicio de domótica eliminado.');
            } catch (\Exception $e) {
                return back()->with('error', 'Error al eliminar el servicio: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'No se puede eliminar un servicio en curso. Complete o cancele primero.');
    }

    /**
     * Endpoint AJAX para estadísticas de servicios de domótica.
     */
    public function getEstadisticas()
    {
        $stats = [
            'total'          => ServicioDomotica::count(),
            'pendientes'     => ServicioDomotica::pendientes()->count(),
            'en_curso'       => ServicioDomotica::where('estado', 'en_curso')->count(),
            'completados'    => ServicioDomotica::where('estado', 'completado')->count(),
            'cancelados'     => ServicioDomotica::where('estado', 'cancelado')->count(),
            'ingresos_mes'   => ServicioDomotica::whereMonth('created_at', now()->month)
                ->sum('total'),
            'presupuesto_total' => ServicioDomotica::sum('presupuesto'),
        ];

        return response()->json($stats);
    }

    /**
     * Generar HTML de acciones para DataTables.
     */
    private function getAccionesHtml(ServicioDomotica $servicio): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('domotica.show', $servicio) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';
        $html .= '<a href="' . route('domotica.edit', $servicio) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';

        if (!in_array($servicio->estado, ['completado', 'cancelado'])) {
            $html .= '<form action="' . route('domotica.destroy', $servicio) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar este servicio?\');">';
            $html .= '@csrf @method("DELETE")';
            $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
            $html .= '</form>';
        }

        $html .= '</div>';
        return $html;
    }
}
