<?php

namespace Tests\Feature;

use App\Models\BusinessInstance;
use App\Models\BusinessType;
use App\Models\Gasto;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolesYUsuariosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GastoTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminA;
    protected User $adminB;
    protected BusinessInstance $instanceA;
    protected BusinessInstance $instanceB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
        $this->seed(RolesYUsuariosSeeder::class);

        $owner = User::firstOrCreate(
            ['email' => 'owner@test.com'],
            ['name' => 'Owner', 'password' => bcrypt('123456')]
        );

        $bizType = BusinessType::firstOrCreate(
            ['slug' => 'default'],
            ['nombre' => 'Default', 'activo' => true]
        );

        $this->instanceA = BusinessInstance::create([
            'nombre' => 'Instancia A',
            'slug' => 'instancia-a',
            'business_type_id' => $bizType->id,
            'owner_user_id' => $owner->id,
            'activo' => true,
        ]);

        $this->instanceB = BusinessInstance::create([
            'nombre' => 'Instancia B',
            'slug' => 'instancia-b',
            'business_type_id' => $bizType->id,
            'owner_user_id' => $owner->id,
            'activo' => true,
        ]);

        $this->adminA = User::firstOrCreate(
            ['email' => 'admin-a@test.com'],
            [
                'name' => 'Admin A',
                'password' => bcrypt('123456'),
                'business_instance_id' => $this->instanceA->id,
            ]
        );
        $this->adminA->syncRoles(['admin']);

        $this->adminB = User::firstOrCreate(
            ['email' => 'admin-b@test.com'],
            [
                'name' => 'Admin B',
                'password' => bcrypt('123456'),
                'business_instance_id' => $this->instanceB->id,
            ]
        );
        $this->adminB->syncRoles(['admin']);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function test_index_requires_auth(): void
    {
        $response = $this->get('/gastos');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_index_ok(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->actingAs($this->adminA)->get('/gastos');
        $response->assertStatus(200);
    }

    public function test_create_form_ok(): void
    {
        $response = $this->actingAs($this->adminA)->get('/gastos/create');
        $response->assertStatus(200);
        $response->assertSee('Registrar Gasto');
    }

    public function test_store_gasto_sets_tenant_id(): void
    {
        $data = [
            'descripcion' => 'Pago de electricidad',
            'monto' => 1500.00,
            'categoria' => 'servicios',
            'fecha_gasto' => date('Y-m-d'),
            'metodo_pago' => 'efectivo',
        ];

        $response = $this->actingAs($this->adminA)->post('/gastos', $data);
        $response->assertRedirect('/gastos');
        $response->assertSessionHas('success');

        $gasto = Gasto::where('descripcion', 'Pago de electricidad')->first();
        $this->assertNotNull($gasto);
        $this->assertEquals($this->instanceA->id, $gasto->tenant_id);
        $this->assertEquals($this->adminA->id, $gasto->user_id);
    }

    public function test_store_gasto_with_all_fields(): void
    {
        $data = [
            'descripcion' => 'Compra de suministros',
            'monto' => 2500.50,
            'categoria' => 'suministros',
            'notas' => 'Nota de prueba',
            'fecha_gasto' => '2026-06-25',
            'metodo_pago' => 'tarjeta',
            'comprobante' => 'FAC-001',
        ];

        $response = $this->actingAs($this->adminA)->post('/gastos', $data);
        $response->assertRedirect('/gastos');

        $gasto = Gasto::where('descripcion', 'Compra de suministros')->first();
        $this->assertNotNull($gasto);
        $this->assertEquals(2500.50, $gasto->monto);
        $this->assertEquals('suministros', $gasto->categoria);
        $this->assertEquals('Nota de prueba', $gasto->notas);
        $this->assertEquals('tarjeta', $gasto->metodo_pago);
        $this->assertEquals('FAC-001', $gasto->comprobante);
        $this->assertEquals($this->instanceA->id, $gasto->tenant_id);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->adminA)->post('/gastos', []);
        $response->assertSessionHasErrors(['descripcion', 'monto', 'fecha_gasto']);
    }

    public function test_store_validates_monto_min(): void
    {
        $response = $this->actingAs($this->adminA)->post('/gastos', [
            'descripcion' => 'Test',
            'monto' => 0,
            'fecha_gasto' => date('Y-m-d'),
        ]);
        $response->assertSessionHasErrors(['monto']);
    }

    public function test_gastos_only_visible_for_own_tenant(): void
    {
        Gasto::create([
            'descripcion' => 'Gasto de Instancia A',
            'monto' => 100,
            'fecha_gasto' => now(),
            'user_id' => $this->adminA->id,
            'tenant_id' => $this->instanceA->id,
        ]);

        Gasto::create([
            'descripcion' => 'Gasto de Instancia B',
            'monto' => 200,
            'fecha_gasto' => now(),
            'user_id' => $this->adminB->id,
            'tenant_id' => $this->instanceB->id,
        ]);

        $responseA = $this->actingAs($this->adminA)->get('/gastos');
        $responseA->assertSee('Gasto de Instancia A');
        $responseA->assertDontSee('Gasto de Instancia B');

        $responseB = $this->actingAs($this->adminB)->get('/gastos');
        $responseB->assertSee('Gasto de Instancia B');
        $responseB->assertDontSee('Gasto de Instancia A');
    }

    public function test_show_gasto_ok(): void
    {
        $gasto = Gasto::create([
            'descripcion' => 'Gasto de prueba',
            'monto' => 500,
            'fecha_gasto' => now(),
            'user_id' => $this->adminA->id,
            'tenant_id' => $this->instanceA->id,
        ]);

        $response = $this->actingAs($this->adminA)->get("/gastos/{$gasto->id}");
        $response->assertStatus(200);
        $response->assertSee('Gasto de prueba');
    }

    public function test_edit_form_ok(): void
    {
        $gasto = Gasto::create([
            'descripcion' => 'Gasto editable',
            'monto' => 300,
            'fecha_gasto' => now(),
            'user_id' => $this->adminA->id,
            'tenant_id' => $this->instanceA->id,
        ]);

        $response = $this->actingAs($this->adminA)->get("/gastos/{$gasto->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Editar Gasto');
        $response->assertSee('Gasto editable');
    }

    public function test_update_gasto(): void
    {
        $gasto = Gasto::create([
            'descripcion' => 'Descripcion original',
            'monto' => 100,
            'fecha_gasto' => now(),
            'user_id' => $this->adminA->id,
            'tenant_id' => $this->instanceA->id,
        ]);

        $response = $this->actingAs($this->adminA)->put("/gastos/{$gasto->id}", [
            'descripcion' => 'Descripcion actualizada',
            'monto' => 999.99,
            'fecha_gasto' => now()->format('Y-m-d'),
        ]);
        $response->assertRedirect('/gastos');
        $response->assertSessionHas('success');

        $gasto->refresh();
        $this->assertEquals('Descripcion actualizada', $gasto->descripcion);
        $this->assertEquals(999.99, $gasto->monto);
    }

    public function test_delete_gasto(): void
    {
        $gasto = Gasto::create([
            'descripcion' => 'Gasto a eliminar',
            'monto' => 50,
            'fecha_gasto' => now(),
            'user_id' => $this->adminA->id,
            'tenant_id' => $this->instanceA->id,
        ]);

        $response = $this->actingAs($this->adminA)->delete("/gastos/{$gasto->id}");
        $response->assertRedirect('/gastos');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('gastos', ['id' => $gasto->id]);
    }

    public function test_tenant_id_not_mass_assignable_from_request(): void
    {
        $data = [
            'descripcion' => 'Intento de inyección',
            'monto' => 100,
            'fecha_gasto' => date('Y-m-d'),
            'tenant_id' => $this->instanceB->id,
        ];

        $this->actingAs($this->adminA)->post('/gastos', $data);

        $gasto = Gasto::where('descripcion', 'Intento de inyección')->first();
        $this->assertNotNull($gasto);
        $this->assertEquals($this->instanceA->id, $gasto->tenant_id);
        $this->assertNotEquals($this->instanceB->id, $gasto->tenant_id);
    }

    public function test_cannot_view_other_tenant_gasto_via_url(): void
    {
        $gastoB = Gasto::create([
            'descripcion' => 'Gasto privado de B',
            'monto' => 999,
            'fecha_gasto' => now(),
            'user_id' => $this->adminB->id,
            'tenant_id' => $this->instanceB->id,
        ]);

        $response = $this->actingAs($this->adminA)->get("/gastos/{$gastoB->id}");
        $response->assertStatus(404);
    }
}
