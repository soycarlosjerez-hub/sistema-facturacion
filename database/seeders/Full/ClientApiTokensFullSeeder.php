<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClientApiTokensFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `client_api_tokens` (2 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('client_api_tokens');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('client_api_tokens')->truncate();

        $rows = [
            ['id' => 1, 'cliente_id' => 199, 'name' => 'auth-token', 'token' => '7aaed41336cae5c42e844edec39106c7a2e53a4421c91b6e32919613d274b961', 'abilities' => '["*"]', 'last_used_at' => null, 'expires_at' => null, 'created_at' => '2026-07-23 17:47:20', 'updated_at' => '2026-07-23 17:47:20'],
            ['id' => 2, 'cliente_id' => 206, 'name' => 'auth-token', 'token' => 'a255a80c28b10d7ccd732001e724cd383db66ffc4b66d020deff10ffde4b8a37', 'abilities' => '["*"]', 'last_used_at' => null, 'expires_at' => null, 'created_at' => '2026-08-05 20:57:10', 'updated_at' => '2026-08-05 20:57:10'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('client_api_tokens')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
