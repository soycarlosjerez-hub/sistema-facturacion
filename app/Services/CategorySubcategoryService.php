<?php

namespace App\Services;

use App\Models\CategoriaSub;
use Illuminate\Support\Facades\DB;

class CategorySubcategoryService
{
    public function getAllWithTree(): \Illuminate\Support\Collection
    {
        return CategoriaSub::with('children')
            ->whereNull('parent_id')
            ->activas()
            ->orderBy('orden')
            ->get();
    }

    public function getAll(): \Illuminate\Support\Collection
    {
        return CategoriaSub::orderBy('orden')
            ->orderBy('nombre')
            ->get();
    }

    public function findById(int $id): ?CategoriaSub
    {
        return CategoriaSub::with('parent', 'children')->find($id);
    }

    public function create(array $data): CategoriaSub
    {
        return CategoriaSub::create($data);
    }

    public function update(int $id, array $data): CategoriaSub
    {
        $category = CategoriaSub::findOrFail($id);

        // Prevent setting a child as its own parent
        if (isset($data['parent_id'])) {
            $this->validateCircularParent($id, $data['parent_id']);
        }

        $category->update($data);
        return $category;
    }

    public function delete(int $id): bool
    {
        $category = CategoriaSub::findOrFail($id);

        // Transfer children to root (parent_id = null)
        if ($category->children()->exists()) {
            $category->children()->update(['parent_id' => null]);
        }

        return $category->delete();
    }

    public function reorder(array $orderData): void
    {
        foreach ($orderData as $id => $orden) {
            CategoriaSub::where('id', $id)->update(['orden' => $orden]);
        }
    }

    protected function validateCircularParent(int $parentId, int $childId): void
    {
        if ($parentId === $childId) {
            throw new \Exception('Una categoría no puede ser subcategoría de sí misma.');
        }

        $child = CategoriaSub::find($childId);
        if (!$child) {
            throw new \Exception('La subcategoría no existe.');
        }

        // Walk up the tree to check if parentId is already a descendant
        $current = CategoriaSub::find($parentId);
        while ($current && $current->parent_id) {
            if ($current->parent_id === $childId) {
                throw new \Exception('No se puede asignar: crearía un ciclo en la jerarquía.');
            }
            $current = CategoriaSub::find($current->parent_id);
        }
    }

    public function getByParentId(int $parentId): \Illuminate\Support\Collection
    {
        return CategoriaSub::where('parent_id', $parentId)
            ->activas()
            ->orderBy('orden')
            ->get();
    }

    public function getFullCategoryTree(): array
    {
        $categories = CategoriaSub::with('children.children')
            ->whereNull('parent_id')
            ->activas()
            ->orderBy('orden')
            ->get();

        return $categories->map(function (CategoriaSub $category) {
            return $this->buildTreeRecursive($category);
        })->toArray();
    }

    protected function buildTreeRecursive(CategoriaSub $category): array
    {
        $node = [
            'id'   => $category->id,
            'name' => $category->nombre,
        ];

        if ($category->children->isNotEmpty()) {
            $node['children'] = $category->children->map(function ($child) {
                return $this->buildTreeRecursive($child);
            })->toArray();
        }

        return $node;
    }
}
