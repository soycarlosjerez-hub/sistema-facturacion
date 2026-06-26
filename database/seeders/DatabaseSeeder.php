<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesYUsuariosSeeder::class);
        $this->call(CustomRolesSeeder::class);        // Crea roles base + root, admin-business, owner
        $this->call(PermissionSeeder::class);         // Asigna permisos a TODOS los roles
        $this->call(TiposVentasSeeder::class);
        $this->call(CategoriaSeeder::class);
        $this->call(ProductosSeeder::class);
        $this->call(ProveedoresSeeder::class);
        $this->call(ClientesSeeder::class);
        $this->call(TipoCompraSeeder::class);
        $this->call(SystemSettingsSeeder::class);
        $this->call(ModuloSeeder::class);
        $this->call(NcfSeeder::class);
        $this->call(SecuenciaEcfSeeder::class);
        $this->call(BusinessTypeSeeder::class);
        $this->call(DeliveryCompanySeeder::class);
        $this->call(WizardStepSeeder::class);
        $this->call(CategoryPermissionsSeeder::class);
        $this->call(RolesAndUsersSeeder::class);
    }
}
