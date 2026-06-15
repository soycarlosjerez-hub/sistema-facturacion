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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Vendedor',
            'email' => 'vendedor@test.com',
            'role' => 'vendedor',
        ]);

        $this->call(RolesYUsuariosSeeder::class);
        $this->call(PermissionSeeder::class);
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
        $this->call(CategoryPermissionsSeeder::class);
    }
}
