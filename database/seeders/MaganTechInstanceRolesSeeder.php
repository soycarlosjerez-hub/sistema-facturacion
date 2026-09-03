<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaganTechInstanceRolesSeeder extends Seeder
{
    public function run(): void
    {
        $instanceId = DB::table('business_instances')->where('slug', 'magan-tech')->value('id');

        if (!$instanceId) {
            $this->command->error('No se encontr' . 'o la instancia magan-tech.');
            return;
        }

        $existingRoles = DB::table('instance_roles')
            ->where('business_instance_id', $instanceId)
            ->pluck('name')
            ->toArray();

        $defaultRoles = [
            'admin',
            'technico',
            'soporte',
            'vendedor-tecnico',
            'soporte-n1',
            'soporte-n2',
            'redes',
            'almacen-tech',
        ];

        $newRoles = array_diff($defaultRoles, $existingRoles);

        if (empty($newRoles)) {
            $this->command->info('Los roles de maganTech ya existen.');
            return;
        }

        $now = now();
        $rows = [];
        $maxId = DB::table('instance_roles')->max('id') ?: 0;

        foreach ($newRoles as $i => $roleName) {
            $rows[] = [
                'id' => $maxId + $i + 1,
                'business_instance_id' => $instanceId,
                'name' => $roleName,
                'guard_name' => 'instance',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('instance_roles')->insert($rows);

        $this->command->info('Roles de maganTech creados: ' . implode(', ', $newRoles));
    }
}
