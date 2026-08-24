<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PagosInstanciaFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `pagos_instancia` (2 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('pagos_instancia');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('pagos_instancia')->truncate();

        $rows = [
            ['id' => 1, 'business_instance_id' => 5, 'plan_id' => null, 'monto' => 2000.0, 'mes_pagado' => '2026-07-01', 'fecha_pago' => '2026-07-28 14:53:40', 'metodo_pago' => 'Transferencia', 'referencia_externa' => null, 'estado_pago' => 'completado', 'notas' => 'transferido banco popular', 'registrado_por' => 3, 'created_at' => '2026-07-28 14:53:40', 'updated_at' => '2026-07-28 14:53:40'],
            ['id' => 2, 'business_instance_id' => 9, 'plan_id' => 1, 'monto' => 7500.0, 'mes_pagado' => '2026-08-01', 'fecha_pago' => '2026-08-14 17:35:14', 'metodo_pago' => 'transferencia', 'referencia_externa' => 'REGISTRO-AUTOSERVICIO', 'estado_pago' => 'pendiente', 'notas' => 'Registro autoservicio — pendiente de confirmación (implementación + primer mes)', 'registrado_por' => 30, 'created_at' => '2026-08-14 17:35:14', 'updated_at' => '2026-08-14 17:35:14'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('pagos_instancia')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
