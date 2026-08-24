<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DeliveryZonesFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `delivery_zones` (1 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('delivery_zones');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('delivery_zones')->truncate();

        $rows = [
            ['id' => 1, 'tenant_id' => 2, 'nombre' => 'Gazcue', 'descripcion' => 'Serca del restaurante', 'radio_km' => 5.0, 'tarifa_base' => 0.0, 'tarifa_por_km' => 0.0, 'tiempo_estimado_minutos' => 30, 'zona_poligono' => null, 'minimo_para_envio_gratis' => 25.0, 'activo' => 1, 'created_at' => '2026-08-15 15:05:30', 'updated_at' => '2026-08-15 15:05:30'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('delivery_zones')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
