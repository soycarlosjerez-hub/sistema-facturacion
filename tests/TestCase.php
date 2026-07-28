<?php

namespace Tests;

use App\Models\BusinessInstance;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.timezone' => 'America/Santo_Domingo']);
    }

    protected function actingAsAdmin(?User $user = null, $guard = null): User
    {
        $user = $user ?? User::factory()->create(['role' => 'admin']);
        Auth::login($user);
        return $user;
    }

    protected function actingAsGerente(?User $user = null, $guard = null): User
    {
        $user = $user ?? User::factory()->create(['role' => 'gerente']);
        Auth::login($user);
        return $user;
    }

    protected function actingAsEmpleado(?User $user = null, $guard = null): User
    {
        $user = $user ?? User::factory()->create(['role' => 'empleado']);
        Auth::login($user);
        return $user;
    }

    protected function createBasicTestData(array $overrides = []): array
    {
        $businessInstance = BusinessInstance::factory()->create();

        $user = User::factory()->create([
            'business_instance_id' => $businessInstance->id,
            'role' => 'admin',
        ]);

        $sucursal = \App\Models\Sucursal::factory()->create([
            'tenant_id' => $businessInstance->id,
            'business_instance_id' => $businessInstance->id,
        ]);

        $caja = \App\Models\Caja::factory()->create([
            'tenant_id' => $businessInstance->id,
            'sucursal_id' => $sucursal->id,
        ]);

        $sesion = \App\Models\SesionCaja::factory()->create([
            'tenant_id' => $businessInstance->id,
            'caja_id' => $caja->id,
            'user_id' => $user->id,
        ]);

        $cliente = \App\Models\Cliente::factory()->create([
            'tenant_id' => $businessInstance->id,
        ]);

        $producto = \App\Models\Producto::factory()->create([
            'tenant_id' => $businessInstance->id,
            'stock' => 100,
        ]);

        $almacen = \App\Models\Almacen::factory()->create([
            'tenant_id' => $businessInstance->id,
            'sucursal_id' => $sucursal->id,
        ]);

        $tipoVenta = \App\Models\TipoVenta::factory()->create();

        $ncfSequence = \App\Models\NcfSequence::factory()->create([
            'tenant_id' => $businessInstance->id,
        ]);

        $consumidorFinal = \App\Models\Cliente::consumidorFinal($businessInstance->id);

        return array_merge([
            'businessInstance' => $businessInstance,
            'user' => $user,
            'sucursal' => $sucursal,
            'caja' => $caja,
            'sesion' => $sesion,
            'cliente' => $cliente,
            'producto' => $producto,
            'almacen' => $almacen,
            'tipoVenta' => $tipoVenta,
            'ncfSequence' => $ncfSequence,
            'consumidorFinal' => $consumidorFinal,
        ], $overrides);
    }

    protected function loginAs(User $user): void
    {
        Auth::login($user);
    }
}
