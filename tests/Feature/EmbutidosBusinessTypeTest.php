<?php

namespace Tests\Feature;

use App\Models\BusinessType;
use App\Models\BusinessTypeModule;
use App\Models\Categoria;
use App\Models\Producto;
use Database\Seeders\BusinessTypeSeeder;
use Database\Seeders\EmbutidosCategoriaSeeder;
use Database\Seeders\EmbutidosProductoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmbutidosBusinessTypeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(BusinessTypeSeeder::class);
    }

    public function test_embutidos_business_type_exists(): void
    {
        $type = BusinessType::where('slug', 'embutidos')->first();

        $this->assertNotNull($type, 'El business type "embutidos" debe existir');
        $this->assertSame('Embutidos / Charcutería', $type->nombre);
        $this->assertTrue((bool) $type->activo);
        $this->assertSame('danger', $type->color);
        $this->assertSame(12, (int) $type->orden);
    }

    public function test_embutidos_visible_modules(): void
    {
        $modules = BusinessType::getModulosVisibles('embutidos');

        $obligatorios = ['dashboard', 'inventario', 'ventas', 'clientes', 'compras', 'ncf', 'ecf', 'reportes-ventas'];

        foreach ($obligatorios as $moduloKey) {
            $this->assertContains(
                $moduloKey,
                $modules,
                "El módulo '$moduloKey' debe estar visible para el tipo embutidos"
            );
        }
    }

    public function test_embutidos_has_module_records_ordered(): void
    {
        $type = BusinessType::where('slug', 'embutidos')->firstOrFail();

        $modulos = BusinessTypeModule::where('business_type_id', $type->id)
            ->where('visible', true)
            ->orderBy('orden')
            ->get();

        $this->assertGreaterThanOrEqual(15, $modulos->count());
        $this->assertSame('dashboard', $modulos->first()->modulo_key);
    }

    public function test_embutidos_roles_config_exists(): void
    {
        $config = config('business_type_roles.embutidos');

        $this->assertNotNull($config, 'La config de roles para "embutidos" debe existir');
        $this->assertIsArray($config);

        foreach (['owner', 'root', 'admin-business'] as $nivel) {
            $this->assertArrayHasKey($nivel, $config);
            $this->assertContains('instance-admin', $config[$nivel]);
        }

        foreach (['owner', 'root'] as $nivel) {
            $this->assertContains('admin', $config[$nivel]);
        }
    }

    public function test_embutidos_seeders_seed_categories_and_products(): void
    {
        $this->seed(EmbutidosCategoriaSeeder::class);

        $categorias = Categoria::whereNull('tenant_id')->whereIn('nombre', [
            'Salami', 'Longaniza', 'Chorizo', 'Jamón',
        ])->pluck('nombre');

        $this->assertContains('Salami', $categorias);
        $this->assertContains('Chorizo', $categorias);

        $this->seed(EmbutidosProductoSeeder::class);

        $productos = Producto::whereNull('tenant_id')->get();
        $this->assertGreaterThanOrEqual(10, $productos->count());

        $salami = Producto::whereNull('tenant_id')
            ->where('nombre', 'Salami Popular (500g)')
            ->first();

        $this->assertNotNull($salami);
        $this->assertSame(18.0, (float) $salami->itbis_porcentaje);
    }

    public function test_embutidos_seeders_are_idempotent(): void
    {
        $this->seed(EmbutidosCategoriaSeeder::class);
        $this->seed(EmbutidosCategoriaSeeder::class);

        $this->seed(EmbutidosProductoSeeder::class);
        $this->seed(EmbutidosProductoSeeder::class);

        $this->assertSame(8, Categoria::whereNull('tenant_id')->whereIn('nombre', [
            'Salami', 'Longaniza', 'Chorizo', 'Jamón',
            'Mortadela / Bologna', 'Tocino', 'Quesos', 'Otros Embutidos',
        ])->count());

        $nombres = ['Salami Popular (500g)', 'Tocino Ahumado (lb)'];
        foreach ($nombres as $nombre) {
            $this->assertSame(
                1,
                Producto::whereNull('tenant_id')->where('nombre', $nombre)->count(),
                "El producto '$nombre' no debe duplicarse al re-correr el seeder"
            );
        }
    }
}