<?php

namespace App\Http\Controllers;

use App\Models\ServicioDomotica;
use App\Models\InstalacionEquipoDomotico;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Tecnico;
use App\Services\InstallationService;
use App\Http\Requests\StoreServicioDomoticaRequest;
use App\Http\Requests\UpdateServicioDomoticaRequest;
use Illuminate\Http\Request;

class ServicioDomoticaController extends Controller
{
    public function __construct(private InstallationService $installationService) {}

    public function index(Request $request)
    {
        $query = ServicioDomotica::query()
            ->with(['cliente', 'tecnico', 'instalaciones.producto']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_servicio')) {
            $query->where('tipo_servicio', $request->tipo_servicio);
        }
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

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

        if ($request->ajax() || $request->wantsJson()) {
            $total = clone $query;
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
                'recordsTotal' => $total->count(),
                'recordsFiltered' => $total->count(),
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

    public function create(Request $request)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $tecnicos = Tecnico::activos()->orderBy('nombre')->get();
        $productos = Producto::activos()->orderBy('nombre')->get();
        $clientePreselect = $request->integer('cliente_id');

        return view('domotica.create', compact('clientes', 'tecnicos', 'productos', 'clientePreselect'));
    }

    public function store(StoreServicioDomoticaRequest $request)
    {
        try {
            $data = $request->validated();
            $data['equipo_asignado_id'] = $data['tecnico_id'] ?? null;

            $servicio = $this->installationService->crearServicioDomotica($data, auth()->id());

            return redirect()->route('domotica.show', $servicio)
                ->with('success', 'Servicio de domótica creado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear el servicio: ' . $e->getMessage());
        }
    }

    public function show(ServicioDomotica $servicio)
    {
        $servicio->load(['cliente', 'tecnico', 'instalaciones.producto']);
        return view('domotica.show', compact('servicio'));
    }

    public function edit(ServicioDomotica $servicio)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $tecnicos = Tecnico::activos()->orderBy('nombre')->get();
        $productos = Producto::activos()->orderBy('nombre')->get();

        return view('domotica.edit', compact('servicio', 'clientes', 'tecnicos', 'productos'));
    }

    public function update(UpdateServicioDomoticaRequest $request, ServicioDomotica $servicio)
    {
        try {
            $data = $request->validated();
            $data['equipo_asignado_id'] = $data['tecnico_id'] ?? null;

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

    public function cambiarEstado(Request $request, ServicioDomotica $servicio)
    {
        $data = $request->validate([
            'estado' => 'required|in:pendiente,programado,en_curso,completado,cancelado',
        ]);

        try {
            $this->installationService->cambiarEstado($servicio->id, $data['estado']);

            return back()->with('success', "Estado cambiado a '{$servicio->estado_label}'.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function completar(ServicioDomotica $servicio)
    {
        try {
            $this->installationService->completarServicio($servicio->id);

            return redirect()->route('domotica.show', $servicio)
                ->with('success', 'Servicio marcado como completado.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function agregarEquipo(Request $request, ServicioDomotica $servicio)
    {
        $data = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
            'ubicacion_instalacion' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string|max:500',
        ]);

        try {
            $this->installationService->agregarEquipo(
                $servicio->id,
                $data['producto_id'],
                $data['cantidad'],
                $data['ubicacion_instalacion'] ?? ''
            );

            return back()->with('success', 'Equipo agregado a la instalación correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al agregar el equipo: ' . $e->getMessage());
        }
    }

    public function quitarEquipo(InstalacionEquipoDomotico $instalacion)
    {
        try {
            $this->installationService->eliminarEquipo($instalacion->id);

            return back()->with('success', 'Equipo retirado de la instalación correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(ServicioDomotica $servicio)
    {
        if (in_array($servicio->estado, ['completado', 'cancelado'])) {
            try {
                $servicio->delete();
                return redirect()->route('domotica.index')
                    ->with('success', 'Servicio de domótica eliminado.');
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        return back()->with('error', 'No se puede eliminar un servicio en curso. Complete o cancele primero.');
    }

    public function getEstadisticas()
    {
        $stats = [
            'total' => ServicioDomotica::count(),
            'pendientes' => ServicioDomotica::pendientes()->count(),
            'en_curso' => ServicioDomotica::where('estado', 'en_curso')->count(),
            'completados' => ServicioDomotica::where('estado', 'completado')->count(),
            'cancelados' => ServicioDomotica::where('estado', 'cancelado')->count(),
            'ingresos_mes' => ServicioDomotica::whereMonth('created_at', now()->month)->sum('total'),
            'presupuesto_total' => ServicioDomotica::sum('presupuesto'),
        ];

        return response()->json($stats);
    }

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
