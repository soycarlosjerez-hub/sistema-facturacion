<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryCompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            ['nombre' => 'Uber Eats',    'nombre_corto' => 'uber_eats',   'comision_porcentaje' => 30.00, 'activo' => true],
            ['nombre' => 'PedidosYa',    'nombre_corto' => 'pedidos_ya',  'comision_porcentaje' => 25.00, 'activo' => true],
            ['nombre' => 'Didi Food',    'nombre_corto' => 'didi_food',   'comision_porcentaje' => 22.00, 'activo' => true],
            ['nombre' => 'DoorDash',     'nombre_corto' => 'door_dash',   'comision_porcentaje' => 25.00, 'activo' => true],
            ['nombre' => 'Glovo',        'nombre_corto' => 'glovo',       'comision_porcentaje' => 28.00, 'activo' => true],
            ['nombre' => 'Otro',         'nombre_corto' => 'otro',        'comision_porcentaje' => 0.00,  'activo' => true],
        ];

        foreach ($companies as $company) {
            DB::table('delivery_companies')->updateOrInsert(
                ['nombre_corto' => $company['nombre_corto']],
                $company
            );
        }
    }
}
