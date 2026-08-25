<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InstanceRolesFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `instance_roles` (15 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('instance_roles');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('instance_roles')->truncate();

        $rows = [
            ['id' => 1, 'business_instance_id' => 1, 'name' => 'admin', 'guard_name' => 'instance', 'created_at' => '2026-07-01 15:06:37', 'updated_at' => '2026-07-01 15:06:37'],
            ['id' => 2, 'business_instance_id' => 2, 'name' => 'admin', 'guard_name' => 'instance', 'created_at' => '2026-07-02 16:57:10', 'updated_at' => '2026-07-02 16:57:10'],
            ['id' => 3, 'business_instance_id' => 3, 'name' => 'admin', 'guard_name' => 'instance', 'created_at' => '2026-07-07 20:54:48', 'updated_at' => '2026-07-07 20:54:48'],
            ['id' => 4, 'business_instance_id' => 4, 'name' => 'admin', 'guard_name' => 'instance', 'created_at' => '2026-07-22 23:37:47', 'updated_at' => '2026-07-22 23:37:47'],
            ['id' => 5, 'business_instance_id' => 5, 'name' => 'admin', 'guard_name' => 'instance', 'created_at' => '2026-07-23 10:45:41', 'updated_at' => '2026-07-23 10:45:41'],
            ['id' => 6, 'business_instance_id' => 2, 'name' => 'gerente', 'guard_name' => 'instance', 'created_at' => '2026-07-27 00:57:48', 'updated_at' => '2026-07-27 00:57:48'],
            ['id' => 7, 'business_instance_id' => 2, 'name' => 'mesero', 'guard_name' => 'instance', 'created_at' => '2026-07-27 00:57:48', 'updated_at' => '2026-07-27 00:57:48'],
            ['id' => 8, 'business_instance_id' => 2, 'name' => 'cocinero', 'guard_name' => 'instance', 'created_at' => '2026-07-27 00:57:49', 'updated_at' => '2026-07-27 00:57:49'],
            ['id' => 9, 'business_instance_id' => 2, 'name' => 'bartender', 'guard_name' => 'instance', 'created_at' => '2026-07-27 00:57:49', 'updated_at' => '2026-07-27 00:57:49'],
            ['id' => 10, 'business_instance_id' => 2, 'name' => 'delivery', 'guard_name' => 'instance', 'created_at' => '2026-07-27 00:57:49', 'updated_at' => '2026-07-27 00:57:49'],
            ['id' => 11, 'business_instance_id' => 2, 'name' => 'cajero', 'guard_name' => 'instance', 'created_at' => '2026-07-27 00:57:49', 'updated_at' => '2026-07-27 00:57:49'],
            ['id' => 12, 'business_instance_id' => 2, 'name' => 'contador', 'guard_name' => 'instance', 'created_at' => '2026-07-27 00:57:49', 'updated_at' => '2026-07-27 00:57:49'],
            ['id' => 13, 'business_instance_id' => 7, 'name' => 'admin', 'guard_name' => 'instance', 'created_at' => '2026-08-03 23:43:54', 'updated_at' => '2026-08-03 23:43:54'],
            ['id' => 14, 'business_instance_id' => 8, 'name' => 'admin', 'guard_name' => 'instance', 'created_at' => '2026-08-04 11:10:47', 'updated_at' => '2026-08-04 11:10:47'],
            ['id' => 15, 'business_instance_id' => 9, 'name' => 'admin', 'guard_name' => 'instance', 'created_at' => '2026-08-14 17:35:14', 'updated_at' => '2026-08-14 17:35:14'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('instance_roles')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
