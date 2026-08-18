<?php

namespace Tests\Feature;

use App\Models\BusinessInstance;
use App\Models\PagoInstancia;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuscripcionBillingTest extends TestCase
{
    use RefreshDatabase;

    private function crearInstancia(array $overrides = []): BusinessInstance
    {
        return BusinessInstance::factory()->create(array_merge([
            'costo_mensual' => 1500,
            'fecha_vencimiento' => now()->addDays(10),
            'trial_started_at' => now()->subDays(5),
            'trial_ends_at' => now()->addDays(10),
            'activo' => true,
            'bloqueado' => false,
        ], $overrides));
    }

    private function crearUsuarioAdmin(BusinessInstance $instance): User
    {
        $user = User::factory()->create([
            'business_instance_id' => $instance->id,
        ]);
        $instance->update(['owner_user_id' => $user->id]);

        return $user;
    }

    public function test_instancia_en_prueba_no_es_bloqueable(): void
    {
        $instance = $this->crearInstancia();

        $this->assertTrue($instance->enPeriodoPrueba());
        $this->assertTrue($instance->estaAlDia());
        $this->assertSame('prueba', $instance->estadoSuscripcion());
        $this->assertFalse($instance->bloqueablePorImpago());
    }

    public function test_pago_pendiente_no_mantiene_al_dia_despues_de_la_prueba(): void
    {
        $instance = $this->crearInstancia([
            'trial_ends_at' => now()->subDay(),
            'fecha_vencimiento' => now()->subDay(),
        ]);

        PagoInstancia::create([
            'business_instance_id' => $instance->id,
            'plan_id' => $instance->plan_id,
            'monto' => 1500,
            'mes_pagado' => now()->startOfMonth(),
            'fecha_pago' => now(),
            'metodo_pago' => 'transferencia',
            'referencia_externa' => 'REGISTRO-AUTOSERVICIO',
            'estado_pago' => 'pendiente',
            'registrado_por' => null,
        ]);

        $this->assertFalse($instance->fresh()->enPeriodoPrueba());
        $this->assertFalse($instance->fresh()->estaAlDia());
        $this->assertSame('atrasada', $instance->fresh()->estadoSuscripcion());
        $this->assertTrue($instance->fresh()->bloqueablePorImpago());
    }

    public function test_pago_confirmado_mantiene_al_dia(): void
    {
        $instance = $this->crearInstancia([
            'trial_ends_at' => now()->subMonth(),
            'fecha_vencimiento' => now()->addDays(20),
        ]);

        PagoInstancia::create([
            'business_instance_id' => $instance->id,
            'plan_id' => $instance->plan_id,
            'monto' => 1500,
            'mes_pagado' => now()->startOfMonth(),
            'fecha_pago' => now(),
            'metodo_pago' => 'transferencia',
            'referencia_externa' => 'CONF-1',
            'estado_pago' => 'completado',
            'registrado_por' => null,
        ]);

        $this->assertFalse($instance->fresh()->enPeriodoPrueba());
        $this->assertTrue($instance->fresh()->estaAlDia());
        $this->assertSame('activa', $instance->fresh()->estadoSuscripcion());
        $this->assertFalse($instance->fresh()->bloqueablePorImpago());
    }

    public function test_billing_verificar_bloquea_instancia_vencida(): void
    {
        $instance = $this->crearInstancia([
            'trial_ends_at' => now()->subDays(4),
            'fecha_vencimiento' => now()->subDays(4),
        ]);

        $this->artisan('billing:verificar')->assertSuccessful();

        $this->assertTrue($instance->fresh()->bloqueado);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'INSTANCE_AUTO_BLOCK',
            'model_id' => $instance->id,
        ]);
    }

    public function test_billing_verificar_no_bloquea_durante_la_prueba(): void
    {
        $instance = $this->crearInstancia();

        $this->artisan('billing:verificar')->assertSuccessful();

        $this->assertFalse($instance->fresh()->bloqueado);
    }

    public function test_billing_verificar_envia_recordatorio_a_3_dias(): void
    {
        $instance = $this->crearInstancia([
            'trial_started_at' => now()->subDays(12),
            'trial_ends_at' => now()->addDays(3),
            'fecha_vencimiento' => now()->addDays(3),
        ]);
        $this->crearUsuarioAdmin($instance);

        $this->artisan('billing:verificar')->assertSuccessful();

        $this->assertDatabaseHas('user_notifications', [
            'type' => 'subscription_expiring',
            'tenant_id' => $instance->id,
        ]);

        // No debe volver a enviarse en la siguiente ejecución (deduplicado).
        $this->artisan('billing:verificar')->assertSuccessful();
        $this->assertSame(1, UserNotification::where('type', 'subscription_expiring')
            ->where('tenant_id', $instance->id)
            ->count());
    }

    public function test_instancia_bloqueada_redirige_pero_suscripcion_es_accesible(): void
    {
        $instance = $this->crearInstancia([
            'bloqueado' => true,
            'motivo_bloqueo' => 'Impago de suscripción',
        ]);
        $user = $this->crearUsuarioAdmin($instance);

        $this->actingAs($user);

        $this->get(route('dashboard'))
            ->assertRedirect(route('instancia-bloqueada'));

        $this->get(route('suscripcion.index'))
            ->assertOk();
    }

    public function test_suscripcion_pagar_crea_pago_pendiente_y_notifica(): void
    {
        $instance = $this->crearInstancia();
        $user = $this->crearUsuarioAdmin($instance);

        $this->actingAs($user)
            ->post(route('suscripcion.pagar'), [
                'monto' => 1500,
                'referencia_externa' => 'REF-ABC-123',
            ])
            ->assertRedirect(route('suscripcion.index'));

        $this->assertDatabaseHas('pagos_instancia', [
            'business_instance_id' => $instance->id,
            'estado_pago' => 'pendiente',
            'referencia_externa' => 'REF-ABC-123',
        ]);

        $this->assertDatabaseHas('user_notifications', [
            'type' => 'payment_received',
            'tenant_id' => $instance->id,
        ]);
    }

    public function test_suscripcion_pagar_rechaza_segundo_pago_pendiente(): void
    {
        $instance = $this->crearInstancia();
        $user = $this->crearUsuarioAdmin($instance);

        PagoInstancia::create([
            'business_instance_id' => $instance->id,
            'plan_id' => $instance->plan_id,
            'monto' => 1500,
            'mes_pagado' => now()->startOfMonth(),
            'fecha_pago' => now(),
            'metodo_pago' => 'transferencia',
            'referencia_externa' => 'PEND-1',
            'estado_pago' => 'pendiente',
            'registrado_por' => $user->id,
        ]);

        $this->actingAs($user)
            ->from(route('suscripcion.index'))
            ->post(route('suscripcion.pagar'), [
                'monto' => 1500,
                'referencia_externa' => 'REF-2',
            ])
            ->assertRedirect(route('suscripcion.index'));

        $this->assertSame(1, PagoInstancia::where('business_instance_id', $instance->id)
            ->where('estado_pago', 'pendiente')
            ->count());
    }

    public function test_owner_confirma_pago_y_desbloquea_instancia(): void
    {
        Role::findOrCreate('owner');
        $owner = User::factory()->create();
        $owner->assignRole('owner');

        $instance = $this->crearInstancia([
            'bloqueado' => true,
            'motivo_bloqueo' => 'Impago de suscripción',
            'trial_ends_at' => now()->subDays(4),
            'fecha_vencimiento' => now()->subDays(4),
        ]);

        $pago = PagoInstancia::create([
            'business_instance_id' => $instance->id,
            'plan_id' => $instance->plan_id,
            'monto' => 1500,
            'mes_pagado' => now()->startOfMonth(),
            'fecha_pago' => now(),
            'metodo_pago' => 'transferencia',
            'referencia_externa' => 'REF-CONF',
            'estado_pago' => 'pendiente',
            'registrado_por' => null,
        ]);

        $this->actingAs($owner)
            ->post(route('owner.instances.pagos.confirmar', [$instance, $pago]))
            ->assertRedirect(route('owner.instances.pagos', $instance->id));

        $this->assertDatabaseHas('pagos_instancia', [
            'id' => $pago->id,
            'estado_pago' => 'completado',
        ]);

        $instance->refresh();
        $this->assertFalse($instance->bloqueado);
        $this->assertNull($instance->motivo_bloqueo);
        $this->assertTrue($instance->estaAlDia());
    }
}