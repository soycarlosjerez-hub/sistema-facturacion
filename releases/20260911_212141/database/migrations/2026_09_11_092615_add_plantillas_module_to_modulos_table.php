<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Insertar el módulo "plantillas" en la tabla modulos si no existe (usar ID único alto)
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

        // 2. Agregar "plantillas" a todos los InstanceRoles existentes
        if (Schema::hasTable('instance_role_modules')) {
            $roles = DB::table('instance_role_modules')
                ->select('instance_role_id')
                ->distinct()
                ->get();

            foreach ($roles as $role) {
                $existsInRole = DB::table('instance_role_modules')
                    ->where('instance_role_id', $role->instance_role_id)
                    ->where('modulo_key', 'plantillas')
                    ->exists();

                if (! $existsInRole) {
                    DB::table('instance_role_modules')->insert([
                        'instance_role_id' => $role->instance_role_id,
                        'modulo_key' => 'plantillas',
                        'is_visible' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        DB::table('modulos')->where('key', 'plantillas')->delete();
        if (Schema::hasTable('instance_role_modules')) {
            DB::table('instance_role_modules')->where('modulo_key', 'plantillas')->delete();
        }
    }
};
