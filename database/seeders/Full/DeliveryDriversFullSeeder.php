<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DeliveryDriversFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `delivery_drivers` (1 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('delivery_drivers');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('delivery_drivers')->truncate();

        $rows = [
            ['id' => 1, 'tenant_id' => 2, 'nombre' => 'Oscar', 'apellido' => 'Rodriguez', 'cedula' => '0000000000', 'telefono' => '000000000', 'whatsapp' => null, 'licencia_conducir' => null, 'activo' => 1, 'notas' => null, 'avatar_url' => null, 'created_at' => '2026-08-15 15:04:28', 'updated_at' => '2026-08-15 15:04:28'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('delivery_drivers')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
