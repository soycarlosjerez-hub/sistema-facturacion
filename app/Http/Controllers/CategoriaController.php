<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    protected function resolveBusinessType(User $user): ?\App\Models\BusinessType
    {
        if ($user->businessInstance) {
            return $user->businessInstance->businessType;
        }

        if ($user->business_instance_id) {
            $inst = \App\Models\BusinessInstance::withTrashed()->find($user->business_instance_id);
            if ($inst) {
                return $inst->businessType;
            }
        }

        return $user->businessType;
    }

    public function index(Request $request)
    {
        $query = Categoria::query();

        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%'.$request->nombre.'%');
        }

        if ($request->filled('activo')) {
            $query->where('activa', (bool) $request->activo);
        }

        $categorias = $query->orderBy('orden')->orderBy('nombre')->get();

        return view('categorias.index', compact('categorias'));
    }

    public function indexAjax(Request $request)
    {
        $search = $request->input('search.value', '');
        $user = auth()->user();

        $query = Categoria::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', '%'.$search.'%')
                    ->orWhere('descripcion', 'like', '%'.$search.'%');
            });
        }

        $orderColumnIndex = (int) $request->input('columns.'.$request->input('order.0.column').'.data', 1);
        $orderDir = $request->input('order.0.dir', 'asc');
        $sortableColumns = ['id', 'nombre', 'descripcion', 'activa', 'orden'];
        $eloquentColumn = $sortableColumns[$orderColumnIndex] ?? 'orden';

        $query->orderBy($eloquentColumn, $orderDir);

        $total = (clone $query)->count();

        $skip = (int) $request->input('start', 0);
        $length = (int) $request->input('length', -1);

        if ($length > 0) {
            $query->skip($skip)->take($length);
        }

        $categorias = $query->get();

        $tenantId = $user->business_instance_id;
        $data = $categorias->map(function ($c) use ($tenantId) {
            $prodCount = Producto::where('categoria_id', $c->id)
                ->where('tenant_id', $tenantId)
                ->count();

            return [
                'id' => $c->id,
                'nombre' => $c->nombre,
                'descripcion' => $c->descripcion ?? 'Sin descripción',
                'productos_count' => $prodCount,
                'activa' => (bool) $c->activa,
                'color' => $c->color ?? '#6366f1',
                'icono' => $c->icono ?? 'bi-grid',
                'orden' => $c->orden ?? 0,
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
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'activa' => 'boolean',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icono' => 'nullable|string|max:50',
            'orden' => 'nullable|integer|min:0',
        ]);

        $user = auth()->user();
        $categoria = Categoria::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activa' => $request->boolean('activa', true),
            'color' => $request->color ?? null,
            'icono' => $request->icono ?? null,
            'orden' => $request->orden ?? 0,
            'tenant_id' => $user->business_instance_id,
        ]);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function show($categoria)
    {
        $user = auth()->user();
        $categoriaId = (int) ($categoria instanceof \App\Models\Categoria ? $categoria->id : $categoria);

        $categoria = Categoria::withoutGlobalScopes()->where('id', $categoriaId)
            ->when($user->business_instance_id, function ($q) use ($user) {
                $q->where('tenant_id', $user->business_instance_id);
            })
            ->first();

        if (! $categoria) {
            abort(404);
        }

        $productos = Producto::withoutGlobalScopes()->where('categoria_id', $categoria->id)
            ->when($user->business_instance_id, function ($q) use ($user) {
                $q->where('tenant_id', $user->business_instance_id);
            })
            ->where('activo', true)
            ->get();

        return view('categorias.show', compact('categoria', 'productos'));
    }

    public function edit(Categoria $categoria)
    {
        $user = auth()->user();

        if ($categoria->tenant_id !== $user->business_instance_id) {
            abort(404);
        }

        $productos = Producto::where('activo', true)
            ->where('tenant_id', $user->business_instance_id)
            ->orderBy('nombre')
            ->get();

        $categoriaNombres = DB::table('categorias')
            ->select('id', 'nombre')
            ->whereIn('id', $productos->pluck('categoria_id')->filter()->toArray())
            ->pluck('nombre', 'id');

        return view('categorias.edit', compact('categoria', 'productos', 'categoriaNombres'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $user = auth()->user();

        if ($categoria->tenant_id !== $user->business_instance_id) {
            abort(403, 'No tienes permisos para editar esta categoría.');
        }

        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'activa' => 'boolean',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icono' => 'nullable|string|max:50',
            'orden' => 'nullable|integer|min:0',
        ]);

        $categoria->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activa' => $request->boolean('activa'),
            'color' => $request->color,
            'icono' => $request->icono,
            'orden' => $request->orden ?? $categoria->orden,
        ]);

        if ($request->has('productos')) {
            Producto::where('categoria_id', $categoria->id)
                ->whereNotIn('id', $request->productos)
                ->where('tenant_id', $categoria->tenant_id)
                ->update(['categoria_id' => null]);

            foreach ($request->productos as $productoId) {
                Producto::where('id', $productoId)
                    ->where('tenant_id', $categoria->tenant_id)
                    ->update(['categoria_id' => $categoria->id]);
            }
        }

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function toggleActiva(Request $request, Categoria $categoria)
    {
        $user = auth()->user();

        if ($categoria->tenant_id !== $user->business_instance_id) {
            return response()->json(['success' => false, 'message' => 'Permiso denegado.'], 403);
        }

        $nuevaActivacion = ! $categoria->activa;

        $categoria->update([
            'activa' => $nuevaActivacion,
        ]);

        return response()->json([
            'success' => true,
            'activa' => $nuevaActivacion,
            'label' => $nuevaActivacion ? 'Activa' : 'Inactiva',
        ]);
    }

    public function pdf(Request $request)
    {
        $categorias = Categoria::where('activa', true)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        $pdf = Pdf::loadView('categorias.pdf', compact('categorias'));

        return $pdf->stream('categorias.pdf');
    }

    public function destroy(Request $request, Categoria $categoria)
    {
        $user = auth()->user();

        if ($categoria->tenant_id !== $user->business_instance_id) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Permiso denegado.'], 403);
            }

            return back()->with('error', 'No tienes permisos para eliminar esta categoría.');
        }

        Producto::where('categoria_id', $categoria->id)
            ->where('tenant_id', $user->business_instance_id)
            ->update(['categoria_id' => null]);

        $categoria->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Categoría eliminada correctamente.']);
        }

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }

    public function showImportForm()
    {
        return view('categorias.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        $filePath = $request->file('file')->store('categorias-import');

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\CategoriaImport, $filePath);
            \Storage::delete($filePath);

            return redirect()->route('categorias.index')
                ->with('success', 'Categorías importadas correctamente.');
        } catch (\Exception $e) {
            \Storage::delete($filePath);

            return back()->with('error', 'Error al importar: '.$e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->all();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\CategoriaExport,
            'categorias_'.date('Y-m-d').'.xlsx'
        );
    }
}
