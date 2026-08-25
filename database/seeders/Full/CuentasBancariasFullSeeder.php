<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CuentasBancariasFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `cuentas_bancarias` (2 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('cuentas_bancarias');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('cuentas_bancarias')->truncate();

        $rows = [
            ['id' => 1, 'nombre' => 'Banco Popular', 'banco' => 'banco popular', 'tipo_cuenta' => 'ahorros', 'numero_cuenta' => '1238754632121', 'moneda' => 'RD', 'titular' => 'test', 'cedula_ruc' => '03104422229', 'saldo_inicial' => 0.0, 'saldo_actual' => 0.0, 'activo' => 0, 'tenant_id' => 2, 'created_at' => '2026-07-08 10:53:34', 'updated_at' => '2026-07-08 10:53:34'],
            ['id' => 3, 'nombre' => 'CUENTA MARIA', 'banco' => 'Banco Popular Dominicano', 'tipo_cuenta' => 'ahorros', 'numero_cuenta' => '0829057090', 'moneda' => 'RD', 'titular' => 'MARIA RODRIGUEZ', 'cedula_ruc' => null, 'saldo_inicial' => 0.0, 'saldo_actual' => 0.0, 'activo' => 1, 'tenant_id' => 2, 'created_at' => '2026-07-09 11:59:27', 'updated_at' => '2026-07-09 11:59:27'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('cuentas_bancarias')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
