<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaganTechInstanceSeeder extends Seeder
{
    public function run(): void
    {
        $businessTypeId = DB::table('business_types')->where('slug', 'tecnologia')->value('id');

        if (!$businessTypeId) {
            $this->command->error('No se encontr' . 'o el tipo de negocio "tecnologia".');
            return;
        }

        $instance = DB::table('business_instances')->where('slug', 'magan-tech')->first();

        if ($instance) {
            $instanceId = $instance->id;
            DB::table('business_instances')->where('id', $instanceId)->update([
                'nombre'     => 'MaganTech',
                'rnc'        => null,
                'email'      => null,
                'telefono'   => null,
                'direccion'  => null,
                'business_type_id' => $businessTypeId,
                'plan_id'    => null,
                'owner_user_id' => null,
                'owner_email' => null,
                'owner_nombre' => null,
                'configuracion' => json_encode([
                    'dias_credito' => 30,
                    'moneda_simbolo' => 'RD$',
                    'nombre_empresa' => 'MaganTech',
                    'prefijo_factura' => 'FAC',
                    'itbis_porcentaje' => 18,
                ]),
                'costo_mensual' => 2000.0,
                'bloqueado' => 0,
                'setup_completed' => 1,
                'activo' => 1,
                'fecha_vencimiento' => now()->addMonth()->startOfMonth()->addMonth(),
                'updated_at' => now(),
            ]);
        } else {
            $instanceId = DB::table('business_instances')->insertGetId([
                'nombre'     => 'MaganTech',
                'slug'     => 'magan-tech',
                'rnc'        => null,
                'email'      => null,
                'telefono'   => null,
                'direccion'  => null,
                'business_type_id' => $businessTypeId,
                'plan_id'    => null,
                'owner_user_id' => null,
                'owner_email' => null,
                'owner_nombre' => null,
                'configuracion' => json_encode([
                    'dias_credito' => 30,
                    'moneda_simbolo' => 'RD$',
                    'nombre_empresa' => 'MaganTech',
                    'prefijo_factura' => 'FAC',
                    'itbis_porcentaje' => 18,
                ]),
                'costo_mensual' => 2000.0,
                'bloqueado' => 0,
                'setup_completed' => 1,
                'activo' => 1,
                'fecha_vencimiento' => now()->addMonth()->startOfMonth()->addMonth(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Instancia maganTech creada/actualizada correctamente (ID: ' . $instanceId . ')');
    }
}
