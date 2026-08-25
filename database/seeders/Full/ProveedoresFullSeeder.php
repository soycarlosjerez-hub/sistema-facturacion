<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProveedoresFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `proveedores` (14 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('proveedores');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('proveedores')->truncate();

        $rows = [
            ['id' => 1, 'nombre' => 'Cervecería Nacional Dominicana', 'rnc_cedula' => null, 'telefono' => '809-483-5000', 'email' => 'contacto@cnd.com.do', 'activo' => 1, 'direccion' => 'Av. Independencia Km 6 1/2, Santo Domingo', 'rnc' => '131-00001-1', 'tipo_persona' => 'juridica', 'sujeto_retencion_isr' => 1, 'sujeto_retencion_itbis' => 1, 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 2, 'nombre' => 'MERCASID', 'rnc_cedula' => null, 'telefono' => '809-565-2151', 'email' => 'info@sid.com.do', 'activo' => 1, 'direccion' => 'Av. Máximo Gómez #182, Santo Domingo', 'rnc' => '131-00002-2', 'tipo_persona' => 'juridica', 'sujeto_retencion_isr' => 1, 'sujeto_retencion_itbis' => 1, 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 3, 'nombre' => 'INDUVECA', 'rnc_cedula' => null, 'telefono' => '809-573-3151', 'email' => 'ventas@induveca.com.do', 'activo' => 1, 'direccion' => 'Av. Pedro A. Rivera, La Vega', 'rnc' => '131-00003-3', 'tipo_persona' => 'juridica', 'sujeto_retencion_isr' => 1, 'sujeto_retencion_itbis' => 1, 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 4, 'nombre' => 'Pasteurizadora RICA', 'rnc_cedula' => null, 'telefono' => '809-567-4411', 'email' => 'servicio@rica.com.do', 'activo' => 1, 'direccion' => 'Av. Máximo Gómez #182, Santo Domingo', 'rnc' => '131-00004-4', 'tipo_persona' => 'juridica', 'sujeto_retencion_isr' => 1, 'sujeto_retencion_itbis' => 1, 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 5, 'nombre' => 'Nestlé Dominicana', 'rnc_cedula' => null, 'telefono' => '809-508-5100', 'email' => 'consumer.services@do.nestle.com', 'activo' => 1, 'direccion' => 'Av. Abraham Lincoln #118, Santo Domingo', 'rnc' => '131-00005-5', 'tipo_persona' => 'juridica', 'sujeto_retencion_isr' => 1, 'sujeto_retencion_itbis' => 1, 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 6, 'nombre' => 'Molinos Modernos', 'rnc_cedula' => null, 'telefono' => '809-594-1515', 'email' => 'info@molinosmodernos.com', 'activo' => 1, 'direccion' => 'Av. España, Santo Domingo Este', 'rnc' => '131-00006-6', 'tipo_persona' => 'juridica', 'sujeto_retencion_isr' => 1, 'sujeto_retencion_itbis' => 1, 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 7, 'nombre' => 'Distribuidora Corripio', 'rnc_cedula' => null, 'telefono' => '809-227-3100', 'email' => 'ventas@corripio.com.do', 'activo' => 1, 'direccion' => 'Av. Núñez de Cáceres, Santo Domingo', 'rnc' => '131-00007-7', 'tipo_persona' => 'juridica', 'sujeto_retencion_isr' => 1, 'sujeto_retencion_itbis' => 1, 'created_at' => null, 'updated_at' => null, 'tenant_id' => null],
            ['id' => 8, 'nombre' => 'test', 'rnc_cedula' => null, 'telefono' => null, 'email' => null, 'activo' => 1, 'direccion' => null, 'rnc' => null, 'tipo_persona' => 'juridica', 'sujeto_retencion_isr' => 1, 'sujeto_retencion_itbis' => 1, 'created_at' => '2026-07-01 15:10:29', 'updated_at' => '2026-07-01 15:10:29', 'tenant_id' => 1],
            ['id' => 10, 'nombre' => 'SUPERMERCADOS BRAVO', 'rnc_cedula' => null, 'telefono' => null, 'email' => null, 'activo' => 1, 'direccion' => null, 'rnc' => null, 'tipo_persona' => 'juridica', 'sujeto_retencion_isr' => 1, 'sujeto_retencion_itbis' => 1, 'created_at' => '2026-07-02 20:34:10', 'updated_at' => '2026-07-02 20:34:10', 'tenant_id' => 2],
            ['id' => 12, 'nombre' => 'unirefri', 'rnc_cedula' => null, 'telefono' => '8095761606', 'email' => null, 'activo' => 1, 'direccion' => null, 'rnc' => null, 'tipo_persona' => 'juridica', 'sujeto_retencion_isr' => 1, 'sujeto_retencion_itbis' => 1, 'created_at' => '2026-07-23 22:55:18', 'updated_at' => '2026-07-23 22:55:18', 'tenant_id' => 4],
            ['id' => 13, 'nombre' => 'Almacenes', 'rnc_cedula' => null, 'telefono' => '8492053525', 'email' => null, 'activo' => 1, 'direccion' => null, 'rnc' => null, 'tipo_persona' => 'juridica', 'sujeto_retencion_isr' => 1, 'sujeto_retencion_itbis' => 1, 'created_at' => '2026-08-12 14:59:52', 'updated_at' => '2026-08-12 14:59:52', 'tenant_id' => 7],
            ['id' => 14, 'nombre' => 'PROVEEDOR TEST', 'rnc_cedula' => null, 'telefono' => '809-555-0100', 'email' => null, 'activo' => 1, 'direccion' => null, 'rnc' => '1', 'tipo_persona' => 'juridica', 'sujeto_retencion_isr' => 1, 'sujeto_retencion_itbis' => 1, 'created_at' => '2026-08-14 17:56:52', 'updated_at' => '2026-08-14 17:56:52', 'tenant_id' => 9],
            ['id' => 15, 'nombre' => 'SUPERMERCADO NACIONAL', 'rnc_cedula' => null, 'telefono' => null, 'email' => null, 'activo' => 1, 'direccion' => null, 'rnc' => null, 'tipo_persona' => 'juridica', 'sujeto_retencion_isr' => 0, 'sujeto_retencion_itbis' => 0, 'created_at' => '2026-08-17 21:43:52', 'updated_at' => '2026-08-17 21:43:52', 'tenant_id' => 2],
            ['id' => 16, 'nombre' => 'HIPERMERCADOS OLE', 'rnc_cedula' => null, 'telefono' => null, 'email' => null, 'activo' => 1, 'direccion' => null, 'rnc' => null, 'tipo_persona' => 'juridica', 'sujeto_retencion_isr' => 0, 'sujeto_retencion_itbis' => 0, 'created_at' => '2026-08-17 21:46:46', 'updated_at' => '2026-08-17 21:46:46', 'tenant_id' => 2],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('proveedores')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
