<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primero, necesitamos crear los roles personalizados
        // Los roles básicos (admin, gerente, vendedor, almacen, contador) ya existen
        // Creamos los roles de administrador personalizados
        
        // 1. Reasignar el usuario admin@test.com actual como root
        $rootUser = User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Administrador Principal',
                'password' => Hash::make('Cambiar123'),
                'role' => 'root',
                'sucursal_id' => null,
                'business_type_id' => null,
            ]
        );
        $rootUser->syncRoles(['root']);
        
        // 2. Crear nuevo usuario administrador para "while-pone-el-restaurante"
        $businessAdminUser = User::updateOrCreate(
            ['email' => 'whilepon@sistema-facturacion.com'],
            [
                'name' => 'while pon el restaurante Admin',
                'password' => Hash::make('Cambiar123'),
                'role' => 'admin-business',
                'sucursal_id' => null,
                'business_type_id' => $this->getWhilePonBusinessTypeId(),
            ]
        );
        $businessAdminUser->syncRoles(['admin-business']);
        
        // 3. Crear usuario de prueba con business type restaurante
        $testUser = User::updateOrCreate(
            ['email' => 'vendedor@sistema-facturacion.com'],
            [
                'name' => 'Vendedor de Prueba',
                'password' => Hash::make('Cambiar123'),
                'role' => 'vendedor',
                'sucursal_id' => null,
                'business_type_id' => $this->getRestauranteBusinessTypeId(),
            ]
        );
        $testUser->syncRoles(['vendedor']);

        // 4. Crear usuario Owner (Dueño del Sistema)
        $ownerUser = User::updateOrCreate(
            ['email' => 'owner@sistema-facturacion.com'],
            [
                'name' => 'Dueño del Sistema',
                'password' => Hash::make('Cambiar123'),
                'role' => 'owner',
                'sucursal_id' => null,
                'business_type_id' => null,
            ]
        );
        $ownerUser->syncRoles(['owner']);

        // 5. Crear instancia de ejemplo
        $restaurantType = \App\Models\BusinessType::where('slug', 'restaurante')->first();
        if ($restaurantType) {
            \App\Models\BusinessInstance::updateOrCreate(
                ['slug' => 'restaurante-ejemplo'],
                [
                    'nombre' => 'Restaurante Ejemplo SRL',
                    'rnc' => '123456789',
                    'costo_mensual' => 5000.00,
                    'email' => 'contacto@restaurante-ejemplo.com',
                    'telefono' => '809-555-0100',
                    'direccion' => 'Calle Principal #123, Santo Domingo',
                    'business_type_id' => $restaurantType->id,
                    'owner_user_id' => $ownerUser->id,
                    'activo' => true,
                    'fecha_vencimiento' => now()->addYear(),
                    'configuracion' => [
                        'nombre_empresa' => 'Restaurante Ejemplo SRL',
                        'slogan' => 'La mejor experiencia culinaria',
                        'moneda_simbolo' => 'RD$',
                        'itbis_porcentaje' => 18,
                    ],
                ]
            );
        }
    }
    
    private function getWhilePonBusinessTypeId(): int
    {
        $whilePon = \App\Models\BusinessType::where('slug', 'while-pone-el-restaurante')->first();
        return $whilePon ? $whilePon->id : 1;
    }
    
    private function getRestauranteBusinessTypeId(): int
    {
        $restaurante = \App\Models\BusinessType::where('slug', 'restaurante')->first();
        return $restaurante ? $restaurante->id : 2;
    }
}
