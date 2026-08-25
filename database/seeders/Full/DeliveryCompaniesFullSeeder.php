<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DeliveryCompaniesFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `delivery_companies` (6 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('delivery_companies');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('delivery_companies')->truncate();

        $rows = [
            ['id' => 1, 'tenant_id' => null, 'nombre' => 'Uber Eats', 'nombre_corto' => 'uber_eats', 'comision_porcentaje' => 30.0, 'activo' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 2, 'tenant_id' => null, 'nombre' => 'PedidosYa', 'nombre_corto' => 'pedidos_ya', 'comision_porcentaje' => 25.0, 'activo' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 3, 'tenant_id' => null, 'nombre' => 'Didi Food', 'nombre_corto' => 'didi_food', 'comision_porcentaje' => 22.0, 'activo' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 4, 'tenant_id' => null, 'nombre' => 'DoorDash', 'nombre_corto' => 'door_dash', 'comision_porcentaje' => 25.0, 'activo' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 5, 'tenant_id' => null, 'nombre' => 'Glovo', 'nombre_corto' => 'glovo', 'comision_porcentaje' => 28.0, 'activo' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 6, 'tenant_id' => null, 'nombre' => 'Otro', 'nombre_corto' => 'otro', 'comision_porcentaje' => 0.0, 'activo' => 1, 'created_at' => null, 'updated_at' => null],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('delivery_companies')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
