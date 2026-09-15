<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Insertar módulo plantillas si no existe (usar ID libre)
        $exists = DB::table('modulos')->where('key', 'plantillas')->exists();
        if (! $exists) {
            DB::table('modulos')->insert([
                'id' => 200,
                'key' => 'plantillas',
                'label' => 'Plantillas de Factura',
                'icon' => 'bi-file-earmark-richtext',
                'categoria' => 'configuracion',
                'section' => 'Otros',
                'sidebar_route' => 'plantillas.index',
                'sidebar_is_route' => 'plantillas.*',
                'sidebar_exact_route' => 'plantillas.index',
                'sidebar_url' => null,
                'sidebar_permission' => 'plantillas.view',
                'activo' => 1,
                'orden' => 65,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('modulos')->where('key', 'plantillas')->delete();
    }
};
