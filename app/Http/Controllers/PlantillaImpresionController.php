<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlantillaImpresionRequest;
use App\Http\Requests\UpdatePlantillaImpresionRequest;
use App\Models\PlantillaImpresion;
use App\Models\Sucursal;
use App\Services\PlantillaPdfGenerator;
use App\Services\PlantillaVariablesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlantillaImpresionController extends Controller
{
    public function __construct(
        private PlantillaVariablesService $variablesService
    ) {}

    public function index(Request $request)
    {
        $modulo = $request->input('modulo');
        $formato = $request->input('formato');
        $activo = $request->boolean('activo');
        $default = $request->boolean('default');

        $query = PlantillaImpresion::query()->with(['sucursales:id,nombre']);

        if ($modulo) {
            $query->where('modulo', $modulo);
        }

        if ($formato) {
            $query->where('formato_papel', $formato);
        }

        if ($activo !== null) {
            $query->where('activo', (bool) $activo);
        }

        if ($default) {
            $query->where('es_default', true);
        }

        $query->orderByDesc('es_default')
            ->orderBy('orden')
            ->orderBy('nombre');

        $plantillas = $query->paginate(15);

        $modulos = PlantillaImpresion::MODULOS;
        $formatos = PlantillaImpresion::FORMATOS_PAPEL;

        return view('plantillas.index', compact('plantillas', 'modulos', 'formatos', 'modulo', 'formato', 'activo', 'default'));
    }

    public function indexAjax(Request $request)
    {
        try {
            $search = $request->input('search.value', $request->input('search', ''));
            $modulo = $request->input('modulo');
            $moduloTarget = $request->input('modulo_target');
            $esDefault = $request->input('es_default', $request->input('default'));
            $activo = $request->input('activo');
            $formato = $request->input('formato');

            $query = PlantillaImpresion::query()
                ->with(['sucursales:id,nombre']);

            if ($modulo) {
                $query->where('modulo', $modulo);
            }

            if ($formato) {
                $query->where('formato_papel', $formato);
            }

            if ($moduloTarget) {
                $query->where(function ($q) use ($moduloTarget) {
                    $q->whereNull('modulo_target')
                        ->orWhere('modulo_target', $moduloTarget);
                });
            }

            if ($esDefault !== null && $esDefault !== '') {
                $query->where('es_default', (bool) $esDefault);
            }

            if ($activo !== null && $activo !== '') {
                $query->where('activo', (bool) $activo);
            } else {
                $query->where('activo', true);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                        ->orWhere('codigo', 'like', "%{$search}%");
                });
            }

            $recordsTotal = PlantillaImpresion::count();

            $query->orderByDesc('es_default')
                ->orderBy('orden')
                ->orderBy('nombre');

            $plantillas = $query->get();

            return response()->json([
                'plantillas' => $plantillas->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'codigo' => $p->codigo,
                        'nombre' => $p->nombre,
                        'modulo' => $p->modulo,
                        'formato_papel' => $p->formato_papel,
                        'es_default' => (bool) $p->es_default,
                        'activo' => (bool) $p->activo,
                        'sucursales' => $p->sucursales->map(fn ($s) => $s->nombre),
                    ];
                }),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $plantillas->count(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en plantillas/ajax: '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'plantillas' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'error' => 'Error en el servidor: '.$e->getMessage(),
            ], 500);
        }
    }

    public function create()
    {
        $sucursales = Sucursal::orderBy('nombre')->get();

        return view('plantillas.create', compact('sucursales'));
    }

    public function store(StorePlantillaImpresionRequest $request)
    {
        $data = $request->validated();

        $plantilla = PlantillaImpresion::create($data);

        if ($request->filled('sucursal_ids')) {
            $plantilla->sucursales()->sync($request->sucursal_ids);
        }

        return redirect()->route('plantillas.index')
            ->with('success', "Plantilla '{$plantilla->nombre}' creada correctamente.");
    }

    public function show(PlantillaImpresion $plantilla)
    {
        $plantilla->load(['sucursales']);

        return view('plantillas.show', compact('plantilla'));
    }

    public function edit(PlantillaImpresion $plantilla)
    {
        $sucursales = Sucursal::orderBy('nombre')->get();
        $plantillaSucursales = $plantilla->sucursales->pluck('id')->toArray();

        return view('plantillas.edit', compact('plantilla', 'sucursales', 'plantillaSucursales'));
    }

    public function update(UpdatePlantillaImpresionRequest $request, PlantillaImpresion $plantilla)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($plantilla->logo_path) {
                Storage::disk('public')->delete($plantilla->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('plantillas', 'public');
            unset($data['_token'], $data['_method']);
        }

        $plantilla->update($data);

        if (isset($data['sucursal_ids'])) {
            $plantilla->sucursales()->sync($data['sucursal_ids']);
            unset($data['sucursal_ids']);
        }

        return redirect()->route('plantillas.index')
            ->with('success', "Plantilla '{$plantilla->nombre}' actualizada correctamente.");
    }

    public function toggleActiva(PlantillaImpresion $plantilla)
    {
        $plantilla->update(['activo' => ! $plantilla->activo]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'activo' => (bool) $plantilla->activo,
                'label' => (bool) $plantilla->activo ? 'Activa' : 'Inactiva',
            ]);
        }

        return back()->with('success', (bool) $plantilla->activo ? 'Plantilla activada.' : 'Plantilla desactivada.');
    }

    public function destroy(PlantillaImpresion $plantilla)
    {
        if ($plantilla->es_default) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'No se puede eliminar la plantilla predeterminada.']);
            }

            return back()->with('error', 'No se puede eliminar la plantilla predeterminada.');
        }

        if ($plantilla->ventas()->exists() || $plantilla->compras()->exists()) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'No se puede eliminar una plantilla asignada a documentos.']);
            }

            return back()->with('error', 'No se puede eliminar una plantilla asignada a documentos.');
        }

        if ($plantilla->logo_path) {
            Storage::disk('public')->delete($plantilla->logo_path);
        }

        $plantilla->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => "Plantilla '{$plantilla->nombre}' eliminada correctamente."]);
        }

        return back()->with('success', "Plantilla '{$plantilla->nombre}' eliminada correctamente.");
    }

    public function duplicate(PlantillaImpresion $plantilla)
    {
        $duplicado = $plantilla->duplicar();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => "Plantilla duplicada: {$duplicado->nombre}", 'url' => route('plantillas.edit', $duplicado)]);
        }

        return redirect()->route('plantillas.edit', $duplicado)
            ->with('success', "Plantilla duplicada: {$duplicado->nombre}");
    }

    public function setDefault(PlantillaImpresion $plantilla)
    {
        $plantilla->update(['es_default' => true]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Plantilla establecida como predeterminada.']);
        }

        return back()->with('success', 'Plantilla establecida como predeterminada.');
    }

    public function preview(PlantillaImpresion $plantilla)
    {
        $modulo = $plantilla->modulo ?: 'ventas';

        if ($modulo === 'compras') {
            $variables = $this->variablesService->datosCompra();
        } elseif ($modulo === 'historial_ventas') {
            $variables = $this->variablesService->datosHistorial();
        } else {
            $variables = $this->variablesService->datosDemo();
        }

        return view('plantillas.preview', ['template' => $plantilla, 'variables' => $variables]);
    }

    public function previewPdf(PlantillaImpresion $plantilla)
    {
        if ($plantilla->modulo === 'historial_ventas') {
            $pdf = app(PlantillaPdfGenerator::class)->previewPdfHistorial($plantilla);
        } elseif (($plantilla->tipo_doc_target ?? 0) === 2) {
            $pdf = app(PlantillaPdfGenerator::class)->previewPdfEcf($plantilla);
        } else {
            $pdf = app(PlantillaPdfGenerator::class)->previewPdfVenta($plantilla);
        }

        return $pdf->stream("preview_{$plantilla->codigo}.pdf");
    }

    public function previewCompra(PlantillaImpresion $plantilla)
    {
        return app(PlantillaPdfGenerator::class)->previewPdfCompra($plantilla);
    }

    public function previewHistorial(PlantillaImpresion $plantilla)
    {
        $pdf = app(PlantillaPdfGenerator::class)->previewPdfHistorial($plantilla);

        return $pdf->stream("preview_{$plantilla->codigo}.pdf");
    }
}
