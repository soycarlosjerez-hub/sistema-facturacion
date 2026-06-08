<?php

namespace App\Http\Controllers;

use App\Models\HistorialImpresion;
use App\Models\Impresora;
use App\Models\PlantillaImpresion;
use App\Services\PrintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ImpresoraController extends Controller
{
    public function __construct(private PrintService $printService) {}

    public function index()
    {
        $impresoras = Impresora::orderBy('orden')->orderBy('nombre')->get();
        $stats = [
            'total' => $impresoras->count(),
            'activas' => $impresoras->where('activo', true)->count(),
            'red' => $impresoras->where('tipo_conexion', 'red')->count(),
            'auto_ventas' => $impresoras->where('auto_imprimir_ventas', true)->count(),
        ];
        return view('impresoras.index', compact('impresoras', 'stats'));
    }

    public function create()
    {
        return view('impresoras.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:191',
            'tipo_conexion' => 'required|in:local,red,compartida,pdf',
            'direccion_ip' => 'nullable|required_if:tipo_conexion,red|ip',
            'puerto' => 'nullable|required_if:tipo_conexion,red|integer|min:1|max:65535',
            'ruta_compartida' => 'nullable|string|max:191',
            'driver' => 'required|in:escpos,windows,network,pdf',
            'papel_tamano' => 'required|in:58mm,80mm,letter',
            'caracteres_por_linea' => 'required|integer|min:20|max:80',
            'auto_imprimir_ventas' => 'boolean',
            'auto_imprimir_cotizaciones' => 'boolean',
            'auto_imprimir_conduces' => 'boolean',
            'activo' => 'boolean',
            'descripcion' => 'nullable|string|max:500',
            'orden' => 'integer|min:0',
        ]);

        Impresora::create($validated);
        return redirect()->route('impresoras.index')->with('success', 'Impresora creada correctamente');
    }

    public function edit(Impresora $impresora)
    {
        return view('impresoras.edit', compact('impresora'));
    }

    public function update(Request $request, Impresora $impresora)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:191',
            'tipo_conexion' => 'required|in:local,red,compartida,pdf',
            'direccion_ip' => 'nullable|required_if:tipo_conexion,red|ip',
            'puerto' => 'nullable|required_if:tipo_conexion,red|integer|min:1|max:65535',
            'ruta_compartida' => 'nullable|string|max:191',
            'driver' => 'required|in:escpos,windows,network,pdf',
            'papel_tamano' => 'required|in:58mm,80mm,letter',
            'caracteres_por_linea' => 'required|integer|min:20|max:80',
            'auto_imprimir_ventas' => 'boolean',
            'auto_imprimir_cotizaciones' => 'boolean',
            'auto_imprimir_conduces' => 'boolean',
            'activo' => 'boolean',
            'descripcion' => 'nullable|string|max:500',
            'orden' => 'integer|min:0',
        ]);

        $impresora->update($validated);
        return redirect()->route('impresoras.index')->with('success', 'Impresora actualizada');
    }

    public function destroy(Impresora $impresora)
    {
        $impresora->delete();
        return redirect()->route('impresoras.index')->with('success', 'Impresora eliminada');
    }

    public function probar(Impresora $impresora)
    {
        try {
            $resultado = $this->printService->enviarATexto($impresora, "=== PRUEBA DE IMPRESION ===\n\n" .
                "Impresora: {$impresora->nombre}\n" .
                "Conexion: {$impresora->tipo_conexion}\n" .
                "Fecha: " . now()->format('d/m/Y H:i:s') . "\n\n" .
                "Si lee este texto, la impresora funciona correctamente.\n");

            $impresora->historial()->create([
                'user_id' => auth()->id(),
                'tipo_documento' => 'prueba',
                'documento_numero' => null,
                'copias' => 1,
                'exitoso' => true,
            ]);

            return back()->with('success', "Prueba enviada a '{$impresora->nombre}': {$resultado}");
        } catch (\Throwable $e) {
            return back()->with('error', "Error en prueba: " . $e->getMessage());
        }
    }

    public function toggleAuto(Impresora $impresora, string $modulo)
    {
        $campo = match ($modulo) {
            'ventas' => 'auto_imprimir_ventas',
            'cotizaciones' => 'auto_imprimir_cotizaciones',
            'conduces' => 'auto_imprimir_conduces',
            default => null,
        };
        if (!$campo) {
            return back()->with('error', 'Módulo inválido');
        }
        $impresora->update([$campo => !$impresora->$campo]);
        return back()->with('success', "Auto-impresión para {$modulo} " .
            ($impresora->fresh()->$campo ? 'activada' : 'desactivada') . " en {$impresora->nombre}");
    }

    public function historial()
    {
        $historial = HistorialImpresion::with(['impresora', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->paginate(30);
        return view('impresoras.historial', compact('historial'));
    }

    public function plantillas(Request $request)
    {
        $query = PlantillaImpresion::orderBy('modulo')->orderBy('codigo');
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }
        $plantillas = $query->get();
        $modulos = PlantillaImpresion::MODULOS;
        return view('impresoras.plantillas', compact('plantillas', 'modulos'));
    }

    public function plantillaUpdate(Request $request, PlantillaImpresion $plantilla)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:191',
            'incluir_logo' => 'boolean',
            'incluir_encabezado' => 'boolean',
            'incluir_pie' => 'boolean',
            'encabezado_personalizado' => 'nullable|string|max:500',
            'pie_personalizado' => 'nullable|string|max:500',
            'activo' => 'boolean',
        ]);

        $plantilla->update($validated);
        return back()->with('success', 'Plantilla actualizada');
    }

    public function printDialog(Request $request)
    {
        $impresoras = Impresora::activas()->get();
        $tipo = $request->tipo; // venta, cotizacion, conduce
        $id = $request->id;

        if ($request->ajax()) {
            return response()->json([
                'html' => view('impresoras._print_dialog', compact('impresoras', 'tipo', 'id'))->render(),
            ]);
        }

        $impresoraPorDefecto = Impresora::activas()
            ->where("auto_imprimir_{$tipo}s", true)
            ->first();

        return view('impresoras.print-dialog', compact('impresoras', 'tipo', 'id', 'impresoraPorDefecto'));
    }

    public function printDirect(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:venta,cotizacion,conduce,ecf',
            'id' => 'required|integer',
            'impresora_id' => 'nullable|exists:impresoras,id',
            'formato' => 'required|in:ticket,pdf',
            'copias' => 'integer|min:1|max:10',
            'papel_tamano' => 'nullable|in:58mm,80mm,letter',
        ]);

        $impresora = $request->impresora_id
            ? Impresora::findOrFail($request->impresora_id)
            : Impresora::activas()->first();

        if (!$impresora) {
            return response()->json(['success' => false, 'mensaje' => 'No hay impresoras activas']);
        }

        try {
            $resultado = $this->printService->imprimirDocumento(
                tipo: $request->tipo,
                id: $request->id,
                impresora: $impresora,
                formato: $request->formato,
                copias: $request->copias ?? 1,
                papelTamano: $request->papel_tamano ?? $impresora->papel_tamano,
            );

            return response()->json(['success' => true, 'mensaje' => $resultado]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'mensaje' => $e->getMessage()]);
        }
    }
}
