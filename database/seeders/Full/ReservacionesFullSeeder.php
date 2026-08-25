<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReservacionesFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `reservaciones` (2 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('reservaciones');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('reservaciones')->truncate();

        $rows = [
            ['id' => 23, 'mesa_id' => 13, 'cliente_id' => 207, 'cliente_nombre' => 'Carlos Perez', 'cliente_telefono' => '8091235555', 'cliente_email' => 'mrrodriguez1825@gmail.com', 'personas' => 9, 'fecha_hora' => '2026-08-07 12:00:00', 'notas' => 'Una silla para bebe', 'estado' => 'cumplida', 'user_id' => 8, 'created_at' => '2026-08-06 20:13:04', 'updated_at' => '2026-08-17 22:01:43', 'tenant_id' => 2],
            ['id' => 24, 'mesa_id' => 7, 'cliente_id' => 208, 'cliente_nombre' => 'Juan Pérez', 'cliente_telefono' => '8091234567', 'cliente_email' => 'mrrodriguez1825@gmail.com', 'personas' => 3, 'fecha_hora' => '2026-08-12 14:00:00', 'notas' => null, 'estado' => 'cumplida', 'user_id' => 8, 'created_at' => '2026-08-06 20:14:36', 'updated_at' => '2026-08-17 22:01:50', 'tenant_id' => 2],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('reservaciones')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
