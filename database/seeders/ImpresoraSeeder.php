<?php

namespace Database\Seeders;

use App\Models\Impresora;
use App\Models\PlantillaImpresion;
use Illuminate\Database\Seeder;

class ImpresoraSeeder extends Seeder
{
    public function run(): void
    {
        // Plantillas predeterminadas
        $plantillasData = PlantillaImpresion::CODIGOS_PREDETERMINADOS;

        foreach ($plantillasData as $codigo => $data) {
            PlantillaImpresion::firstOrCreate(
                ['codigo' => $codigo],
                [
                    'nombre' => $data['nombre'],
                    'modulo' => $data['modulo'],
                    'tipo_formato' => $data['tipo_formato'],
                    'incluir_logo' => true,
                    'incluir_encabezado' => true,
                    'incluir_pie' => true,
                    'activo' => true,
                ]
            );
        }

        // Solo crear impresora PDF por defecto si no hay ninguna
        if (Impresora::count() === 0) {
            Impresora::create([
                'nombre' => 'PDF Predeterminado',
                'tipo_conexion' => 'pdf',
                'driver' => 'pdf',
                'papel_tamano' => 'letter',
                'caracteres_por_linea' => 80,
                'auto_imprimir_ventas' => false,
                'auto_imprimir_cotizaciones' => false,
                'auto_imprimir_conduces' => false,
                'activo' => true,
                'orden' => 1,
                'descripcion' => 'Impresora PDF generada automáticamente',
            ]);
        }
    }
}
