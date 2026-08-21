<?php

namespace App\Http\Controllers;

use App\Models\Garantia;
use App\Models\Equipo;
use App\Models\OrdenReparacion;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GarantiaController extends Controller
{
    /**
     * Display a listing of warranties.
     */
    public function index(Request $request)
    {
        $garantias = $this->buildQuery($request)->latest()->paginate(20)->withQueryString();
        $tiposGarantia = [
            'reparacion' => 'Reparación',
            'pieza' => 'Pieza',
            'servicio' => 'Servicio',
            'extendida' => 'Extendida',
        ];

        return view('garantias.index', compact('garantias', 'tiposGarantia'));
    }

    public function indexAjax(Request $request)
    {
        try {
            $query = Garantia::query();

            // Apply filters
            if ($request->input('tipo')) {
                $query->where('garantias.tipo', $request->input('tipo'));
            }
            if ($request->input('estado')) {
                $query->where('garantias.estado', $request->input('estado'));
            }
            if ($request->input('vigencia')) {
                switch ($request->input('vigencia')) {
                    case 'vigentes':
                        $query->where('garantias.fecha_fin', '>=', today())->where('garantias.estado', 'vigente');
                        break;
                    case 'por_vencer':
                        $query->where('garantias.estado', 'vigente')
                            ->where('garantias.fecha_fin', '<=', now()->addDays(30))
                            ->where('garantias.fecha_fin', '>=', today());
                        break;
                    case 'expiradas':
                        $query->where('garantias.fecha_fin', '<', today())
                            ->where('garantias.estado', 'vigente');
                        break;
                }
            }

            $search = $this->dtSearch($request);
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('garantias.cobertura', 'like', "%{$search}%")
                        ->orWhereHas('equipo', function ($q2) use ($search) {
                            $q2->where('serial_imei', 'like', "%{$search}%")
                               ->orWhere('modelo', 'like', "%{$search}%");
                        })
                        ->orWhereHas('ordenReparacion', function ($q2) use ($search) {
                            $q2->where('numero_orden', 'like', "%{$search}%");
                        });
                });
            }

            $total = $query->count();

            // Ordering
            $orderColIdx = (int) $request->input('columns.0.data', 0);
            $orderColMapping = [
                0 => 'garantias.tipo',
                1 => 'garantias.id',
                2 => 'garantias.id',
                3 => 'garantias.cobertura',
                4 => 'garantias.fecha_inicio',
                5 => 'garantias.fecha_fin',
                6 => 'garantias.fecha_fin',
                7 => 'garantias.estado',
            ];
            $orderCol = $orderColMapping[$orderColIdx] ?? 'garantias.id';
            $orderDir = $request->input('order.0.dir', 'desc');
            $query->orderBy($orderCol, $orderDir);

            // Pagination
            $skip = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            if ($length > 0) {
                $query->skip($skip)->take($length);
            }

            $garantias = $query->with(['equipo', 'ordenReparacion.cliente'])->get();

            $rows = $garantias->map(function ($garantia) {
                $diasRestantes = $garantia->dias_restantes;
                if (!$garantia->esta_vigente) {
                    $badgeColor = 'danger';
                } elseif ($diasRestantes <= 7) {
                    $badgeColor = 'warning';
                } elseif ($diasRestantes <= 30) {
                    $badgeColor = 'info';
                } else {
                    $badgeColor = 'success';
                }

                return [
                    'DT_RowIndex' => $garantia->id,
                    'tipo' => $garantia->tipo_label ?? ucfirst($garantia->tipo),
                    'equipo_serial' => $garantia->equipo ? $garantia->equipo->serial_imei : '-',
                    'equipo_modelo' => $garantia->equipo ? $garantia->equipo->modelo : '-',
                    'cobertura' => $garantia->cobertura,
                    'fecha_inicio' => $garantia->fecha_inicio ? $garantia->fecha_inicio->format('d/m/Y') : '',
                    'fecha_fin' => $garantia->fecha_fin ? $garantia->fecha_fin->format('d/m/Y') : '',
                    'dias_restantes' => $diasRestantes,
                    'estado' => $garantia->estado,
                    'estado_label' => $garantia->estado_label ?? ucfirst($garantia->estado),
                    'badge_color' => $badgeColor,
                    'acciones' => $this->getAccionesHtml($garantia),
                ];
            });

            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $rows,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function buildQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        return Garantia::query()
            ->with(['equipo', 'ordenReparacion.cliente'])
            ->when($request->filled('tipo'), fn($q) => $q->where('tipo', $request->tipo))
            ->when($request->filled('estado'), fn($q) => $q->where('estado', $request->estado))
            ->when($request->filled('vigencia'), function ($q) use ($request) {
                match ($request->vigencia) {
                    'vigentes' => $q->vigentes(),
                    'por_vencer' => $q->where('estado', 'vigente')->where('fecha_fin', '<=', now()->addDays(30))->where('fecha_fin', '>=', today()),
                    'expiradas' => $q->where('fecha_fin', '<', today())->where('estado', 'vigente'),
                };
            })
            ->when($search = $this->dtSearch($request), function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('cobertura', 'like', "%{$search}%")
                        ->orWhereHas('equipo', fn($q3) => $q3->where('serial_imei', 'like', "%{$search}%")->orWhere('modelo', 'like', "%{$search}%"))
                        ->orWhereHas('ordenReparacion', fn($q4) => $q4->where('numero_orden', 'like', "%{$search}%"));
                });
            });
    }

    /**
     * Show the form for creating a new warranty.
     */
    public function create()
    {
        $equipos = Equipo::where('estado', '!=', 'vendido')
            ->orderBy('serial_imei')
            ->get();

        $ordenes = OrdenReparacion::where('estado', 'entregado')
            ->orderByDesc('fecha_entrega_real')
            ->limit(50)
            ->get();

        return view('garantias.create', compact('equipos', 'ordenes'));
    }

    /**
     * Store a newly created warranty.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'equipo_id'              => 'nullable|exists:equipos,id',
            'orden_reparacion_id'    => 'nullable|exists:ordenes_reparacion,id',
            'tipo'                   => 'required|in:reparacion,pieza,servicio,extendida',
            'fecha_inicio'           => 'required|date',
            'fecha_fin'              => 'required|date|after_or_equal:fecha_inicio',
            'cobertura'              => 'required|numeric|min:0',
            'terminos_condiciones'   => 'nullable|string|max:2000',
        ]);

        try {
            $garantia = Garantia::create(array_merge($data, [
                'estado' => 'vigente',
            ]));

            return redirect()->route('garantias.show', $garantia)
                ->with('success', 'Garantía registrada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al registrar la garantía: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified warranty.
     */
    public function show(Garantia $garantia)
    {
        $garantia->load(['equipo', 'ordenReparacion.cliente']);
        return view('garantias.show', compact('garantia'));
    }

    /**
     * Show the form for editing the specified warranty.
     */
    public function edit(Garantia $garantia)
    {
        $equipos = Equipo::orderBy('serial_imei')->get();
        $ordenes = OrdenReparacion::orderByDesc('created_at')->limit(50)->get();

        return view('garantias.edit', compact('garantia', 'equipos', 'ordenes'));
    }

    /**
     * Update the specified warranty.
     */
    public function update(Request $request, Garantia $garantia)
    {
        $data = $request->validate([
            'equipo_id'              => 'nullable|exists:equipos,id',
            'orden_reparacion_id'    => 'nullable|exists:ordenes_reparacion,id',
            'tipo'                   => 'required|in:reparacion,pieza,servicio,extendida',
            'fecha_inicio'           => 'required|date',
            'fecha_fin'              => 'required|date|after_or_equal:fecha_inicio',
            'cobertura'              => 'required|numeric|min:0',
            'terminos_condiciones'   => 'nullable|string|max:2000',
        ]);

        try {
            $garantia->update($data);

            return redirect()->route('garantias.show', $garantia)
                ->with('success', 'Garantía actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar la garantía: ' . $e->getMessage());
        }
    }

    /**
     * Extender una garantía existente.
     */
    public function extender(Request $request, Garantia $garantia)
    {
        $data = $request->validate([
            'meses_adicionales' => 'required|integer|min:1|max:60',
            'motivo'            => 'nullable|string|max:500',
        ]);

        try {
            $nuevaFechaFin = $garantia->fecha_fin->addMonths($data['meses_adicionales']);

            $garantia->update([
                'fecha_fin' => $nuevaFechaFin,
                'terminos_condiciones' => ($garantia->terminos_condiciones ?? '')
                    . "\n\nEXTENSIÓN: {$data['meses_adicionales']} meses adicionales. Motivo: " . ($data['motivo'] ?? 'Sin especificar'),
            ]);

            return redirect()->route('garantias.show', $garantia)
                ->with('success', "Garantía extendida {$data['meses_adicionales']} meses. Nueva fecha de vencimiento: {$nuevaFechaFin->format('d/m/Y')}");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al extender la garantía: ' . $e->getMessage());
        }
    }

    /**
     * Procesar un reclamo de garantía.
     */
    public function procesarReclamo(Request $request, Garantia $garantia)
    {
        $data = $request->validate([
            'descripcion_reclamo' => 'required|string|min:10',
            'accion_tomada'       => 'nullable|string|max:1000',
        ]);

        // Verificar que la garantía esté vigente
        if (!$garantia->esta_vigente) {
            return back()->with('error', 'Esta garantía ya no está vigente.');
        }

        try {
            DB::beginTransaction();

            $garantia->update([
                'estado' => 'reclamada',
                'terminos_condiciones' => ($garantia->terminos_condiciones ?? '')
                    . "\n\nRECLAMO: {$data['descripcion_reclamo']}",
            ]);

            // Si hay orden de reparación asociada, actualizar nota
            if ($garantia->orden_reparacion_id) {
                $orden = $garantia->ordenReparacion;
                if ($orden) {
                    $orden->notas = ($orden->notas ?? '') . ' [GARANTÍA EN RECLAMO]';
                    $orden->save();
                }
            }

            DB::commit();

            return redirect()->route('garantias.show', $garantia)
                ->with('success', 'Reclamo registrado. La garantía pasa a estado "En Reclamo".');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al procesar el reclamo: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar una garantía.
     */
    public function destroy(Garantia $garantia)
    {
        // No permitir eliminar garantías con reclamos activos
        if ($garantia->estado === 'reclamada') {
            return back()->with('error', 'No se puede eliminar una garantía con reclamo activo.');
        }

        try {
            $garantia->delete();
            return redirect()->route('garantias.index')
                ->with('success', 'Garantía eliminada.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la garantía: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint AJAX: contar garantías vigentes.
     */
    public function getVigentes()
    {
        $count = Garantia::vigentes()->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Endpoint AJAX: garantías por vencer en los próximos 30 días.
     */
    public function getPorVencer()
    {
        $porVencer = Garantia::where('estado', 'vigente')
            ->where('fecha_fin', '<=', now()->addDays(30))
            ->where('fecha_fin', '>=', today())
            ->with(['equipo', 'ordenReparacion.cliente'])
            ->orderBy('fecha_fin')
            ->get()
            ->map(function ($garantia) {
                return [
                    'id' => $garantia->id,
                    'equipo_serial' => $garantia->equipo ? $garantia->equipo->serial_imei : '-',
                    'equipo_modelo' => $garantia->equipo ? $garantia->equipo->modelo : '-',
                    'fecha_fin' => $garantia->fecha_fin->format('d/m/Y'),
                    'dias_restantes' => $garantia->dias_restantes,
                    'cliente' => $garantia->ordenReparacion?->cliente?->nombre ?? 'N/A',
                    'numero_orden' => $garantia->ordenReparacion?->numero_orden ?? 'N/A',
                ];
            });

        return response()->json($porVencer);
    }

    /**
     * Generar HTML de acciones para DataTables.
     */
    private function getAccionesHtml(Garantia $garantia): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('garantias.show', $garantia) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';
        $html .= '<a href="' . route('garantias.edit', $garantia) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';

        if ($garantia->esta_vigente && $garantia->estado === 'vigente') {
            $html .= '<a href="' . route('garantias.extender', $garantia) . '" class="btn btn-outline-success" title="Extender"><i class="bi bi-calendar-plus"></i></a>';
            $html .= '<a href="' . route('garantias.reclamo', $garantia) . '" class="btn btn-outline-danger" title="Procesar Reclamo"><i class="bi bi-exclamation-triangle"></i></a>';
        }

        if (!in_array($garantia->estado, ['reclamada', 'cancelada'])) {
            $html .= '<form action="' . route('garantias.destroy', $garantia) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar esta garantía?\');">';
            $html .= csrf_field() . method_field('DELETE');
            $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
            $html .= '</form>';
        }

        $html .= '</div>';
        return $html;
    }
}
