<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seed de lavadores para la instancia 10 (Gato Negro)
 *
 * Ejecutar: php artisan db:seed --class=LavadorInstance10Seeder
 */
class LavadorInstance10Seeder extends Seeder
{
    protected int $tenantId = 10;

    public function run(): void
    {
        $lavadores = [
            [
                'nombre' => 'Juan Pérez',
                'tipo' => 'fijo',
                'porcentaje' => 30.00,
                'telefono' => '809-555-0101',
                'email' => 'juan.perez@gatonegro.do',
                'identificacion' => 'RNC11223344556',
                'activo' => 1,
                'notas' => 'Lavador principal - Especialista en detailing',
            ],
            [
                'nombre' => 'Pedro Martínez',
                'tipo' => 'fijo',
                'porcentaje' => 30.00,
                'telefono' => '809-555-0102',
                'email' => 'pedro.martinez@gatonegro.do',
                'identificacion' => 'RNC22334455667',
                'activo' => 1,
                'notas' => 'Lavador fijo - Especialista en lavado exterior',
            ],
            [
                'nombre' => 'Carlos Rodríguez',
                'tipo' => 'fijo',
                'porcentaje' => 30.00,
                'telefono' => '809-555-0103',
                'email' => 'carlos.rodriguez@gatonegro.do',
                'identificacion' => 'RNC33445566778',
                'activo' => 1,
                'notas' => 'Lavador fijo - Especialista en interior',
            ],
            [
                'nombre' => 'Luis Hernández',
                'tipo' => 'temporal',
                'porcentaje' => 25.00,
                'telefono' => '809-555-0104',
                'email' => 'luis.hernandez@gatonegro.do',
                'identificacion' => 'RNC44556677889',
                'activo' => 1,
                'notas' => 'Lavador temporal - Apoyo en horas pico',
            ],
            [
                'nombre' => 'Miguel Santos',
                'tipo' => 'temporal',
                'porcentaje' => 25.00,
                'telefono' => '809-555-0105',
                'email' => 'miguel.santos@gatonegro.do',
                'identificacion' => 'RNC55667788990',
                'activo' => 1,
                'notas' => 'Lavador temporal - Apoyo en días festivos',
            ],
            [
                'nombre' => 'Roberto Díaz',
                'tipo' => 'temporal',
                'porcentaje' => 25.00,
                'telefono' => '809-555-0106',
                'email' => 'roberto.diaz@gatonegro.do',
                'identificacion' => 'RNC66778899001',
                'activo' => 1,
                'notas' => 'Lavador temporal - Apoyo adicional',
            ],
        ];

        $ahora = now();

        foreach ($lavadores as $lavador) {
            // Verificar si ya existe para no duplicar
            $existente = DB::table('lavadores')
                ->where('tenant_id', $this->tenantId)
                ->where('nombre', $lavador['nombre'])
                ->first();

            if ($existente) {
                // Actualizar si ya existe
                DB::table('lavadores')
                    ->where('id', $existente->id)
                    ->update([
                        'tipo' => $lavador['tipo'],
                        'porcentaje' => $lavador['porcentaje'],
                        'telefono' => $lavador['telefono'],
                        'email' => $lavador['email'],
                        'identificacion' => $lavador['identificacion'],
                        'activo' => $lavador['activo'],
                        'notas' => $lavador['notas'],
                        'tenant_id' => $this->tenantId,
                        'updated_at' => $ahora,
                    ]);
            } else {
                // Insertar nuevo
                DB::table('lavadores')->insert([
                    'nombre' => $lavador['nombre'],
                    'tipo' => $lavador['tipo'],
                    'porcentaje' => $lavador['porcentaje'],
                    'telefono' => $lavador['telefono'],
                    'email' => $lavador['email'],
                    'identificacion' => $lavador['identificacion'],
                    'activo' => $lavador['activo'],
                    'notas' => $lavador['notas'],
                    'tenant_id' => $this->tenantId,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]);
            }
        }

        $this->command->info(
            'Lavador (instancia 10): Sincronizados ' . count($lavadores) . ' lavadores.'
        );
    }
}
