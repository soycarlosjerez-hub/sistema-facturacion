<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrdenesFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `ordenes` (4 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('ordenes');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('ordenes')->truncate();

        $rows = [
            ['id' => 41, 'tenant_id' => 2, 'ncf' => null, 'ncf_tipo' => null, 'ncf_vencimiento' => null, 'tipo_comprobante' => 'ncf', 'encf' => null, 'terminal_id' => null, 'user_id' => 8, 'caja_id' => 5, 'sesion_caja_id' => 6, 'cliente_id' => 199, 'sucursal_id' => 2, 'tipo_orden' => 'pickup', 'entrega_empresa_id' => null, 'driver_id' => null, 'tracking_status' => 'pendiente', 'fecha_entrega_estimada' => null, 'fecha_entrega_real' => null, 'prueba_entrega_foto' => null, 'prueba_entrega_firma' => null, 'notas_entrega' => null, 'direccion_entrega' => null, 'telefono_contacto' => '829-546-8686', 'hora_retiro' => '2026-07-23 20:00:00', 'subtotal' => 210.0, 'impuestos' => 37.8, 'descuento' => 0.0, 'descuento_tipo' => null, 'descuento_motivo' => null, 'propina' => 0.0, 'cargo_servicio' => 0.0, 'delivery_fee' => 0.0, 'total' => 0.0, 'notas' => 'Sin cebolla', 'estado' => 'en_proceso', 'created_at' => '2026-07-23 17:49:36', 'updated_at' => '2026-07-23 17:51:19'],
            ['id' => 42, 'tenant_id' => 2, 'ncf' => null, 'ncf_tipo' => null, 'ncf_vencimiento' => null, 'tipo_comprobante' => 'ncf', 'encf' => null, 'terminal_id' => null, 'user_id' => 8, 'caja_id' => 5, 'sesion_caja_id' => 6, 'cliente_id' => 209, 'sucursal_id' => 2, 'tipo_orden' => 'pickup', 'entrega_empresa_id' => null, 'driver_id' => null, 'tracking_status' => 'pendiente', 'fecha_entrega_estimada' => null, 'fecha_entrega_real' => null, 'prueba_entrega_foto' => null, 'prueba_entrega_firma' => null, 'notas_entrega' => null, 'direccion_entrega' => null, 'telefono_contacto' => '8095551234', 'hora_retiro' => '2026-08-06 17:14:00', 'subtotal' => 1890.0, 'impuestos' => 340.2, 'descuento' => 0.0, 'descuento_tipo' => null, 'descuento_motivo' => null, 'propina' => 0.0, 'cargo_servicio' => 0.0, 'delivery_fee' => 0.0, 'total' => 0.0, 'notas' => null, 'estado' => 'pendiente', 'created_at' => '2026-08-06 20:20:45', 'updated_at' => '2026-08-06 20:20:45'],
            ['id' => 43, 'tenant_id' => 2, 'ncf' => null, 'ncf_tipo' => null, 'ncf_vencimiento' => null, 'tipo_comprobante' => 'ncf', 'encf' => null, 'terminal_id' => null, 'user_id' => 8, 'caja_id' => 5, 'sesion_caja_id' => 6, 'cliente_id' => 209, 'sucursal_id' => 2, 'tipo_orden' => 'delivery', 'entrega_empresa_id' => null, 'driver_id' => null, 'tracking_status' => 'pendiente', 'fecha_entrega_estimada' => null, 'fecha_entrega_real' => null, 'prueba_entrega_foto' => null, 'prueba_entrega_firma' => null, 'notas_entrega' => null, 'direccion_entrega' => 'Calle Santiago #234', 'telefono_contacto' => '8095551234', 'hora_retiro' => null, 'subtotal' => 27720.0, 'impuestos' => 4989.6, 'descuento' => 0.0, 'descuento_tipo' => null, 'descuento_motivo' => null, 'propina' => 0.0, 'cargo_servicio' => 0.0, 'delivery_fee' => 0.0, 'total' => 0.0, 'notas' => 'Sin camarones', 'estado' => 'pendiente', 'created_at' => '2026-08-06 20:22:14', 'updated_at' => '2026-08-06 20:22:14'],
            ['id' => 44, 'tenant_id' => 2, 'ncf' => null, 'ncf_tipo' => null, 'ncf_vencimiento' => null, 'tipo_comprobante' => 'ncf', 'encf' => null, 'terminal_id' => null, 'user_id' => 8, 'caja_id' => 5, 'sesion_caja_id' => 6, 'cliente_id' => 209, 'sucursal_id' => 2, 'tipo_orden' => 'pickup', 'entrega_empresa_id' => null, 'driver_id' => null, 'tracking_status' => 'pendiente', 'fecha_entrega_estimada' => null, 'fecha_entrega_real' => null, 'prueba_entrega_foto' => null, 'prueba_entrega_firma' => null, 'notas_entrega' => null, 'direccion_entrega' => null, 'telefono_contacto' => '8095551234', 'hora_retiro' => '2026-08-09 01:05:00', 'subtotal' => 22925.0, 'impuestos' => 4126.5, 'descuento' => 0.0, 'descuento_tipo' => null, 'descuento_motivo' => null, 'propina' => 0.0, 'cargo_servicio' => 0.0, 'delivery_fee' => 0.0, 'total' => 0.0, 'notas' => 'El bistec sin cebolla y el cerdo sin mostaza', 'estado' => 'pendiente', 'created_at' => '2026-08-09 21:46:56', 'updated_at' => '2026-08-09 21:46:57'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('ordenes')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
