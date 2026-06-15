<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\BusinessType;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with(['businessTypes' => function ($q) {
            $q->select('business_types.id', 'business_types.key', 'business_types.nombre', 'business_types.color_default', 'business_types.icono_default');
        }])
            ->where('tenant_id', $request->user()->tenant_id)
            ->withCount(['products', 'tables']);

        // Filtros
        if ($request->filled('type')) {
            $query->ofType($request->type);
        }
        if ($request->filled('activa')) {
            $query->where('activa', $request->boolean('activa'));
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->search}%")
                    ->orWhere('descripcion', 'like', "%{$request->search}%");
            });
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'orden');
        $sortDir = $request->get('sort_dir', 'asc');
        $query->orderBy($sortBy, $sortDir)->orderBy('nombre');

        $perPage = min($request->get('per_page', 20), 100);
        $categories = $query->paginate($perPage)->withQueryString();

        return CategoryResource::collection($categories);
    }

    public function store(CategoryStoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $category = Category::create([
                'tenant_id' => $request->user()->tenant_id,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'activa' => $request->activa,
                'color' => $request->color,
                'icono' => $request->icono,
                'orden' => $request->orden,
                'configuracion' => $request->configuracion,
            ]);

            $this->syncBusinessTypes($category, $request->type_keys, $request->type_configs ?? []);

            return new CategoryResource($category->load('businessTypes'));
        });
    }

    public function show(Category $category)
    {
        $this->authorize('view', $category);
        return new CategoryResource($category->load(['businessTypes', 'products', 'tables']));
    }

    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $this->authorize('update', $category);

        return DB::transaction(function () use ($request, $category) {
            $category->update($request->only([
                'nombre', 'descripcion', 'activa', 'color', 'icono', 'orden', 'configuracion'
            ]));

            if ($request->has('type_keys')) {
                $this->syncBusinessTypes($category, $request->type_keys, $request->type_configs ?? []);
            }

            return new CategoryResource($category->load('businessTypes'));
        });
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        // Check if category has products or tables in any business type
        $hasProducts = $category->products()->exists();
        $hasTables = $category->tables()->exists();

        if ($hasProducts || $hasTables) {
            return response()->json([
                'message' => 'No se puede eliminar la categoría porque tiene productos o mesas asociadas.',
            ], 422);
        }

        $category->delete();

        return response()->json(['message' => 'Categoría eliminada correctamente.']);
    }

    public function toggleActiva(Category $category)
    {
        $this->authorize('update', $category);
        $category->update(['activa' => !$category->activa]);
        return new CategoryResource($category);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:categories,id',
            'items.*.orden' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            Category::where('id', $item['id'])
                ->where('tenant_id', $request->user()->tenant_id)
                ->update(['orden' => $item['orden']]);
        }

        return response()->json(['message' => 'Orden actualizado correctamente.']);
    }

    public function toggleType(Category $category, Request $request)
    {
        $this->authorize('update', $category);

        $request->validate([
            'type_key' => 'required|string|exists:business_types,key',
            'action' => 'required|in:attach,detach',
            'configuracion' => 'nullable|array',
            'soft_delete_enabled' => 'boolean',
        ]);

        $type = BusinessType::where('key', $request->type_key)->firstOrFail();

        if ($request->action === 'attach') {
            $category->businessTypes()->syncWithoutDetaching([
                $type->id => [
                    'configuracion' => $request->configuracion ?? [],
                    'soft_delete_enabled' => $request->boolean('soft_delete_enabled', true),
                    'orden' => $request->integer('orden', 0),
                ]
            ]);
        } else {
            $category->businessTypes()->detach($type->id);
        }

        return new CategoryResource($category->load('businessTypes'));
    }

    private function syncBusinessTypes(Category $category, array $typeKeys, array $typeConfigs = [])
    {
        $types = BusinessType::whereIn('key', $typeKeys)->get()->keyBy('key');
        
        $syncData = [];
        foreach ($typeKeys as $key) {
            $type = $types[$key];
            $syncData[$type->id] = [
                'configuracion' => $typeConfigs[$key] ?? [],
                'soft_delete_enabled' => $type->soft_delete_default,
                'orden' => 0,
            ];
        }

        $category->businessTypes()->sync($syncData);
    }
}