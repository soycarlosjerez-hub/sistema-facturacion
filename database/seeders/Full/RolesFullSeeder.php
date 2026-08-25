<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolesFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `roles` (13 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('roles');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('roles')->truncate();

        $rows = [
            ['id' => 1, 'name' => 'admin-business', 'guard_name' => 'web', 'created_at' => '2026-07-01 14:06:30', 'updated_at' => '2026-07-01 14:06:30'],
            ['id' => 2, 'name' => 'owner', 'guard_name' => 'web', 'created_at' => '2026-07-01 14:06:31', 'updated_at' => '2026-07-01 14:06:31'],
            ['id' => 3, 'name' => 'admin', 'guard_name' => 'web', 'created_at' => '2026-07-01 15:03:54', 'updated_at' => '2026-07-01 15:03:54'],
            ['id' => 4, 'name' => 'gerente', 'guard_name' => 'web', 'created_at' => '2026-07-01 15:03:54', 'updated_at' => '2026-07-01 15:03:54'],
            ['id' => 5, 'name' => 'vendedor', 'guard_name' => 'web', 'created_at' => '2026-07-01 15:03:54', 'updated_at' => '2026-07-01 15:03:54'],
            ['id' => 6, 'name' => 'almacen', 'guard_name' => 'web', 'created_at' => '2026-07-01 15:03:54', 'updated_at' => '2026-07-01 15:03:54'],
            ['id' => 7, 'name' => 'contador', 'guard_name' => 'web', 'created_at' => '2026-07-01 15:03:54', 'updated_at' => '2026-07-01 15:03:54'],
            ['id' => 8, 'name' => 'root', 'guard_name' => 'web', 'created_at' => '2026-07-01 15:03:54', 'updated_at' => '2026-07-01 15:03:54'],
            ['id' => 9, 'name' => 'mesero', 'guard_name' => 'web', 'created_at' => '2026-07-27 00:47:44', 'updated_at' => '2026-07-27 00:47:44'],
            ['id' => 10, 'name' => 'cocinero', 'guard_name' => 'web', 'created_at' => '2026-07-27 00:47:45', 'updated_at' => '2026-07-27 00:47:45'],
            ['id' => 11, 'name' => 'bartender', 'guard_name' => 'web', 'created_at' => '2026-07-27 00:47:45', 'updated_at' => '2026-07-27 00:47:45'],
            ['id' => 12, 'name' => 'delivery', 'guard_name' => 'web', 'created_at' => '2026-07-27 00:47:45', 'updated_at' => '2026-07-27 00:47:45'],
            ['id' => 13, 'name' => 'cajero', 'guard_name' => 'web', 'created_at' => '2026-07-27 00:47:45', 'updated_at' => '2026-07-27 00:47:45'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('roles')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
