<?php

namespace Tests\Feature;

use App\Models\BusinessInstance;
use App\Models\BusinessType;
use App\Models\InstanceRole;
use App\Models\Plan;
use App\Models\SystemSetting;
use App\Models\User;
use Database\Seeders\WizardStepSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SetupWizardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private BusinessInstance $instance;
    private InstanceRole $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin-business', 'guard_name' => 'web']);

        $type = BusinessType::create([
            'slug' => 'retail',
            'nombre' => 'Retail',
            'activo' => true,
            'orden' => 1,
        ]);

        $plan = Plan::create([
            'nombre' => 'Básico',
            'slug' => 'basico',
            'precio_mensual' => 2000,
            'max_usuarios' => 10,
            'modulos' => [],
            'activo' => true,
            'orden' => 1,
        ]);

        $this->instance = BusinessInstance::create([
            'nombre' => 'Tienda Test',
            'slug' => 'tienda-test',
            'rnc' => '130000001',
            'telefono' => '8091111111',
            'business_type_id' => $type->id,
            'plan_id' => $plan->id,
            'activo' => true,
            'setup_completed' => false,
        ]);

        $this->adminRole = InstanceRole::create([
            'business_instance_id' => $this->instance->id,
            'name' => 'admin',
            'guard_name' => 'instance',
        ]);
        $this->adminRole->syncModules(['inventario', 'cajas']);

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@tienda.com',
            'password' => bcrypt('password123'),
            'role' => 'admin-business',
            'business_type_id' => $type->id,
            'business_instance_id' => $this->instance->id,
            'instance_role_id' => $this->adminRole->id,
        ]);
        $this->admin->assignRole('admin-business');

        $this->seed(WizardStepSeeder::class);
    }

    public function test_parametros_step_updates_instance_and_system_settings(): void
    {
        $this->actingAs($this->admin)
            ->post('/setup/wizard/step', [
                'step' => 'parametros',
                'empresa_nombre' => 'Nuevo Nombre SRL',
                'empresa_telefono' => '8092222222',
                'empresa_rnc' => '130999999',
                'empresa_direccion' => 'Av. Principal 45',
                'empresa_email' => 'contacto@nuevonombre.com',
                'moneda_simbolo' => 'US$',
                'impuesto_itbis' => '18',
            ])
            ->assertRedirect(route('setup.wizard'));

        $this->instance->refresh();
        $this->assertSame('Nuevo Nombre SRL', $this->instance->nombre);
        $this->assertSame('8092222222', $this->instance->telefono);
        $this->assertSame('130999999', $this->instance->rnc);
        $this->assertSame('Av. Principal 45', $this->instance->direccion);
        $this->assertSame('contacto@nuevonombre.com', $this->instance->email);

        $this->assertDatabaseHas('system_settings', [
            'clave' => 'moneda_simbolo',
            'tenant_id' => $this->instance->id,
            'valor' => 'US$',
        ]);
        $this->assertDatabaseHas('system_settings', [
            'clave' => 'impuesto_itbis',
            'tenant_id' => $this->instance->id,
            'valor' => '18',
        ]);
    }

    public function test_usuario_admin_step_creates_additional_admin(): void
    {
        $this->actingAs($this->admin)
            ->post('/setup/wizard/step', [
                'step' => 'usuario-admin',
                'name' => 'Segundo Admin',
                'email' => 'segundo@tienda.com',
                'password' => 'password123456',
                'password_confirmation' => 'password123456',
            ])
            ->assertRedirect(route('setup.wizard'))
            ->assertSessionHas('success');

        $newUser = User::where('email', 'segundo@tienda.com')->firstOrFail();
        $this->assertSame($this->instance->id, $newUser->business_instance_id);
        $this->assertSame($this->adminRole->id, $newUser->instance_role_id);
        $this->assertTrue($newUser->hasRole('admin-business'));

        $this->assertDatabaseHas('system_settings', [
            'clave' => 'wizard_usuario_admin',
            'tenant_id' => $this->instance->id,
        ]);

        $this->assertTrue(app(\App\Services\SetupWizardService::class)
            ->isStepCompleted(\App\Models\WizardStep::where('key', 'usuario-admin')->first(), $this->admin));
    }

    public function test_usuario_admin_step_can_be_skipped(): void
    {
        $this->actingAs($this->admin)
            ->post('/setup/wizard/step', [
                'step' => 'usuario-admin',
                'skip' => '1',
            ])
            ->assertRedirect(route('setup.wizard'));

        $this->assertDatabaseHas('system_settings', [
            'clave' => 'wizard_usuario_admin_skip',
            'tenant_id' => $this->instance->id,
        ]);
        $this->assertSame(1, User::where('business_instance_id', $this->instance->id)->count());
    }
}
