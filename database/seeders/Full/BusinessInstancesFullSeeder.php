<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BusinessInstancesFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `business_instances` (8 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('business_instances');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('business_instances')->truncate();

        $rows = [
            ['id' => 1, 'nombre' => 'Restaurante Ejemplo SRL', 'slug' => 'restaurante-ejemplo', 'rnc' => '123456789', 'email' => 'contacto@restaurante-ejemplo.com', 'telefono' => '809-555-0100', 'direccion' => 'Calle Principal #123, Santo Domingo', 'business_type_id' => 1, 'plan_id' => null, 'owner_user_id' => 3, 'owner_email' => 'owner@sistema-facturacion.com', 'owner_nombre' => 'Dueño del Sistema', 'configuracion' => '{"slogan": "La mejor experiencia culinaria", "moneda_simbolo": "RD$", "nombre_empresa": "Restaurante Ejemplo SRL", "itbis_porcentaje": 18}', 'costo_mensual' => 5000.0, 'bloqueado' => 0, 'setup_completed' => 1, 'deleted_at' => '2026-07-22 18:02:52', 'motivo_bloqueo' => null, 'bloqueado_en' => null, 'activo' => 1, 'fecha_vencimiento' => '2027-07-01 15:04:05', 'created_at' => '2026-07-01 15:04:05', 'updated_at' => '2026-08-04 12:49:49'],
            ['id' => 2, 'nombre' => 'Michelle Casero y Gourmet', 'slug' => 'michelle-casero-y-gourmet', 'rnc' => null, 'email' => 'jhonattan0302@gmail.com', 'telefono' => null, 'direccion' => null, 'business_type_id' => 1, 'plan_id' => 4, 'owner_user_id' => 27, 'owner_email' => 'asimismo@armada.do', 'owner_nombre' => 'asimismo@armada.do', 'configuracion' => '{"enabled": "1", "sale_paid": "1", "order_ready": "1", "credit_abono": "1", "sale_created": "1", "shift_closed": "1", "shift_opened": "1", "backup_failed": "1", "cash_shortage": "1", "ncff_expiring": "1", "order_shipped": "1", "credit_overdue": "1", "sale_cancelled": "1", "stock_critical": "1", "order_confirmed": "1", "user_registered": "1", "payment_received": "1", "ecf_certificate_expiring": "1", "restaurante_valida_stock": "0"}', 'costo_mensual' => 6000.0, 'bloqueado' => 0, 'setup_completed' => 1, 'deleted_at' => null, 'motivo_bloqueo' => null, 'bloqueado_en' => null, 'activo' => 1, 'fecha_vencimiento' => null, 'created_at' => '2026-07-02 16:55:57', 'updated_at' => '2026-08-17 13:49:55'],
            ['id' => 3, 'nombre' => 'Simon', 'slug' => 'simon', 'rnc' => null, 'email' => null, 'telefono' => null, 'direccion' => null, 'business_type_id' => 1, 'plan_id' => null, 'owner_user_id' => 3, 'owner_email' => 'owner@sistema-facturacion.com', 'owner_nombre' => 'Dueño del Sistema', 'configuracion' => '[]', 'costo_mensual' => null, 'bloqueado' => 0, 'setup_completed' => 0, 'deleted_at' => '2026-07-22 18:02:57', 'motivo_bloqueo' => null, 'bloqueado_en' => null, 'activo' => 1, 'fecha_vencimiento' => null, 'created_at' => '2026-07-07 20:54:29', 'updated_at' => '2026-08-04 12:49:49'],
            ['id' => 4, 'nombre' => 'Climatizació HVAC', 'slug' => 'climatizaci-hvac', 'rnc' => null, 'email' => null, 'telefono' => null, 'direccion' => null, 'business_type_id' => 9, 'plan_id' => 1, 'owner_user_id' => 26, 'owner_email' => 'jcjerez@gmail.com', 'owner_nombre' => 'JUAN CARLOS JEREZ GOMEZ', 'configuracion' => '[]', 'costo_mensual' => 2000.0, 'bloqueado' => 0, 'setup_completed' => 1, 'deleted_at' => null, 'motivo_bloqueo' => null, 'bloqueado_en' => null, 'activo' => 1, 'fecha_vencimiento' => null, 'created_at' => '2026-07-22 23:03:15', 'updated_at' => '2026-08-12 10:08:53'],
            ['id' => 5, 'nombre' => 'Tecno Plus RD', 'slug' => 'tecno-plus', 'rnc' => null, 'email' => 'tecnoplusrd@gmail.com', 'telefono' => null, 'direccion' => null, 'business_type_id' => 10, 'plan_id' => 1, 'owner_user_id' => 26, 'owner_email' => 'jcjerez@gmail.com', 'owner_nombre' => 'JUAN CARLOS JEREZ GOMEZ', 'configuracion' => '{"dias_credito": "30", "moneda_simbolo": "RD$", "nombre_empresa": "Tecno Plus RD", "prefijo_factura": "FAC", "itbis_porcentaje": "18", "restaurante_valida_stock": "1"}', 'costo_mensual' => 2000.0, 'bloqueado' => 0, 'setup_completed' => 1, 'deleted_at' => null, 'motivo_bloqueo' => null, 'bloqueado_en' => null, 'activo' => 1, 'fecha_vencimiento' => '2027-07-27 22:00:00', 'created_at' => '2026-07-23 10:44:02', 'updated_at' => '2026-08-12 10:08:21'],
            ['id' => 7, 'nombre' => 'Colmado Rodriguez', 'slug' => 'colmado-rodriguez', 'rnc' => null, 'email' => null, 'telefono' => null, 'direccion' => null, 'business_type_id' => 2, 'plan_id' => 1, 'owner_user_id' => 26, 'owner_email' => 'jcjerez@gmail.com', 'owner_nombre' => 'JUAN CARLOS JEREZ GOMEZ', 'configuracion' => '[]', 'costo_mensual' => 2000.0, 'bloqueado' => 0, 'setup_completed' => 1, 'deleted_at' => null, 'motivo_bloqueo' => null, 'bloqueado_en' => null, 'activo' => 1, 'fecha_vencimiento' => '2027-12-03 23:00:00', 'created_at' => '2026-08-03 23:42:35', 'updated_at' => '2026-08-12 15:01:37'],
            ['id' => 8, 'nombre' => 'Armada Shop', 'slug' => 'armadas-shop', 'rnc' => null, 'email' => 'asimismo@armada.do', 'telefono' => '+1 (829) 546-8686', 'direccion' => null, 'business_type_id' => 11, 'plan_id' => 1, 'owner_user_id' => 27, 'owner_email' => 'asimismo@armada.do', 'owner_nombre' => 'asimismo@armada.do', 'configuracion' => '{"enabled": "1", "sale_paid": "1", "order_ready": "1", "credit_abono": "1", "sale_created": "1", "shift_closed": "1", "shift_opened": "1", "backup_failed": "1", "cash_shortage": "1", "ncff_expiring": "1", "order_shipped": "1", "credit_overdue": "1", "sale_cancelled": "1", "stock_critical": "1", "order_confirmed": "1", "user_registered": "1", "payment_received": "1", "impresora_papel_default": "80mm", "ecf_certificate_expiring": "1", "restaurante_valida_stock": "1"}', 'costo_mensual' => 2000.0, 'bloqueado' => 0, 'setup_completed' => 0, 'deleted_at' => null, 'motivo_bloqueo' => null, 'bloqueado_en' => null, 'activo' => 1, 'fecha_vencimiento' => '2027-10-19 22:00:00', 'created_at' => '2026-08-04 11:09:12', 'updated_at' => '2026-08-12 11:09:53'],
            ['id' => 9, 'nombre' => 'Arte', 'slug' => 'arte', 'rnc' => '0232152', 'email' => 'soycarlosjerez@gmail.com', 'telefono' => '8097507255', 'direccion' => 'Calle Maximo Gomez', 'business_type_id' => 12, 'plan_id' => 1, 'owner_user_id' => 26, 'owner_email' => 'jcjerez@gmail.com', 'owner_nombre' => 'JUAN CARLOS JEREZ GOMEZ', 'configuracion' => '[]', 'costo_mensual' => 2000.0, 'bloqueado' => 0, 'setup_completed' => 1, 'deleted_at' => null, 'motivo_bloqueo' => null, 'bloqueado_en' => null, 'activo' => 1, 'fecha_vencimiento' => '2026-09-13 22:00:00', 'created_at' => '2026-08-14 17:35:14', 'updated_at' => '2026-08-17 13:49:38'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('business_instances')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
