<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PasswordResetTokensFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `password_reset_tokens` (1 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('password_reset_tokens');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('password_reset_tokens')->truncate();

        $rows = [
            ['email' => 'warcold@gmail.com', 'token' => '$2y$12$nAaQxTQ6ZZQ5dJgTecEuoeZNk25yGkExFWRfG5aDsEOAAc5b2UKFS', 'created_at' => '2026-07-23 17:20:44'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('password_reset_tokens')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
