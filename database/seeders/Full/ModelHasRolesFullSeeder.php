<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModelHasRolesFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `model_has_roles` (25 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('model_has_roles');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('model_has_roles')->truncate();

        $rows = [
            ['role_id' => 8, 'model_type' => 'App\\Models\\User', 'model_id' => 1],
            ['role_id' => 5, 'model_type' => 'App\\Models\\User', 'model_id' => 2],
            ['role_id' => 2, 'model_type' => 'App\\Models\\User', 'model_id' => 3],
            ['role_id' => 1, 'model_type' => 'App\\Models\\User', 'model_id' => 4],
            ['role_id' => 4, 'model_type' => 'App\\Models\\User', 'model_id' => 5],
            ['role_id' => 6, 'model_type' => 'App\\Models\\User', 'model_id' => 6],
            ['role_id' => 7, 'model_type' => 'App\\Models\\User', 'model_id' => 7],
            ['role_id' => 1, 'model_type' => 'App\\Models\\User', 'model_id' => 8],
            ['role_id' => 1, 'model_type' => 'App\\Models\\User', 'model_id' => 9],
            ['role_id' => 1, 'model_type' => 'App\\Models\\User', 'model_id' => 10],
            ['role_id' => 4, 'model_type' => 'App\\Models\\User', 'model_id' => 12],
            ['role_id' => 13, 'model_type' => 'App\\Models\\User', 'model_id' => 13],
            ['role_id' => 9, 'model_type' => 'App\\Models\\User', 'model_id' => 14],
            ['role_id' => 10, 'model_type' => 'App\\Models\\User', 'model_id' => 15],
            ['role_id' => 11, 'model_type' => 'App\\Models\\User', 'model_id' => 16],
            ['role_id' => 12, 'model_type' => 'App\\Models\\User', 'model_id' => 17],
            ['role_id' => 7, 'model_type' => 'App\\Models\\User', 'model_id' => 18],
            ['role_id' => 1, 'model_type' => 'App\\Models\\User', 'model_id' => 21],
            ['role_id' => 1, 'model_type' => 'App\\Models\\User', 'model_id' => 23],
            ['role_id' => 1, 'model_type' => 'App\\Models\\User', 'model_id' => 25],
            ['role_id' => 2, 'model_type' => 'App\\Models\\User', 'model_id' => 26],
            ['role_id' => 2, 'model_type' => 'App\\Models\\User', 'model_id' => 27],
            ['role_id' => 1, 'model_type' => 'App\\Models\\User', 'model_id' => 28],
            ['role_id' => 1, 'model_type' => 'App\\Models\\User', 'model_id' => 29],
            ['role_id' => 1, 'model_type' => 'App\\Models\\User', 'model_id' => 30],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('model_has_roles')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
