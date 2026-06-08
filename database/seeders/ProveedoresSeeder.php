<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProveedoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar tabla antes de sembrar
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('proveedores')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('proveedores')->insert([
            [
                'nombre' => 'Cervecería Nacional Dominicana',
                'telefono' => '809-483-5000',
                'email' => 'contacto@cnd.com.do',
                'direccion' => 'Av. Independencia Km 6 1/2, Santo Domingo',
                'rnc' => '1' . str_pad((string)rand(0, 9999999999), 9, '0', STR_PAD_LEFT),
                'tipo_persona' => 'juridica',
                'sujeto_retencion_isr' => true,
                'sujeto_retencion_itbis' => true,
            ],
            [
                'nombre' => 'MERCASID',
                'telefono' => '809-565-2151',
                'email' => 'info@sid.com.do',
                'direccion' => 'Av. Máximo Gómez #182, Santo Domingo',
                'rnc' => '1' . str_pad((string)rand(0, 9999999999), 9, '0', STR_PAD_LEFT),
                'tipo_persona' => 'juridica',
                'sujeto_retencion_isr' => true,
                'sujeto_retencion_itbis' => true,
            ],
            [
                'nombre' => 'INDUVECA',
                'telefono' => '809-573-3151',
                'email' => 'ventas@induveca.com.do',
                'direccion' => 'Av. Pedro A. Rivera, La Vega',
                'rnc' => '1' . str_pad((string)rand(0, 9999999999), 9, '0', STR_PAD_LEFT),
                'tipo_persona' => 'juridica',
                'sujeto_retencion_isr' => true,
                'sujeto_retencion_itbis' => true,
            ],
            [
                'nombre' => 'Pasteurizadora RICA',
                'telefono' => '809-567-4411',
                'email' => 'servicio@rica.com.do',
                'direccion' => 'Av. Máximo Gómez #182, Santo Domingo',
                'rnc' => '1' . str_pad((string)rand(0, 9999999999), 9, '0', STR_PAD_LEFT),
                'tipo_persona' => 'juridica',
                'sujeto_retencion_isr' => true,
                'sujeto_retencion_itbis' => true,
            ],
            [
                'nombre' => 'Nestlé Dominicana',
                'telefono' => '809-508-5100',
                'email' => 'consumer.services@do.nestle.com',
                'direccion' => 'Av. Abraham Lincoln #118, Santo Domingo',
                'rnc' => '1' . str_pad((string)rand(0, 9999999999), 9, '0', STR_PAD_LEFT),
                'tipo_persona' => 'juridica',
                'sujeto_retencion_isr' => true,
                'sujeto_retencion_itbis' => true,
            ],
            [
                'nombre' => 'Molinos Modernos',
                'telefono' => '809-594-1515',
                'email' => 'info@molinosmodernos.com',
                'direccion' => 'Av. España, Santo Domingo Este',
                'rnc' => '1' . str_pad((string)rand(0, 9999999999), 9, '0', STR_PAD_LEFT),
                'tipo_persona' => 'juridica',
                'sujeto_retencion_isr' => true,
                'sujeto_retencion_itbis' => true,
            ],
            [
                'nombre' => 'Distribuidora Corripio',
                'telefono' => '809-227-3100',
                'email' => 'ventas@corripio.com.do',
                'direccion' => 'Av. Núñez de Cáceres, Santo Domingo',
                'rnc' => '1' . str_pad((string)rand(0, 9999999999), 9, '0', STR_PAD_LEFT),
                'tipo_persona' => 'juridica',
                'sujeto_retencion_isr' => true,
                'sujeto_retencion_itbis' => true,
            ],
        ]);
    }
}
