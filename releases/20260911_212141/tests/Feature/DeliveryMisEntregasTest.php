<?php

namespace Tests\Feature;

use App\Models\Almacen;
use App\Models\AlmacenMovimiento;
use App\Models\BusinessInstance;
use App\Models\BusinessType;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\DeliveryTracking;
use App\Models\Producto;
use App\Models\SesionCaja;
use App\Models\Sucursal;
use App\Models\TipoVenta;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DeliveryMisEntregasTest extends TestCase
{
    /** @var array */
    protected $session = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Crear BusinessType primero
        $businessType = BusinessType::create(['nombre' => 'Restaurante', 'activo' => true]);

        // Crear datos base mínimos
        $businessInstance = BusinessInstance::create([
            'business_type_id' => $businessType->id,
            'nombre' => 'Test Business',
            'estado' => 'activo',
        ]);

        $sucursal = Sucursal::create([
            'tenant_id' => $businessInstance->id,
            'nombre' => 'Test Sucursal',
            'business_instance_id' => $businessInstance->id,
        ]);

        $caja = Caja::create([
            'tenant_id' => $businessInstance->id,
            'sucursal_id' => $sucursal->id,
            'codigo' => '001',
            'business_instance_id' => $businessInstance->id,
        ]);

        $sesion = SesionCaja::create([
            'tenant_id' => $businessInstance->id,
            'caja_id' => $caja->id,
            'user_id' => null,
            'estado' => 'abierta',
            'business_instance_id' => $businessInstance->id,
        ]);

        $consumidorFinal = Cliente::create([
            'tenant_id' => $businessInstance->id,
            'nombre' => 'Consumidor Final',
            'nit' => '111-11111-1',
            'es_cliente_frecuente' => false,
            'business_instance_id' => $businessInstance->id,
        ]);

        $producto = Producto::create([
            'tenant_id' => $businessInstance->id,
            'nombre' => 'Producto Test',
            'precio_compra' => 100,
            'precio_venta' => 150,
            'costo_promedio' => 100,
            'iva' => true,
            'codigo_barras' => '123456',
            'sku' => 'SKU001',
            'business_instance_id' => $businessInstance->id,
        ]);

        $almacen = Almacen::create([
            'tenant_id' => $businessInstance->id,
            'nombre' => 'Almacen Test',
            'sucursal_id' => $sucursal->id,
            'business_instance_id' => $businessInstance->id,
        ]);

        AlmacenMovimiento::create([
            'tenant_id' => $businessInstance->id,
            'producto_id' => $producto->id,
            'almacen_id' => $almacen->id,
            'tipo' => 'entrada',
            'cantidad' => 100,
            'nota' => 'Stock inicial',
            'user_id' => null,
            'business_instance_id' => $businessInstance->id,
        ]);

        $tipoVenta = TipoVenta::create([
            'tenant_id' => $businessInstance->id,
            'nombre' => 'Venta Test',
            'business_instance_id' => $businessInstance->id,
        ]);

        $adminUser = User::create([
            'business_instance_id' => $businessInstance->id,
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        Auth::login($adminUser);

        $this->session = [
            'businessInstance' => $businessInstance,
            'sucursal' => $sucursal,
            'caja' => $caja,
            'sesion' => $sesion,
            'consumidorFinal' => $consumidorFinal,
            'producto' => $producto,
            'almacen' => $almacen,
            'tipoVenta' => $tipoVenta,
            'adminUser' => $adminUser,
        ];

        // Crear rol delivery y permisos
        $role = Role::firstOrCreate(['name' => 'delivery', 'guard_name' => 'web']);

        Permission::firstOrCreate(['name' => 'dashboard.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delivery-mis-entregas.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delivery-tracking.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delivery-tracking.assign', 'guard_name' => 'web']);

        $role->syncPermissions(['delivery-mis-entregas.view', 'dashboard.view']);
    }

    private function createDeliveryUser(): User
    {
        $user = User::create([
            'business_instance_id' => $this->session['businessInstance']->id,
            'name' => 'Driver Test',
            'email' => 'driver@test.com',
            'password' => bcrypt('password'),
            'role' => 'delivery',
        ]);
        $user->assignRole('delivery');

        SesionCaja::create([
            'tenant_id' => $this->session['businessInstance']->id,
            'caja_id' => $this->session['caja']->id,
            'user_id' => $user->id,
            'estado' => 'abierta',
            'business_instance_id' => $this->session['businessInstance']->id,
        ]);

        return $user;
    }

    private function createDeliveryVenta(User $driver): Venta
    {
        $venta = Venta::create([
            'tenant_id' => $this->session['businessInstance']->id,
            'business_instance_id' => $this->session['businessInstance']->id,
            'user_id' => $driver->id,
            'driver_id' => $driver->id,
            'tipo_orden' => 'delivery',
            'estado' => 'pendiente',
            'cliente_id' => $this->session['consumidorFinal']->id,
            'sucursal_id' => $this->session['sucursal']->id,
            'caja_id' => $this->session['caja']->id,
            'tipo_venta_id' => $this->session['tipoVenta']->id,
            'total' => 500,
            'subtotal' => 500,
        ]);

        return $venta;
    }

    public function test_driver_can_access_mis_entregas_page(): void
    {
        $driver = $this->createDeliveryUser();
        $this->createDeliveryVenta($driver);

        $response = $this->actingAs($driver)
            ->get(route('delivery-mis-entregas'));

        $response->assertOk();
        $response->assertViewIs('delivery-mis-entregas.index');
        $response->assertViewHas('ventas');
    }

    public function test_driver_cannot_access_without_permission(): void
    {
        $userWithoutPermission = User::create([
            'business_instance_id' => $this->session['businessInstance']->id,
            'name' => 'User Test',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'role' => 'empleado',
        ]);

        $response = $this->actingAs($userWithoutPermission)
            ->get(route('delivery-mis-entregas'));

        $response->assertRedirect();
    }

    public function test_mis_entregas_shows_assigned_ventas(): void
    {
        $driver = $this->createDeliveryUser();
        $venta1 = $this->createDeliveryVenta($driver);
        $venta2 = $this->createDeliveryVenta($driver);

        $response = $this->actingAs($driver)
            ->get(route('delivery-mis-entregas'));

        $response->assertOk();
        $response->assertViewHas('ventas', function ($ventas) {
            return $ventas->count() === 2;
        });
        $response->assertSee($venta1->total);
        $response->assertSee($venta2->total);
    }

    public function test_driver_can_change_status_to_en_camino(): void
    {
        $driver = $this->createDeliveryUser();
        $venta = $this->createDeliveryVenta($driver);

        $tracking = DeliveryTracking::create([
            'venta_id' => $venta->id,
            'driver_id' => $driver->id,
            'status' => 'creado',
            'tenant_id' => $venta->tenant_id,
            'business_instance_id' => $venta->business_instance_id,
        ]);

        $response = $this->actingAs($driver)
            ->patch(route('delivery-mis-entregas.updateStatus', $tracking), [
                'status' => 'en_camino',
                'notas' => 'En camino al cliente',
            ]);

        $response->assertRedirect(route('delivery-mis-entregas'));
        $response->assertSessionHas('success');

        $tracking->refresh();
        $this->assertEquals('en_camino', $tracking->status);
        $this->assertEquals('En camino al cliente', $tracking->notas);
    }

    public function test_driver_can_change_status_to_entregado(): void
    {
        $driver = $this->createDeliveryUser();
        $venta = $this->createDeliveryVenta($driver);
        $venta->update(['estado' => 'pendiente']);

        $tracking = DeliveryTracking::create([
            'venta_id' => $venta->id,
            'driver_id' => $driver->id,
            'status' => 'en_camino',
            'tenant_id' => $venta->tenant_id,
            'business_instance_id' => $venta->business_instance_id,
        ]);

        $response = $this->actingAs($driver)
            ->patch(route('delivery-mis-entregas.updateStatus', $tracking), [
                'status' => 'entregado',
                'notas' => 'Entregado correctamente',
            ]);

        $response->assertRedirect(route('delivery-mis-entregas'));
        $response->assertSessionHas('success');

        $tracking->refresh();
        $venta->refresh();

        $this->assertEquals('entregado', $tracking->status);
        $this->assertEquals('cobrada', $venta->estado);
    }

    public function test_driver_can_change_status_to_fallido(): void
    {
        $driver = $this->createDeliveryUser();
        $venta = $this->createDeliveryVenta($driver);
        $venta->update(['estado' => 'pendiente']);

        $tracking = DeliveryTracking::create([
            'venta_id' => $venta->id,
            'driver_id' => $driver->id,
            'status' => 'en_camino',
            'tenant_id' => $venta->tenant_id,
            'business_instance_id' => $venta->business_instance_id,
        ]);

        $response = $this->actingAs($driver)
            ->patch(route('delivery-mis-entregas.updateStatus', $tracking), [
                'status' => 'fallido',
                'notas' => 'Cliente no encontrado',
            ]);

        $response->assertRedirect(route('delivery-mis-entregas'));
        $response->assertSessionHas('success');

        $tracking->refresh();

        $this->assertEquals('fallido', $tracking->status);
        $this->assertEquals('Cliente no encontrado', $tracking->notas);
    }

    public function test_driver_cannot_change_other_drivers_ventas(): void
    {
        $driver1 = $this->createDeliveryUser();
        $driver2 = $this->createDeliveryUser();
        $venta = $this->createDeliveryVenta($driver2);

        $tracking = DeliveryTracking::create([
            'venta_id' => $venta->id,
            'driver_id' => $driver2->id,
            'status' => 'creado',
            'tenant_id' => $venta->tenant_id,
            'business_instance_id' => $venta->business_instance_id,
        ]);

        $response = $this->actingAs($driver1)
            ->patch(route('delivery-mis-entregas.updateStatus', $tracking), [
                'status' => 'en_camino',
            ]);

        $response->assertStatus(403);

        $tracking->refresh();
        $this->assertEquals('creado', $tracking->status);
    }

    public function test_mis_entregas_filters_only_assigned_ventas(): void
    {
        $driver1 = $this->createDeliveryUser();
        $driver2 = $this->createDeliveryUser();

        $this->createDeliveryVenta($driver1);
        $this->createDeliveryVenta($driver2);

        $response = $this->actingAs($driver1)
            ->get(route('delivery-mis-entregas'));

        $response->assertOk();
        $response->assertViewHas('ventas', function ($ventas) use ($driver1) {
            // Solo debe ver las ventas asignadas a driver1
            return $ventas->filter(fn ($v) => $v->driver_id == $driver1->id)->count() === $ventas->count();
        });
    }

    public function test_mis_entregas_shows_summary_counts(): void
    {
        $driver = $this->createDeliveryUser();
        $this->createDeliveryVenta($driver);
        $this->createDeliveryVenta($driver);

        $response = $this->actingAs($driver)
            ->get(route('delivery-mis-entregas'));

        $response->assertOk();
        $response->assertViewHas('pendientes');
        $response->assertViewHas('enCamino');
        $response->assertViewHas('entregadasHoy');
        $response->assertViewHas('ventas');
    }

    public function test_validation_requires_status(): void
    {
        $driver = $this->createDeliveryUser();
        $venta = $this->createDeliveryVenta($driver);

        $tracking = DeliveryTracking::create([
            'venta_id' => $venta->id,
            'driver_id' => $driver->id,
            'status' => 'creado',
            'tenant_id' => $venta->tenant_id,
            'business_instance_id' => $venta->business_instance_id,
        ]);

        $response = $this->actingAs($driver)
            ->patch(route('delivery-mis-entregas.updateStatus', $tracking), []);

        $response->assertStatus(302); // Redirección por validación fallida
        $response->assertSessionHasErrors('status');
    }

    public function test_validation_rejects_invalid_status(): void
    {
        $driver = $this->createDeliveryUser();
        $venta = $this->createDeliveryVenta($driver);

        $tracking = DeliveryTracking::create([
            'venta_id' => $venta->id,
            'driver_id' => $driver->id,
            'status' => 'creado',
            'tenant_id' => $venta->tenant_id,
            'business_instance_id' => $venta->business_instance_id,
        ]);

        $response = $this->actingAs($driver)
            ->patch(route('delivery-mis-entregas.updateStatus', $tracking), [
                'status' => 'estado_invalido',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('status');
    }
}
