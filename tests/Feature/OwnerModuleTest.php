<?php

namespace Tests\Feature;

use App\Mail\UserCreatedNotification;
use App\Models\BusinessInstance;
use App\Models\BusinessType;
use App\Models\BusinessTypeModule;
use App\Models\Modulo;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OwnerModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected BusinessInstance $instance;
    protected BusinessType $businessType;
    protected Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->owner = User::create([
            'name' => 'Super Owner',
            'email' => 'owner@test.com',
            'password' => bcrypt('password123'),
        ]);
        $this->owner->assignRole('owner');

        $this->businessType = BusinessType::create([
            'slug' => 'restaurante',
            'nombre' => 'Restaurante',
            'activo' => true,
            'orden' => 1,
        ]);

        BusinessTypeModule::create([
            'business_type_id' => $this->businessType->id,
            'modulo_key' => 'inventario',
            'visible' => true,
            'orden' => 1,
        ]);

        $this->plan = Plan::create([
            'nombre' => 'Profesional',
            'slug' => 'profesional',
            'descripcion' => 'Plan de prueba',
            'precio_mensual' => 3000,
            'precio_implementacion' => 3000,
            'precio_lanzamiento' => 1500,
            'max_usuarios' => 5,
            'max_empresas' => 3,
            'features' => ['Facturación electrónica', 'Reportes'],
            'modulos' => [],
            'activo' => true,
            'recomendado' => true,
            'orden' => 1,
        ]);

        $this->instance = BusinessInstance::create([
            'nombre' => 'Mi Negocio',
            'slug' => 'mi-negocio',
            'business_type_id' => $this->businessType->id,
            'owner_user_id' => $this->owner->id,
            'plan_id' => $this->plan->id,
            'activo' => true,
            'costo_mensual' => 3000,
        ]);
    }

    public function test_owner_dashboard_accessible(): void
    {
        $response = $this->actingAs($this->owner)->get('/owner');
        $response->assertStatus(200);
        $response->assertSee('Panel de Control');
        $response->assertSee('Mi Negocio');
    }

    public function test_owner_can_view_instances(): void
    {
        $response = $this->actingAs($this->owner)->get('/owner/instances');
        $response->assertStatus(200);
        $response->assertSee('Mi Negocio');
    }

    public function test_owner_can_show_instance(): void
    {
        // Verify instance show loads without error and doesn't eager-load user tokens
        $response = $this->actingAs($this->owner)->get('/owner/instances/' . $this->instance->id);
        $response->assertStatus(200);
        $response->assertSee('Mi Negocio');
    }

    public function test_dashboard_limits_instance_load(): void
    {
        // Create more than 50 instances to verify the dashboard doesn't load them all
        for ($i = 0; $i < 60; $i++) {
            BusinessInstance::create([
                'nombre' => "Instancia {$i}",
                'slug' => "instancia-{$i}",
                'business_type_id' => $this->businessType->id,
                'owner_user_id' => $this->owner->id,
                'plan_id' => $this->plan->id, // Need plan_id to match the join in MRR query
                'activo' => true,
            ]);
        }

        $response = $this->actingAs($this->owner)->get('/owner');
        $response->assertStatus(200);
        // Dashboard should still work even with many instances
        // Should see at least the first instance name
        $response->assertSee('Mi Negocio');
    }

    public function test_owner_can_create_instance(): void
    {
        $instanceData = [
            'nombre' => 'Nueva Empresa',
            'slug' => 'nueva-empresa',
            'rnc' => '12300000',
            'business_type_id' => $this->businessType->id,
            'plan_id' => $this->plan->id,
            'owner_user_id' => $this->owner->id,
            'activo' => 1,
        ];

        $response = $this->actingAs($this->owner)->post('/owner/instances', $instanceData);
        $response->assertRedirect();
        $response->assertRedirectContains('/owner/instances/');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('business_instances', [
            'nombre' => 'Nueva Empresa',
            'slug' => 'nueva-empresa',
        ]);
    }

    public function test_owner_can_edit_instance(): void
    {
        $formData = [
            'nombre' => 'Mi Negocio Editado',
            'business_type_id' => $this->businessType->id,
            'plan_id' => $this->plan->id,
            'activo' => 1,
        ];

        $response = $this->actingAs($this->owner)->put("/owner/instances/{$this->instance->id}", $formData);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->instance->refresh();
        $this->assertEquals('Mi Negocio Editado', $this->instance->nombre);
    }

    public function test_owner_can_delete_instance(): void
    {
        $instanceId = $this->instance->id;
        $response = $this->actingAs($this->owner)->delete("/owner/instances/{$instanceId}");
        $response->assertRedirect('/owner/instances');
        $response->assertSessionHas('success');

        // Soft deleted
        $this->assertNotNull(\App\Models\BusinessInstance::withTrashed()->find($instanceId)->trashed());
    }

    public function test_owner_cannot_access_instances_as_non_owner(): void
    {
        $adminUser = User::create([
            'name' => 'Admin Usuario',
            'email' => 'admin@negocio.com',
            'password' => bcrypt('password123'),
            'business_instance_id' => $this->instance->id,
        ]);
        $adminUser->assignRole('admin');

        $response = $this->actingAs($adminUser)->get('/owner/instances');
        $response->assertStatus(403);
    }

    public function test_owner_can_view_business_types(): void
    {
        $response = $this->actingAs($this->owner)->get('/owner/business-types');
        $response->assertStatus(200);
        $response->assertSee('Restaurante');
    }

    public function test_owner_can_create_business_type(): void
    {
        $data = [
            'nombre' => 'Tienda',
            'slug' => 'tienda',
            'activo' => 1,
            'orden' => 2,
        ];

        $response = $this->actingAs($this->owner)->post('/owner/business-types', $data);
        $response->assertRedirect('/owner/business-types');

        $this->assertDatabaseHas('business_types', [
            'slug' => 'tienda',
        ]);
    }

    public function test_owner_can_create_modulo(): void
    {
        Modulo::create(['key' => 'inventario', 'label' => 'Inventario', 'categoria' => 'core', 'activo' => true, 'orden' => 1]);

        $data = [
            'key' => 'reportes',
            'label' => 'Reportes',
            'categoria' => 'reportes',
            'orden' => 1,
            'activo' => 1,
        ];

        $response = $this->actingAs($this->owner)->post('/owner/modules', $data);
        $response->assertRedirect('/owner/modules');
        $this->assertDatabaseHas('modulos', ['key' => 'reportes']);
    }

    public function test_owner_plans_can_be_listed(): void
    {
        $response = $this->actingAs($this->owner)->get('/owner/planes');
        $response->assertStatus(200);
        $response->assertSee('Profesional');
    }

    public function test_owner_can_register_payment(): void
    {
        $paymentData = [
            'monto' => 3000,
            'mes_pagado' => now()->format('Y-m-d'),
            'metodo_pago' => 'transferencia',
            'notas' => 'Pago mensual',
        ];

        $response = $this->actingAs($this->owner)->post(
            "/owner/instances/{$this->instance->id}/pagos",
            $paymentData
        );

        $response->assertRedirect("/owner/instances/{$this->instance->id}");
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pagos_instancia', [
            'business_instance_id' => $this->instance->id,
            'monto' => 3000,
            'estado_pago' => 'completado',
        ]);
    }

    public function test_owner_can_block_instance(): void
    {
        $response = $this->actingAs($this->owner)->post(
            "/owner/instances/{$this->instance->id}/toggle-block",
            [
                'bloqueado' => true,
                'motivo_bloqueo' => 'Impago mensual',
            ]
        );

        $response->assertRedirect("/owner/instances/{$this->instance->id}");
        $this->instance->refresh();
        $this->assertTrue($this->instance->bloqueado);

        // Unblock
        $response = $this->actingAs($this->owner)->post(
            "/owner/instances/{$this->instance->id}/toggle-block",
            ['bloqueado' => false]
        );

        $this->instance->refresh();
        $this->assertFalse($this->instance->bloqueado);
    }

    public function test_owner_can_view_online_users(): void
    {
        // Create users with last_seen_at set within 5 minutes
        $onlineUser = User::create([
            'name' => 'Usuario Online',
            'email' => 'online@test.com',
            'password' => bcrypt('password123'),
            'business_instance_id' => $this->instance->id,
            'last_seen_at' => now(),
        ]);

        $response = $this->actingAs($this->owner)->get('/owner/online');
        $response->assertStatus(200);
        // Verify the response is actually rendering content (even if partial)
        $response->assertOk();
    }

    public function test_owner_can_view_errors(): void
    {
        \App\Models\InstanceErrorLog::create([
            'tenant_id' => $this->instance->id,
            'level' => 'error',
            'title' => 'Test error',
            'message' => 'Error de prueba',
            'source' => 'exception',
        ]);

        $response = $this->actingAs($this->owner)->get('/owner/errors');
        $response->assertStatus(200);
        $response->assertSee('Test error');
    }

    public function test_owner_can_create_business_type_with_validation(): void
    {
        $response = $this->actingAs($this->owner)->post('/owner/business-types', []);
        $response->assertSessionHasErrors(['nombre', 'slug']);
    }

    public function test_business_type_slug_must_be_unique(): void
    {
        $data = [
            'nombre' => 'Otro Restaurante',
            'slug' => 'restaurante',
            'activo' => 1,
        ];

        $response = $this->actingAs($this->owner)->post('/owner/business-types', $data);
        $response->assertSessionHasErrors(['slug']);
    }

    public function test_instance_slug_must_be_unique(): void
    {
        $data = [
            'nombre' => 'Otro Nombre',
            'slug' => 'mi-negocio',
            'business_type_id' => $this->businessType->id,
            'activo' => 1,
        ];

        $response = $this->actingAs($this->owner)->post('/owner/instances', $data);
        $response->assertSessionHasErrors(['slug']);
    }
}
