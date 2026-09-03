<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaganTechInstanceRoleModulesSeeder extends Seeder
{
    public function run(): void
    {
        $instanceId = DB::table('business_instances')->where('slug', 'magan-tech')->value('id');

        if (!$instanceId) {
            $this->command->error('No se encontr' . 'o la instancia magan-tech.');
            return;
        }

        $adminRole = DB::table('instance_roles')
            ->where('business_instance_id', $instanceId)
            ->where('name', 'admin')
            ->first();

        if (!$adminRole) {
            $this->command->error('No se encontr' . 'o el rol admin en magan-tech.');
            return;
        }

        $techModules = [
            'tecnologia', 'dashboard', 'inventario', 'compras', 'proveedores',
            'clientes', 'cajas', 'gastos',
            'equipos', 'tecnicas', 'tecnicos',
            'domotica', 'garantias',
            'marcas-tecnologicas', 'licencias-software', 'redes-config',
            'presupuestos', 'tecnica-especialidades', 'garantias-config',
            'sucursales', 'almacenes',
            'reportes-ventas', 'reportes-caja', 'reportes-stock',
            'reportes-retenciones', 'reportes-fiscales', 'reportes-resumen',
            'reportes-gastos',
            'cuentas-bancarias',
            'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
            'libros-ventas', 'libros-compras', 'formulario-14-14',
            'configuracion-general', 'impresoras', 'payment-processors',
            'auditoria', 'backups', 'plantilla-gastos',
        ];

        $existingModules = DB::table('instance_role_modules')
            ->where('instance_role_id', $adminRole->id)
            ->pluck('modulo_key')
            ->toArray();

        $newModules = array_diff($techModules, $existingModules);

        if (empty($newModules)) {
            $this->command->info('Los m' . 'odulos del admin en maganTech ya existen.');
            return;
        }

        $now = now();
        $rows = [];
        $maxId = DB::table('instance_role_modules')->max('id') ?: 0;

        foreach ($newModules as $i => $moduloKey) {
            $rows[] = [
                'id' => $maxId + $i + 1,
                'instance_role_id' => $adminRole->id,
                'modulo_key' => $moduloKey,
                'is_visible' => 1,
                'orden' => $i + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('instance_role_modules')->insert($rows);

        $this->command->info('M' . 'odulos de tecnolog' . 'a asignados al admin en maganTech: ' . count($newModules));
    }
}
