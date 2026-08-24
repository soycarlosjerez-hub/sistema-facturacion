<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ListaPrecioLogsFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `lista_precio_logs` (1 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('lista_precio_logs');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('lista_precio_logs')->truncate();

        $rows = [
            ['id' => 1, 'tenant_id' => 2, 'lista_precio_id' => 1, 'producto_id' => 171, 'precio_anterior' => 100.0, 'precio_nuevo' => 1.0, 'usuario_id' => 8, 'cambio_en' => 'precio', 'observacion' => 'Precio actualizado vía edición masiva', 'created_at' => '2026-07-22 00:53:47', 'updated_at' => '2026-07-22 00:53:47'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('lista_precio_logs')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
