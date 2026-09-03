<?php

namespace Database\Seeders;

use App\Models\MarcaTecnologica;
use Illuminate\Database\Seeder;

class MarcaTecnologicaSeeder extends Seeder
{
    /**
     * Ejecuta el seeder para crear las marcas tecnológicas globales.
     */
    public function run(): void
    {
        $marcas = [
            [
                'nombre'         => 'Apple',
                'logo_url'       => null,
                'website'        => 'https://www.apple.com',
                'pais'           => 'EE.UU.',
                'contacto_email' => 'contacto@apple.com',
                'activo'         => true,
                'orden'          => 1,
            ],
            [
                'nombre'         => 'ASUS',
                'logo_url'       => null,
                'website'        => 'https://www.asus.com',
                'pais'           => 'Taiwán',
                'contacto_email' => 'contacto@asus.com',
                'activo'         => true,
                'orden'          => 2,
            ],
            [
                'nombre'         => 'Cisco',
                'logo_url'       => null,
                'website'        => 'https://www.cisco.com',
                'pais'           => 'EE.UU.',
                'contacto_email' => 'contacto@cisco.com',
                'activo'         => true,
                'orden'          => 3,
            ],
            [
                'nombre'         => 'Dell',
                'logo_url'       => null,
                'website'        => 'https://www.dell.com',
                'pais'           => 'EE.UU.',
                'contacto_email' => 'contacto@dell.com',
                'activo'         => true,
                'orden'          => 4,
            ],
            [
                'nombre'         => 'Epson',
                'logo_url'       => null,
                'website'        => 'https://www.epson.com',
                'pais'           => 'Japón',
                'contacto_email' => 'contacto@epson.com',
                'activo'         => true,
                'orden'          => 5,
            ],
            [
                'nombre'         => 'HP (Hewlett-Packard)',
                'logo_url'       => null,
                'website'        => 'https://www.hp.com',
                'pais'           => 'EE.UU.',
                'contacto_email' => 'contacto@hp.com',
                'activo'         => true,
                'orden'          => 6,
            ],
            [
                'nombre'         => 'LG',
                'logo_url'       => null,
                'website'        => 'https://www.lg.com',
                'pais'           => 'Corea del Sur',
                'contacto_email' => 'contacto@lg.com',
                'activo'         => true,
                'orden'          => 7,
            ],
            [
                'nombre'         => 'Lenovo',
                'logo_url'       => null,
                'website'        => 'https://www.lenovo.com',
                'pais'           => 'China',
                'contacto_email' => 'contacto@lenovo.com',
                'activo'         => true,
                'orden'          => 8,
            ],
            [
                'nombre'         => 'Samsung',
                'logo_url'       => null,
                'website'        => 'https://www.samsung.com',
                'pais'           => 'Corea del Sur',
                'contacto_email' => 'contacto@samsung.com',
                'activo'         => true,
                'orden'          => 9,
            ],
            [
                'nombre'         => 'Sony',
                'logo_url'       => null,
                'website'        => 'https://www.sony.com',
                'pais'           => 'Japón',
                'contacto_email' => 'contacto@sony.com',
                'activo'         => true,
                'orden'          => 10,
            ],
        ];

        $creadas  = 0;
        $existentes = 0;

        foreach ($marcas as $marca) {
            $existe = MarcaTecnologica::firstOrCreate(
                ['nombre' => $marca['nombre'], 'tenant_id' => null],
                $marca
            );

            if ($existe->wasRecentlyCreated) {
                $creadas++;
            } else {
                $existentes++;
            }
        }

        $total = count($marcas);
        $this->command->info("Info: Se crearon las {$creadas} marcas tecnológicas. ({$existentes} ya existían de {$total} procesadas.)");
    }
}
