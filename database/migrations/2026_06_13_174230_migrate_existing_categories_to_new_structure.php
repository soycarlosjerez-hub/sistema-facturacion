<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Get the business type IDs by slug
        $restaurantType = DB::table('business_types')->where('slug', 'restaurante')->value('id');
        $productCatalogType = DB::table('business_types')->where('slug', 'retail')->value('id');
        
        // Fallback to mixto if retail doesn't exist
        if (!$productCatalogType) {
            $productCatalogType = DB::table('business_types')->where('slug', 'mixto')->value('id');
        }

        // Clean up existing wrong records first
        DB::table('categorizables')
            ->where('categorizable_type', '!=', 'App\\Models\\BusinessType')
            ->delete();

        // 1. Migrate existing 'categorias' (products) to new 'categories' table
        $oldCategorias = DB::table('categorias')->get();
        
        foreach ($oldCategorias as $cat) {
            $newId = DB::table('categories')->insertGetId([
                'tenant_id' => 1,
                'nombre' => $cat->nombre,
                'descripcion' => $cat->descripcion,
                'activa' => $cat->activa ?? true,
                'color' => null,
                'icono' => null,
                'orden' => 0,
                'configuracion' => null,
                'created_at' => $cat->created_at ?? now(),
                'updated_at' => $cat->updated_at ?? now(),
            ]);

            // Link to retail (product catalog) business type
            DB::table('categorizables')->insert([
                'category_id' => $newId,
                'categorizable_type' => 'App\\Models\\BusinessType',
                'categorizable_id' => $productCatalogType,
                'configuracion' => null,
                'soft_delete_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Migrate existing 'mesa_categorias' (restaurant tables) to new 'categories' table
        $oldMesaCats = DB::table('mesa_categorias')->get();
        
        foreach ($oldMesaCats as $cat) {
            $newId = DB::table('categories')->insertGetId([
                'tenant_id' => 1,
                'nombre' => $cat->nombre,
                'descripcion' => null,
                'activa' => true,
                'color' => $cat->color ?? '#6b7280',
                'icono' => $cat->icono ?? 'bi-grid',
                'orden' => $cat->orden ?? 0,
                'configuracion' => null,
                'created_at' => $cat->created_at ?? now(),
                'updated_at' => $cat->updated_at ?? now(),
            ]);

            // Link to restaurant business type
            DB::table('categorizables')->insert([
                'category_id' => $newId,
                'categorizable_type' => 'App\\Models\\BusinessType',
                'categorizable_id' => $restaurantType,
                'configuracion' => null,
                'soft_delete_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Update foreign keys in productos table (cross-DB compatible)
        $categoriaMap = DB::table('categorias')
            ->join('categories', function ($join) {
                $join->on('categorias.nombre', '=', 'categories.nombre')
                     ->where('categories.tenant_id', 1);
            })
            ->select('categorias.id as old_id', 'categories.id as new_id')
            ->get();

        foreach ($categoriaMap as $map) {
            DB::table('productos')
                ->where('categoria_id', $map->old_id)
                ->update(['categoria_id' => $map->new_id]);
        }

        // 4. Update foreign keys in mesas table (cross-DB compatible)
        $mesaCatMap = DB::table('mesa_categorias')
            ->join('categories', function ($join) {
                $join->on('mesa_categorias.nombre', '=', 'categories.nombre')
                     ->where('categories.tenant_id', 1);
            })
            ->select('mesa_categorias.id as old_id', 'categories.id as new_id')
            ->get();

        foreach ($mesaCatMap as $map) {
            DB::table('mesas')
                ->where('categoria_id', $map->old_id)
                ->update(['categoria_id' => $map->new_id]);
        }
    }

    public function down(): void
    {
        // Cannot easily rollback data migration
    }
};