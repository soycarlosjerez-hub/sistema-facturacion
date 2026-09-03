<?php

namespace Database\Seeders;

use App\Models\DeliveryDriver;
use Illuminate\Database\Seeder;

class DeliveryDriversSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = \App\Models\BusinessInstance::whereNotNull('id')->take(3)->get();
        
        $drivers = [
            [
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'cedula' => '401-0000000-1',
                'telefono' => '+1 (829) 555-0101',
                'whatsapp' => '+1 (829) 555-0101',
                'licencia_conducir' => 'A-12345678',
                'activo' => true,
            ],
            [
                'nombre' => 'María',
                'apellido' => 'Díaz',
                'cedula' => '001-0000000-2',
                'telefono' => '+1 (809) 555-0202',
                'whatsapp' => '+1 (809) 555-0202',
                'licencia_conducir' => 'B-87654321',
                'activo' => true,
            ],
            [
                'nombre' => 'Carlos',
                'apellido' => 'Ramírez',
                'cedula' => '001-0000000-3',
                'telefono' => '+1 (829) 555-0303',
                'whatsapp' => '+1 (829) 555-0303',
                'licencia_conducir' => 'A-11223344',
                'activo' => true,
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Martínez',
                'cedula' => '001-0000000-4',
                'telefono' => '+1 (809) 555-0404',
                'whatsapp' => '+1 (809) 555-0404',
                'licencia_conducir' => 'C-55667788',
                'activo' => true,
            ],
            [
                'nombre' => 'Pedro',
                'apellido' => 'Santos',
                'cedula' => '001-0000000-5',
                'telefono' => '+1 (829) 555-0505',
                'whatsapp' => '+1 (829) 555-0505',
                'licencia_conducir' => 'A-99887766',
                'activo' => true,
            ],
            [
                'nombre' => 'Laura',
                'apellido' => 'Gómez',
                'cedula' => '001-0000000-6',
                'telefono' => '+1 (809) 555-0606',
                'whatsapp' => '+1 (809) 555-0606',
                'licencia_conducir' => 'B-22334455',
                'activo' => true,
            ],
        ];

        foreach ($tenants as $tenant) {
            foreach ($drivers as $driverData) {
                $driverData['tenant_id'] = $tenant->id;
                DeliveryDriver::create($driverData);
            }
        }
    }
}
