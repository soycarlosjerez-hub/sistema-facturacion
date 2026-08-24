<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HistorialImpresionFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `historial_impresion` (13 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('historial_impresion');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('historial_impresion')->truncate();

        $rows = [
            ['id' => 1, 'tenant_id' => 2, 'imprimible_type' => 'App\\Models\\Venta', 'imprimible_id' => 52, 'impresora_id' => null, 'user_id' => 8, 'tipo_documento' => 'venta', 'documento_numero' => '#000052', 'formato' => 'ticket', 'copias' => 1, 'papel_tamano' => '80mm', 'exitoso' => 0, 'error_mensaje' => 'fopen(/dev/usb/lp0): Failed to open stream: No such file or directory', 'tamanio_bytes' => 785, 'created_at' => '2026-08-09 21:03:20', 'updated_at' => '2026-08-09 21:03:20'],
            ['id' => 2, 'tenant_id' => 2, 'imprimible_type' => 'App\\Models\\Venta', 'imprimible_id' => 53, 'impresora_id' => null, 'user_id' => 28, 'tipo_documento' => 'venta', 'documento_numero' => '#000053', 'formato' => 'ticket', 'copias' => 1, 'papel_tamano' => '80mm', 'exitoso' => 0, 'error_mensaje' => 'fopen(/dev/usb/lp0): Failed to open stream: No such file or directory', 'tamanio_bytes' => 727, 'created_at' => '2026-08-10 13:07:32', 'updated_at' => '2026-08-10 13:07:32'],
            ['id' => 3, 'tenant_id' => 2, 'imprimible_type' => 'App\\Models\\Venta', 'imprimible_id' => 53, 'impresora_id' => null, 'user_id' => 28, 'tipo_documento' => 'venta', 'documento_numero' => '#000053', 'formato' => 'ticket', 'copias' => 1, 'papel_tamano' => '80mm', 'exitoso' => 0, 'error_mensaje' => 'fopen(/dev/usb/lp0): Failed to open stream: No such file or directory', 'tamanio_bytes' => 727, 'created_at' => '2026-08-10 13:07:41', 'updated_at' => '2026-08-10 13:07:41'],
            ['id' => 4, 'tenant_id' => 2, 'imprimible_type' => 'App\\Models\\Venta', 'imprimible_id' => 54, 'impresora_id' => null, 'user_id' => 28, 'tipo_documento' => 'venta', 'documento_numero' => '#000054', 'formato' => 'ticket', 'copias' => 1, 'papel_tamano' => '80mm', 'exitoso' => 0, 'error_mensaje' => 'fopen(/dev/usb/lp0): Failed to open stream: No such file or directory', 'tamanio_bytes' => 727, 'created_at' => '2026-08-10 22:04:16', 'updated_at' => '2026-08-10 22:04:16'],
            ['id' => 5, 'tenant_id' => 2, 'imprimible_type' => 'App\\Models\\Venta', 'imprimible_id' => 56, 'impresora_id' => null, 'user_id' => 8, 'tipo_documento' => 'venta', 'documento_numero' => '#000056', 'formato' => 'ticket', 'copias' => 1, 'papel_tamano' => '80mm', 'exitoso' => 0, 'error_mensaje' => 'fopen(/dev/usb/lp0): Failed to open stream: No such file or directory', 'tamanio_bytes' => 808, 'created_at' => '2026-08-14 18:00:10', 'updated_at' => '2026-08-14 18:00:10'],
            ['id' => 6, 'tenant_id' => 2, 'imprimible_type' => 'prueba', 'imprimible_id' => null, 'impresora_id' => 3, 'user_id' => 8, 'tipo_documento' => 'prueba', 'documento_numero' => null, 'formato' => 'ticket', 'copias' => 1, 'papel_tamano' => null, 'exitoso' => 1, 'error_mensaje' => null, 'tamanio_bytes' => null, 'created_at' => '2026-08-15 13:51:12', 'updated_at' => '2026-08-15 13:51:12'],
            ['id' => 7, 'tenant_id' => 2, 'imprimible_type' => 'prueba', 'imprimible_id' => null, 'impresora_id' => 3, 'user_id' => 8, 'tipo_documento' => 'prueba', 'documento_numero' => null, 'formato' => 'ticket', 'copias' => 1, 'papel_tamano' => null, 'exitoso' => 1, 'error_mensaje' => null, 'tamanio_bytes' => null, 'created_at' => '2026-08-15 13:51:49', 'updated_at' => '2026-08-15 13:51:49'],
            ['id' => 8, 'tenant_id' => 2, 'imprimible_type' => 'prueba', 'imprimible_id' => null, 'impresora_id' => 3, 'user_id' => 8, 'tipo_documento' => 'prueba', 'documento_numero' => null, 'formato' => 'ticket', 'copias' => 1, 'papel_tamano' => null, 'exitoso' => 1, 'error_mensaje' => null, 'tamanio_bytes' => null, 'created_at' => '2026-08-15 13:53:35', 'updated_at' => '2026-08-15 13:53:35'],
            ['id' => 9, 'tenant_id' => 2, 'imprimible_type' => 'prueba', 'imprimible_id' => null, 'impresora_id' => 3, 'user_id' => 8, 'tipo_documento' => 'prueba', 'documento_numero' => null, 'formato' => 'ticket', 'copias' => 1, 'papel_tamano' => null, 'exitoso' => 1, 'error_mensaje' => null, 'tamanio_bytes' => null, 'created_at' => '2026-08-15 13:54:43', 'updated_at' => '2026-08-15 13:54:43'],
            ['id' => 10, 'tenant_id' => 2, 'imprimible_type' => 'prueba', 'imprimible_id' => null, 'impresora_id' => 3, 'user_id' => 8, 'tipo_documento' => 'prueba', 'documento_numero' => null, 'formato' => 'ticket', 'copias' => 1, 'papel_tamano' => null, 'exitoso' => 1, 'error_mensaje' => null, 'tamanio_bytes' => null, 'created_at' => '2026-08-15 13:55:35', 'updated_at' => '2026-08-15 13:55:35'],
            ['id' => 11, 'tenant_id' => 2, 'imprimible_type' => 'prueba', 'imprimible_id' => null, 'impresora_id' => 3, 'user_id' => 8, 'tipo_documento' => 'prueba', 'documento_numero' => null, 'formato' => 'ticket', 'copias' => 1, 'papel_tamano' => null, 'exitoso' => 1, 'error_mensaje' => null, 'tamanio_bytes' => null, 'created_at' => '2026-08-15 13:58:34', 'updated_at' => '2026-08-15 13:58:34'],
            ['id' => 12, 'tenant_id' => 2, 'imprimible_type' => 'App\\Models\\Venta', 'imprimible_id' => 57, 'impresora_id' => null, 'user_id' => 8, 'tipo_documento' => 'venta', 'documento_numero' => '#000057', 'formato' => 'ticket', 'copias' => 1, 'papel_tamano' => '80mm', 'exitoso' => 0, 'error_mensaje' => 'Cannot initialise NetworkPrintConnector: Connection refused', 'tamanio_bytes' => 743, 'created_at' => '2026-08-15 14:14:45', 'updated_at' => '2026-08-15 14:14:45'],
            ['id' => 13, 'tenant_id' => 2, 'imprimible_type' => 'App\\Models\\Venta', 'imprimible_id' => 57, 'impresora_id' => null, 'user_id' => 8, 'tipo_documento' => 'venta', 'documento_numero' => '#000057', 'formato' => 'ticket', 'copias' => 1, 'papel_tamano' => '80mm', 'exitoso' => 0, 'error_mensaje' => 'Cannot initialise NetworkPrintConnector: Connection refused', 'tamanio_bytes' => 743, 'created_at' => '2026-08-15 14:15:00', 'updated_at' => '2026-08-15 14:15:00'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('historial_impresion')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
