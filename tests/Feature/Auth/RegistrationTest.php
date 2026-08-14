<?php

namespace Tests\Feature\Auth;

use App\Mail\NuevaInstanciaRegistrada;
use App\Models\BusinessInstance;
use App\Models\BusinessType;
use App\Models\BusinessTypeModule;
use App\Models\InstanceRole;
use App\Models\PagoInstancia;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin-business', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
    }

    protected function makeBusinessType(string $slug = 'restaurante'): BusinessType
    {
        $type = BusinessType::create([
            'slug' => $slug,
            'nombre' => 'Restaurante',
            'activo' => true,
            'orden' => 1,
        ]);

        BusinessTypeModule::create([
            'business_type_id' => $type->id,
            'modulo_key' => 'inventario',
            'visible' => true,
            'orden' => 1,
        ]);
        BusinessTypeModule::create([
            'business_type_id' => $type->id,
            'modulo_key' => 'cajas',
            'visible' => true,
            'orden' => 2,
        ]);

        BusinessType::flush();

        return $type;
    }

    protected function makePlan(): Plan
    {
        return Plan::create([
            'nombre' => 'Profesional',
            'slug' => 'profesional',
            'descripcion' => 'Plan de prueba',
            'precio_mensual' => 3000,
            'precio_implementacion' => 3000,
            'precio_lanzamiento' => 1500,
            'max_usuarios' => 5,
            'max_empresas' => 1,
            'features' => ['Facturación electrónica', 'Reportes'],
            'modulos' => [],
            'activo' => true,
            'recomendado' => true,
            'orden' => 1,
        ]);
    }

    protected function registrationPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Admin Test',
            'email' => 'admin-test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'negocio_nombre' => 'Negocio Test',
            'business_type_id' => $overrides['business_type_id'] ?? $this->makeBusinessType()->id,
            'rnc' => '130000000',
            'telefono' => '8090000000',
            'direccion' => 'Calle Test 123',
            'plan_id' => $overrides['plan_id'] ?? $this->makePlan()->id,
        ], $overrides);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->makeBusinessType();
        $this->makePlan();

        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Profesional');
        $response->assertSee('Restaurante');
    }

    public function test_new_business_can_register_with_instance_plan_and_admin_role(): void
    {
        $payload = $this->registrationPayload();

        $response = $this->post('/register', $payload);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();

        $user = User::where('email', 'admin-test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('admin-business'));

        $instance = BusinessInstance::where('rnc', '130000000')->first();
        $this->assertNotNull($instance);
        $this->assertSame($payload['business_type_id'], $instance->business_type_id);
        $this->assertSame($payload['plan_id'], $instance->plan_id);
        $this->assertSame($user->id, $instance->owner_user_id);
        $this->assertFalse($instance->setup_completed);
        $this->assertSame($user->business_instance_id, $instance->id);

        $this->assertDatabaseHas('pagos_instancia', [
            'business_instance_id' => $instance->id,
            'plan_id' => $payload['plan_id'],
            'estado_pago' => 'pendiente',
            'referencia_externa' => 'REGISTRO-AUTOSERVICIO',
        ]);

        $adminRole = InstanceRole::where('business_instance_id', $instance->id)
            ->where('name', 'admin')
            ->first();
        $this->assertNotNull($adminRole);
        $this->assertSame($adminRole->id, $user->instance_role_id);
        $this->assertTrue($adminRole->visibleModules()->where('modulo_key', 'inventario')->exists());
        $this->assertTrue($adminRole->visibleModules()->where('modulo_key', 'cajas')->exists());
    }

    public function test_duplicate_email_cannot_register(): void
    {
        $type = $this->makeBusinessType();
        $plan = $this->makePlan();
        $payload = $this->registrationPayload([
            'business_type_id' => $type->id,
            'plan_id' => $plan->id,
        ]);

        $this->post('/register', $payload)->assertRedirect(route('dashboard', absolute: false));
        auth()->logout();

        $this->post('/register', $payload)->assertSessionHasErrors('email');
        $this->assertSame(1, User::where('email', 'admin-test@example.com')->count());
    }

    public function test_duplicate_rnc_cannot_register(): void
    {
        $type = $this->makeBusinessType();
        $plan = $this->makePlan();
        $payload = $this->registrationPayload([
            'business_type_id' => $type->id,
            'plan_id' => $plan->id,
        ]);

        $this->post('/register', $payload)->assertRedirect(route('dashboard', absolute: false));
        auth()->logout();

        $other = $this->registrationPayload([
            'business_type_id' => $type->id,
            'plan_id' => $plan->id,
            'email' => 'other@example.com',
            'name' => 'Otro Admin',
        ]);

        $this->post('/register', $other)->assertSessionHasErrors('rnc');
    }

    public function test_owners_are_notified_when_instance_registers(): void
    {
        Mail::fake();

        $owner = User::create([
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'password' => bcrypt('password123'),
        ]);
        $owner->syncRoles(['owner']);

        $this->post('/register', $this->registrationPayload());

        $instance = BusinessInstance::where('rnc', '130000000')->firstOrFail();

        Mail::assertSent(NuevaInstanciaRegistrada::class, function ($mail) use ($owner, $instance) {
            return $mail->hasTo($owner->email)
                && $mail->instance->id === $instance->id
                && $mail->adminUser->email === 'admin-test@example.com';
        });
    }
}

function AuthLogout(): void
{
    auth()->logout();
}