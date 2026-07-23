<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Primero: Instalar roles básicos y personalizados
        $this->call(CustomRolesSeeder::class);        // 1. Crea roles base + root, admin-business, owner
        $this->call(PermissionSeeder::class);         // 2. Asigna permisos a TODOS los roles
        
        // Segundo: Poblar tipos de negocio (necesario para usuarios y business_instances)
        $this->call(BusinessTypeSeeder::class);       // 3. Crea tipos de negocio (restaurante, retail, mayorista, etc.)
        
        // Tercero: Limpiar usuarios admin existentes y asignarlos roles correctos
        $this->call(RolesAndUsersSeeder::class);      // 4. Reasigna admin@test.com como root, crea owner, crea restaurante-ejemplo
        
        // Cuarto: Poblar usuarios de prueba/descanso (dependerán de los roles anteriores)
        $this->call(DemoUsersSeeder::class);          // 5. Crea usuarios demo (gerente, almacen, contador)
        
        $this->call(TiposVentasSeeder::class);
        $this->call(CategoriaSeeder::class);
        $this->call(ProductosSeeder::class);
        $this->call(ProveedoresSeeder::class);
        $this->call(ClientesSeeder::class);
        $this->call(TipoCompraSeeder::class);
        $this->call(SystemSettingsSeeder::class);
        $this->call(ModuloSeeder::class);
        $this->call(NcfSequenceSeeder::class);
        $this->call(SecuenciaEcfSeeder::class);
        $this->call(DeliveryCompanySeeder::class);
        $this->call(WizardStepSeeder::class);
        $this->call(CategoryPermissionsSeeder::class);
        
        // Climatización
        $this->call(TiposClimaSeeder::class);
        $this->call(ClimatizacionSeeder::class);
    }
}
