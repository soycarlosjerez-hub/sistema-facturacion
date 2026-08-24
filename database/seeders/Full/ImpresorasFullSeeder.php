<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImpresorasFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `impresoras` (1 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('impresoras');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('impresoras')->truncate();

        $rows = [
            ['id' => 3, 'tenant_id' => 2, 'nombre' => 'Impresora 58MM', 'tipo' => 'general', 'tipo_conexion' => 'local', 'direccion_ip' => null, 'puerto' => 9100, 'ruta_compartida' => 'USB002', 'driver' => 'escpos', 'papel_tamano' => '58mm', 'caracteres_por_linea' => 42, 'auto_imprimir_ventas' => 1, 'auto_imprimir_cotizaciones' => 0, 'auto_imprimir_conduces' => 0, 'activo' => 1, 'descripcion' => 'impresora de prueba', 'orden' => 1, 'created_at' => '2026-08-15 13:48:11', 'updated_at' => '2026-08-15 13:55:32'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('impresoras')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
