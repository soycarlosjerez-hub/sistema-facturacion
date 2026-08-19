<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CuentasBancariasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cuentas_bancarias')
            ->where('numero_cuenta', '0829057090')
            ->delete();

        $cuentas = [
            [
                'nombre' => 'BHD',
                'banco' => 'BHD',
                'tipo_cuenta' => null,
                'numero_cuenta' => '11474630079',
                'moneda' => 'RD',
                'titular' => 'Juan Carlos Jerez Gomez',
                'saldo_inicial' => 0,
                'saldo_actual' => 0,
                'activo' => true,
                'tenant_id' => 2,
            ],
            [
                'nombre' => 'Banreserva',
                'banco' => 'Banreserva',
                'tipo_cuenta' => null,
                'numero_cuenta' => '9607298232',
                'moneda' => 'RD',
                'titular' => 'Juan Carlos Jerez Gomez',
                'saldo_inicial' => 0,
                'saldo_actual' => 0,
                'activo' => true,
                'tenant_id' => 2,
            ],
        ];

        foreach ($cuentas as $cuenta) {
            $existe = DB::table('cuentas_bancarias')
                ->where('numero_cuenta', $cuenta['numero_cuenta'])
                ->first();

            if ($existe) {
                DB::table('cuentas_bancarias')
                    ->where('id', $existe->id)
                    ->update($cuenta);
            } else {
                $cuenta['id'] = (DB::table('cuentas_bancarias')->max('id') ?? 0) + 1;
                $cuenta['created_at'] = now();
                $cuenta['updated_at'] = now();
                DB::table('cuentas_bancarias')->insert($cuenta);
            }
        }
    }
}