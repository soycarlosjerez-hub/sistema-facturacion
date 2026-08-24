<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ListaPreciosFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `lista_precios` (1 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('lista_precios');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('lista_precios')->truncate();

        $rows = [
            ['id' => 1, 'codigo' => 'CORP', 'nombre' => 'CORPORATIVO', 'descripcion' => 'PRECIOS ESPECIALES PARA CORPORACIONES', 'vigencia_desde' => '2026-07-22', 'vigencia_hasta' => '2030-01-22', 'activa' => 1, 'created_at' => '2026-07-21 22:55:07', 'updated_at' => '2026-07-21 22:55:07', 'deleted_at' => null, 'tenant_id' => 2],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('lista_precios')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
