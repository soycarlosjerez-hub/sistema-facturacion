<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EcfLogEnviosFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `ecf_log_envios` (10 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('ecf_log_envios');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('ecf_log_envios')->truncate();

        $rows = [
            ['id' => 1, 'ecf_documento_id' => 1, 'accion' => 'crear', 'estado_resultado' => 'exito', 'codigo_http' => null, 'request_payload' => null, 'response_payload' => null, 'mensaje' => 'e-CF generado en estado borrador', 'duracion_ms' => null, 'created_at' => '2026-07-12 21:52:25', 'tenant_id' => 2],
            ['id' => 2, 'ecf_documento_id' => 1, 'accion' => 'firmar', 'estado_resultado' => 'exito', 'codigo_http' => null, 'request_payload' => '{"metodo":"simulado","serial":"SIM-B24E699AD5FC27BC"}', 'response_payload' => null, 'mensaje' => 'Firmado con simulado (algoritmo: SHA256-SIMULATED)', 'duracion_ms' => null, 'created_at' => '2026-07-12 21:52:25', 'tenant_id' => 2],
            ['id' => 3, 'ecf_documento_id' => 1, 'accion' => 'enviar', 'estado_resultado' => 'exito', 'codigo_http' => 200, 'request_payload' => null, 'response_payload' => '{"trackId":"TRK-1F3696D8DF4766A2","estado":"ACEPTADO","codigoSeguridad":"DB502A"}', 'mensaje' => 'e-CF recibido y aceptado por DGII (simulación)', 'duracion_ms' => 200, 'created_at' => '2026-07-12 21:52:25', 'tenant_id' => 2],
            ['id' => 4, 'ecf_documento_id' => 2, 'accion' => 'crear', 'estado_resultado' => 'exito', 'codigo_http' => null, 'request_payload' => null, 'response_payload' => null, 'mensaje' => 'e-CF generado en estado borrador', 'duracion_ms' => null, 'created_at' => '2026-07-21 10:59:34', 'tenant_id' => 2],
            ['id' => 5, 'ecf_documento_id' => 2, 'accion' => 'firmar', 'estado_resultado' => 'exito', 'codigo_http' => null, 'request_payload' => '{"metodo":"simulado","serial":"SIM-601756EB5AC48AC0"}', 'response_payload' => null, 'mensaje' => 'Firmado con simulado (algoritmo: SHA256-SIMULATED)', 'duracion_ms' => null, 'created_at' => '2026-07-21 10:59:34', 'tenant_id' => 2],
            ['id' => 6, 'ecf_documento_id' => 2, 'accion' => 'enviar', 'estado_resultado' => 'exito', 'codigo_http' => 200, 'request_payload' => null, 'response_payload' => '{"trackId":"TRK-B3806D61B784FF10","estado":"ACEPTADO","codigoSeguridad":"46A054"}', 'mensaje' => 'e-CF recibido y aceptado por DGII (simulación)', 'duracion_ms' => 200, 'created_at' => '2026-07-21 10:59:34', 'tenant_id' => 2],
            ['id' => 7, 'ecf_documento_id' => 3, 'accion' => 'crear', 'estado_resultado' => 'exito', 'codigo_http' => null, 'request_payload' => null, 'response_payload' => null, 'mensaje' => 'e-CF generado en estado borrador', 'duracion_ms' => null, 'created_at' => '2026-08-10 22:04:05', 'tenant_id' => 2],
            ['id' => 8, 'ecf_documento_id' => 3, 'accion' => 'firmar', 'estado_resultado' => 'error', 'codigo_http' => null, 'request_payload' => null, 'response_payload' => null, 'mensaje' => 'DOMXPath::query(): Undefined namespace prefix', 'duracion_ms' => null, 'created_at' => '2026-08-10 22:04:05', 'tenant_id' => 2],
            ['id' => 9, 'ecf_documento_id' => 3, 'accion' => 'firmar', 'estado_resultado' => 'error', 'codigo_http' => null, 'request_payload' => null, 'response_payload' => null, 'mensaje' => 'Transición inválida: firmado → firmado. Transiciones permitidas desde firmado: enviado', 'duracion_ms' => null, 'created_at' => '2026-08-10 22:04:09', 'tenant_id' => 2],
            ['id' => 10, 'ecf_documento_id' => 3, 'accion' => 'firmar', 'estado_resultado' => 'error', 'codigo_http' => null, 'request_payload' => null, 'response_payload' => null, 'mensaje' => 'Transición inválida: firmado → firmado. Transiciones permitidas desde firmado: enviado', 'duracion_ms' => null, 'created_at' => '2026-08-10 22:04:12', 'tenant_id' => 2],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('ecf_log_envios')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
