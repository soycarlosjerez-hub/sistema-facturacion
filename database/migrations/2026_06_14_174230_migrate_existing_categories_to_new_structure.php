<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Get the business type IDs by key
        $restaurantType = DB::table('business_types')->where('key', 'restaurante')->value('id');
        $productCatalogType = DB::table('business_types')->where('key', 'retail')->value('id');
        
        // Fallback to mixto if retail doesn't exist
        if (!$productCatalogType) {
            $productCatalogType = DB::table('business_types')->where('key', 'mixto')->value('id');
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

        // 3. Update foreign keys in productos table
        DB::statement("
            UPDATE productos p
            JOIN categorias oc ON p.categoria_id = oc.id
            JOIN categories nc ON nc.nombre = oc.nombre AND nc.tenant_id = 1
            SET p.categoria_id = nc.id
        ");

        // 4. Update foreign keys in mesas table
        DB::statement("
            UPDATE mesas m
            JOIN mesa_categorias oc ON m.categoria_id = oc.id
            JOIN categories nc ON nc.nombre = oc.nombre AND nc.tenant_id = 1
            SET m.categoria_id = nc.id
        ");
    }

    public function down(): void
    {
        // Cannot easily rollback data migration
    }
};