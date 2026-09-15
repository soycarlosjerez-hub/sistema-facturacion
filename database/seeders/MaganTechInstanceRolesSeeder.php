<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Legacy: crea los roles de MaganTech.
 * Reemplazado por InstanceRolesCatalogSeeder (idempotente, catálogo unificado).
 * Se mantiene solo como respaldo.
 */
class MaganTechInstanceRolesSeeder extends Seeder
{
    public function run(): void
    {
        $instanceId = DB::table('business_instances')->where('slug', 'magan-tech')->value('id');

        if (! $instanceId) {
            return;
        }

        $defaultRoles = [
            'admin',
            'tecnico',
            'soporte-n1',
            'soporte-n2',
            'vendedor-tecnico',
            'redes',
            'almacen-tech',
        ];

        $existingRoles = DB::table('instance_roles')
            ->where('business_instance_id', $instanceId)
            ->pluck('name')
            ->toArray();

        $newRoles = array_diff($defaultRoles, $existingRoles);

        if (empty($newRoles)) {
            return;
        }

        $now = now();
        $maxId = DB::table('instance_roles')->max('id') ?: 0;

        foreach ($newRoles as $i => $roleName) {
            DB::table('instance_roles')->insert([
                'id' => $maxId + $i + 1,
                'business_instance_id' => $instanceId,
                'name' => $roleName,
                'guard_name' => 'instance',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
