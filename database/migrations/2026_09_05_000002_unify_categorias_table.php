<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Phase 1: Add columns to categorias
        Schema::table('categorias', function (Blueprint $table) {
            if (!Schema::hasColumn('categorias', 'color')) $table->string('color', 7)->default('#6366f1')->after('activa');
            if (!Schema::hasColumn('categorias', 'icono')) $table->string('icono', 50)->default('bi-grid')->after('color');
            if (!Schema::hasColumn('categorias', 'orden')) $table->integer('orden')->default(0)->after('icono');
            if (!Schema::hasColumn('categorias', 'configuracion')) $table->json('configuracion')->nullable()->after('orden');
            if (!Schema::hasColumn('categorias', 'deleted_at')) $table->timestamp('deleted_at')->nullable()->after('configuracion');
        });

        // Phase 2: Drop old tables
        try { DB::statement('DROP TABLE IF EXISTS categorizables'); } catch (QueryException $e) {}
        try { DB::statement('DROP TABLE IF EXISTS category_subcategories'); } catch (QueryException $e) {}
        try { DB::statement('DROP TABLE IF EXISTS categories'); } catch (QueryException $e) {}

        // Phase 3: Recreate categorizables
        Schema::create('categorizables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categorias')->onDelete('cascade');
            $table->string('categorizable_type', 255);
            $table->foreignId('categorizable_id');
            $table->json('configuracion')->nullable();
            $table->boolean('soft_delete_enabled')->default(true);
            $table->timestamps();
            $table->index(['categorizable_type', 'categorizable_id']);
        });

        // Phase 4: Populate categorizables - link all categorias to all business_types
        $businessTypes = DB::table('business_types')->pluck('id')->toArray();
        $categorias = DB::table('categorias')->pluck('id')->toArray();

        foreach ($businessTypes as $btId) {
            foreach ($categorias as $catId) {
                DB::table('categorizables')->insert([
                    'category_id' => $catId,
                    'categorizable_type' => 'App\Models\BusinessType',
                    'categorizable_id' => $btId,
                    'configuracion' => null,
                    'soft_delete_enabled' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Phase 5: Recreate category_subcategories
        Schema::create('category_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categorias')->onDelete('cascade');
            $table->foreignId('business_type_id')->constrained('business_types')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('category_subcategories')->onDelete('cascade');
            $table->string('nombre', 200);
            $table->smallInteger('orden')->default(0);
            $table->boolean('activa')->default(true);
            $table->json('configuracion')->nullable();
            $table->timestamps();
            $table->index(['category_id', 'business_type_id']);
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Drop new tables
        try { DB::statement('DROP TABLE IF EXISTS category_subcategories'); } catch (QueryException $e) {}
        try { DB::statement('DROP TABLE IF EXISTS categorizables'); } catch (QueryException $e) {}

        // Recreate old categories table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->onDelete('set null');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->boolean('activa')->default(true);
            $table->string('color', 7)->nullable();
            $table->string('icono', 50)->nullable();
            $table->integer('orden')->default(0);
            $table->json('configuracion')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Recreate old categorizables
        Schema::create('categorizables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('categorizable_type', 255);
            $table->foreignId('categorizable_id');
            $table->json('configuracion')->nullable();
            $table->boolean('soft_delete_enabled')->default(true);
            $table->timestamps();
            $table->index(['categorizable_type', 'categorizable_id']);
        });

        // Recreate old category_subcategories
        Schema::create('category_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('business_type_id')->nullable()->constrained('business_types')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('category_subcategories')->onDelete('cascade');
            $table->string('nombre', 200);
            $table->smallInteger('orden')->default(0);
            $table->boolean('activa')->default(true);
            $table->json('configuracion')->nullable();
            $table->timestamps();
        });

        // Remove extra columns from categorias
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn(['color', 'icono', 'orden', 'configuracion', 'deleted_at']);
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
