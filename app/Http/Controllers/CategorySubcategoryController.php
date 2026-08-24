<?php

namespace App\Http\Controllers;

use App\Models\CategoriaSub;
use App\Services\CategorySubcategoryService;
use Illuminate\Http\Request;

class CategorySubcategoryController extends Controller
{
    protected CategorySubcategoryService $service;

    public function __construct(CategorySubcategoryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $tree = $this->service->getFullCategoryTree();
        $allCategories = CategoriaSub::with('parent')->orderBy('orden')->get();

        return view('category-subcategories.index', compact('tree', 'allCategories'));
    }

    public function create()
    {
        $parents = CategoriaSub::whereNull('parent_id')
            ->activas()
            ->orderBy('nombre')
            ->get();

        return view('category-subcategories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id'   => 'nullable|exists:categoria_subs,id',
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'activa'      => 'boolean',
            'orden'       => 'nullable|integer|min:0',
        ]);

        $data['activa'] = $request->boolean('activa', true);
        $data['tenant_id'] = auth()->user()->business_instance_id;

        $this->service->create($data);

        return redirect()->route('category-subcategories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function show(CategoriaSub $categoriaSub)
    {
        $categoriaSub->load(['parent', 'children']);
        return view('category-subcategories.show', compact('categoriaSub'));
    }

    public function edit(CategoriaSub $categoriaSub)
    {
        $parents = CategoriaSub::whereNull('parent_id')
            ->activas()
            ->where('id', '!=', $categoriaSub->id)
            ->orderBy('nombre')
            ->get();

        return view('category-subcategories.edit', compact('categoriaSub', 'parents'));
    }

    public function update(Request $request, CategoriaSub $categoriaSub)
    {
        $data = $request->validate([
            'parent_id'   => 'nullable|exists:categoria_subs,id',
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'activa'      => 'boolean',
            'orden'       => 'nullable|integer|min:0',
        ]);

        $data['activa'] = $request->boolean('activa', true);

        $this->service->update($categoriaSub->id, $data);

        return redirect()->route('category-subcategories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(CategoriaSub $categoriaSub)
    {
        $this->service->delete($categoriaSub->id);

        return redirect()->route('category-subcategories.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }

    public function getChildren(CategoriaSub $categoriaSub)
    {
        $children = $this->service->getByParentId($categoriaSub->id);
        return response()->json($children);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:categoria_subs,id',
        ]);

        $this->service->reorder($request->input('order'));

        return response()->json(['success' => true, 'message' => 'Orden actualizado.']);
    }
}
