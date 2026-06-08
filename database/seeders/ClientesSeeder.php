<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar tabla antes de sembrar
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('clientes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('clientes')->insert([
            [
                'nombre' => 'Consumidor Final',
                'telefono' => 'N/A',
                'email' => 'cf@sistema.com',
                'direccion' => 'N/A',
                'rnc_cedula' => '00000000000',
                'limite_credito' => 0.00,
                'balance_pendiente' => 0.00,
                'activo' => 1,
            ],
            [
                'nombre' => 'Doña Tatica',
                'telefono' => '809-555-0101',
                'email' => 'tatica@vecina.com',
                'direccion' => 'Casa #4, Calle Principal',
                'rnc_cedula' => '001-1234567-8',
                'limite_credito' => 5000.00,
                'balance_pendiente' => 2450.00,
                'activo' => 1,
            ],
            [
                'nombre' => 'Ramón (El Mecánico)',
                'telefono' => '829-555-0202',
                'email' => 'ramon@mecanico.com',
                'direccion' => 'Taller de Ramón, Calle 2',
                'rnc_cedula' => '001-9876543-2',
                'limite_credito' => 3000.00,
                'balance_pendiente' => 1800.00,
                'activo' => 1,
            ],
            [
                'nombre' => 'Doña Carmen',
                'telefono' => '809-555-0303',
                'email' => 'carmen@email.com',
                'direccion' => 'Apartamento 2B, Edificio C',
                'rnc_cedula' => '031-1122334-5',
                'limite_credito' => 10000.00,
                'balance_pendiente' => 4200.00,
                'activo' => 1,
            ],
            [
                'nombre' => 'José el Cojo',
                'telefono' => '849-555-0404',
                'email' => 'jose@colmado.com',
                'direccion' => 'Calle 4, Casa #12',
                'rnc_cedula' => '001-0000000-0',
                'limite_credito' => 1500.00,
                'balance_pendiente' => 950.00,
                'activo' => 1,
            ],
            [
                'nombre' => 'Milagros (La Rubia)',
                'telefono' => '809-555-0505',
                'email' => 'mili@gmail.com',
                'direccion' => 'Calle Duarte #15',
                'rnc_cedula' => '102-3344556-7',
                'limite_credito' => 2000.00,
                'balance_pendiente' => 150.00,
                'activo' => 1,
            ],
        ]);
    }
}
