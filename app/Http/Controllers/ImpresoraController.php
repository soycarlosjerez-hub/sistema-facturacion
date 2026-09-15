<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImpresoraRequest;
use App\Http\Requests\UpdateImpresoraRequest;
use App\Models\Impresora;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class ImpresoraController extends Controller
{
    public function index(Request $request)
    {
        $sucursales = Sucursal::where('tenant_id', $request->user()->business_instance_id)
            ->orderBy('nombre')
            ->get();

        return view('impresoras.index', compact('sucursales'));
    }

    public function indexAjax(Request $request)
    {
        $user = $request->user();
        $search = $request->input('search.value', '');

        $query = Impresora::query();

        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }

        if ($request->filled('activo')) {
            $query->where('activo', (bool) $request->activo);
        }

        if ($request->filled('auto_imprimir')) {
            $query->where('auto_imprimir_ventas', true);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%")
                    ->orWhere('tipo_conexion', 'like', "%{$search}%");
            });
        }

        $total = (clone $query)->count();

        $skip = (int) $request->input('start', 0);
        $length = (int) $request->input('length', -1);

        if ($length > 0) {
            $query->skip($skip)->take($length);
        }

        $query->orderBy('orden')->orderBy('nombre');

        $impresoras = $query->with('sucursal')->get();

        $data = $impresoras->map(function ($i) {
            return [
                'id' => $i->id,
                'nombre' => $i->nombre,
                'sucursal' => $i->sucursal ? $i->sucursal->nombre : '<span class="text-muted">Sin sucursal</span>',
                'sucursal_raw' => $i->sucursal_id ?? 'sin_sucursal',
                'tipo_conexion' => $this->tipoConexionLabel($i->tipo_conexion),
                'papel' => $i->papel_tamano,
                'caracteres' => $i->caracteres_por_linea,
                'auto_imprimir' => $i->auto_imprimir_ventas ? 'Sí' : 'No',
                'activo' => (bool) $i->activo,
                'orden' => $i->orden,
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        $sucursales = Sucursal::where('tenant_id', $user->business_instance_id)
            ->orderBy('nombre')
            ->get();

        return view('impresoras.create', compact('sucursales'));
    }

    public function store(StoreImpresoraRequest $request)
    {
        $user = auth()->user();

        Impresora::create([
            'nombre' => $request->nombre,
            'tipo' => $request->tipo ?? 'general',
            'sucursal_id' => $request->sucursal_id,
            'tenant_id' => $user->business_instance_id,
            'tipo_conexion' => $request->tipo_conexion,
            'direccion_ip' => $request->direccion_ip,
            'puerto' => $request->puerto ?? 9100,
            'ruta_compartida' => $request->ruta_compartida,
            'driver' => $request->driver ?? 'escpos',
            'papel_tamano' => $request->papel_tamano,
            'caracteres_por_linea' => $request->caracteres_por_linea ?? ($request->papel_tamano === '58mm' ? 42 : 48),
            'auto_imprimir_ventas' => $request->boolean('auto_imprimir_ventas', false),
            'auto_imprimir_cotizaciones' => $request->boolean('auto_imprimir_cotizaciones', false),
            'auto_imprimir_conduces' => $request->boolean('auto_imprimir_conduces', false),
            'activo' => $request->boolean('activo', true),
            'orden' => $request->orden ?? 0,
            'descripcion' => $request->descripcion,
            'configuracion' => $request->configuracion ? [
                'copias' => (int) ($request->configuracion['copias'] ?? 1),
                'densidad' => $request->configuracion['densidad'] ?? 'normal',
                'font_size' => $request->configuracion['font_size'] ?? null,
                'impresion' => $request->configuracion['impresion'] ?? 'normal',
                'margenes' => [
                    'top' => (int) ($request->configuracion['margenes']['top'] ?? 3),
                    'right' => (int) ($request->configuracion['margenes']['right'] ?? 2),
                    'bottom' => (int) ($request->configuracion['margenes']['bottom'] ?? 3),
                    'left' => (int) ($request->configuracion['margenes']['left'] ?? 2),
                ],
            ] : null,
        ]);

        return redirect()->route('impresoras.index')
            ->with('success', 'Impresora registrada correctamente.');
    }

    public function show($id)
    {
        $user = auth()->user();
        $impresora = Impresora::where('id', $id)
            ->where('tenant_id', $user->business_instance_id)
            ->with('sucursal')
            ->first();

        if (! $impresora) {
            abort(404);
        }

        return view('impresoras.show', compact('impresora'));
    }

    public function edit(Impresora $impresora)
    {
        $user = auth()->user();

        if ($impresora->tenant_id !== $user->business_instance_id) {
            abort(404);
        }

        $sucursales = Sucursal::where('tenant_id', $user->business_instance_id)
            ->orderBy('nombre')
            ->get();

        return view('impresoras.edit', compact('impresora', 'sucursales'));
    }

    public function update(UpdateImpresoraRequest $request, Impresora $impresora)
    {
        $impresora->update([
            'nombre' => $request->nombre,
            'tipo' => $request->tipo ?? $impresora->tipo,
            'sucursal_id' => $request->sucursal_id,
            'tipo_conexion' => $request->tipo_conexion,
            'direccion_ip' => $request->direccion_ip,
            'puerto' => $request->puerto ?? $impresora->puerto,
            'ruta_compartida' => $request->ruta_compartida,
            'driver' => $request->driver ?? $impresora->driver,
            'papel_tamano' => $request->papel_tamano,
            'caracteres_por_linea' => $request->caracteres_por_linea ?? $impresora->caracteres_por_linea,
            'auto_imprimir_ventas' => $request->boolean('auto_imprimir_ventas'),
            'auto_imprimir_cotizaciones' => $request->boolean('auto_imprimir_cotizaciones'),
            'auto_imprimir_conduces' => $request->boolean('auto_imprimir_conduces'),
            'activo' => $request->boolean('activo'),
            'orden' => $request->orden ?? $impresora->orden,
            'descripcion' => $request->descripcion,
            'configuracion' => $request->configuracion ? [
                'copias' => (int) ($request->configuracion['copias'] ?? 1),
                'densidad' => $request->configuracion['densidad'] ?? 'normal',
                'font_size' => $request->configuracion['font_size'] ?? null,
                'impresion' => $request->configuracion['impresion'] ?? 'normal',
                'margenes' => [
                    'top' => (int) ($request->configuracion['margenes']['top'] ?? 3),
                    'right' => (int) ($request->configuracion['margenes']['right'] ?? 2),
                    'bottom' => (int) ($request->configuracion['margenes']['bottom'] ?? 3),
                    'left' => (int) ($request->configuracion['margenes']['left'] ?? 2),
                ],
            ] : null,
        ]);

        return redirect()->route('impresoras.index')
            ->with('success', 'Impresora actualizada correctamente.');
    }

    public function toggleActiva(Request $request, Impresora $impresora)
    {
        $user = auth()->user();

        if ($impresora->tenant_id !== $user->business_instance_id) {
            return response()->json(['success' => false, 'message' => 'Permiso denegado.'], 403);
        }

        $nuevaActivacion = ! $impresora->activo;

        $impresora->update([
            'activo' => $nuevaActivacion,
        ]);

        return response()->json([
            'success' => true,
            'activo' => $nuevaActivacion,
            'label' => $nuevaActivacion ? 'Activa' : 'Inactiva',
        ]);
    }

    public function manual()
    {
        return view('impresoras.manual');
    }

    public function destroy(Request $request, Impresora $impresora)
    {
        $user = auth()->user();

        if ($impresora->tenant_id !== $user->business_instance_id) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Permiso denegado.'], 403);
            }

            return back()->with('error', 'No tienes permisos para eliminar esta impresora.');
        }

        $impresora->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Impresora eliminada correctamente.']);
        }

        return redirect()->route('impresoras.index')
            ->with('success', 'Impresora eliminada correctamente.');
    }

    public function imprimirTicket(Impresora $impresora)
    {
        return redirect()->route('ventas.ticket', 1);
    }

    private function tipoConexionLabel($tipo): string
    {
        $mapa = [
            'local' => 'Local',
            'usb' => 'USB',
            'red' => 'Red',
            'pdf' => 'PDF',
        ];

        return $mapa[$tipo] ?? ucfirst($tipo);
    }
}
