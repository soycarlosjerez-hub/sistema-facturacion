<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InstanceApiKeysFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `instance_api_keys` (1 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('instance_api_keys');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('instance_api_keys')->truncate();

        $rows = [
            ['id' => 1, 'business_instance_id' => 1, 'name' => 'web-site', 'key' => '4e58233e48e70bddc7db2d036668980e83d7cf10b13c83ff0395b41efe6aa58b', 'last_used_at' => null, 'is_active' => 1, 'created_by' => 3, 'created_at' => '2026-07-01 15:18:11', 'updated_at' => '2026-07-01 15:18:11'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('instance_api_keys')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
