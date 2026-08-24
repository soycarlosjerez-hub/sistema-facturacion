<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PersonalAccessTokensFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `personal_access_tokens` (2 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('personal_access_tokens');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('personal_access_tokens')->truncate();

        $rows = [
            ['id' => 1, 'tokenable_type' => 'App\\Models\\User', 'tokenable_id' => 4, 'name' => 'web-site', 'token' => '048cb056c7b9f6d8ca89844fbf85e7e969b6b5c9d650dcfed78aab060d9d668d', 'abilities' => '["*"]', 'last_used_at' => '2026-07-03 14:04:37', 'expires_at' => null, 'created_at' => '2026-07-01 15:19:12', 'updated_at' => '2026-07-03 14:04:37'],
            ['id' => 2, 'tokenable_type' => 'App\\Models\\User', 'tokenable_id' => 8, 'name' => 'web-sites', 'token' => 'e17e1558993d2187cfe3be45225d3cb07f0f45660f9e63425fc4601c77c3eca2', 'abilities' => '["*"]', 'last_used_at' => '2026-07-05 01:17:52', 'expires_at' => null, 'created_at' => '2026-07-02 17:00:45', 'updated_at' => '2026-07-05 01:17:52'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('personal_access_tokens')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
