<?php

namespace Tests\Feature;

use App\Models\AlmacenMovimiento;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiVentaControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $session;

    protected function setUp(): void
    {
        parent::setUp();
        $this->session = $this->createBasicTestData();
    }

    private function tokenFor(array $session, array $userOverrides = []): string
    {
        $user = $session['user'];

        if ($userOverrides) {
            $user = User::factory()->create(array_merge([
                'business_instance_id' => $session['businessInstance']->id,
                'role' => 'empleado',
            ], $userOverrides));
        }

        return $user->createToken('test-api')->plainTextToken;
    }

    private function payload(array $session, array $overrides = []): array
    {
        $producto = $session['producto'];

        return array_merge([
            'ncf' => 'B0100000001',
            'ncf_tipo' => 'B01',
            'tipo_comprobante' => 'ncf',
            'user_id' => $session['user']->id,
            'caja_id' => $session['caja']->id,
            'sucursal_id' => $session['sucursal']->id,
            'tipo_venta_id' => $session['tipoVenta']->id,
            'subtotal' => (float) $producto->precio,
            'impuestos' => 0,
            'descuento' => 0,
            'propina' => 0,
            'total' => (float) $producto->precio,
            'estado' => 'completada',
            'detalles' => [
                [
                    'producto_id' => $producto->id,
                    'cantidad' => 1,
                    'precio_unitario' => (float) $producto->precio,
                    'descuento' => 0,
                    'descuento_tipo' => 'monto',
                    'almacen_id' => $session['almacen']->id,
                ],
            ],
        ], $overrides);
    }

    public function test_api_store_creates_sale_with_authoritative_totals(): void
    {
        $session = $this->session;
        $session['producto']->update(['precio' => 100.00, 'itbis_porcentaje' => 18]);

        $response = $this->withToken($this->tokenFor($session))
            ->postJson('/api/sales', $this->payload($session));

        $response->assertCreated();
        $response->assertJsonPath('data.estado', 'completada');

        $this->assertDatabaseHas('venta_detalles', [
            'producto_id' => $session['producto']->id,
            'precio_unitario' => '100.00',
            'subtotal' => '100.00',
        ]);

        $venta = Venta::where('user_id', $session['user']->id)->first();
        $this->assertNotNull($venta);
        $this->assertSame($session['businessInstance']->id, $venta->tenant_id);
        $this->assertSame('100.00', $venta->subtotal);
        $this->assertSame('18.00', $venta->impuestos);
        $this->assertSame('118.00', $venta->total);
    }

    public function test_api_store_rejects_price_override_for_non_privileged(): void
    {
        $session = $this->session;
        $session['producto']->update(['precio' => 50.00, 'itbis_porcentaje' => 0]);

        $token = $this->tokenFor($session, ['role' => 'empleado']);

        $payload = $this->payload($session);
        $payload['detalles'][0]['precio_unitario'] = 250.00;

        $response = $this->withToken($token)->postJson('/api/sales', $payload);

        $response->assertStatus(422);
        $response->assertJsonPath('message', 'No autorizado para modificar el precio de "' . $session['producto']->nombre . '".');

        $this->assertDatabaseMissing('ventas', [
            'user_id' => $session['user']->id,
        ]);
    }

    public function test_api_store_allows_price_override_for_admin(): void
    {
        $session = $this->session;
        $session['producto']->update(['precio' => 50.00, 'itbis_porcentaje' => 0]);

        $payload = $this->payload($session);
        $payload['detalles'][0]['precio_unitario'] = 250.00;

        $response = $this->withToken($this->tokenFor($session))
            ->postJson('/api/sales', $payload);

        $response->assertCreated();

        $this->assertDatabaseHas('venta_detalles', [
            'producto_id' => $session['producto']->id,
            'precio_unitario' => '250.00',
        ]);

        $venta = Venta::where('user_id', $session['user']->id)->first();
        $this->assertSame('250.00', $venta->total);
    }

    public function test_api_store_rejects_insufficient_stock(): void
    {
        $session = $this->session;

        $payload = $this->payload($session);
        $payload['detalles'][0]['cantidad'] = 999;

        $response = $this->withToken($this->tokenFor($session))
            ->postJson('/api/sales', $payload);

        $response->assertStatus(422);
        $response->assertJsonPath('message', 'Stock insuficiente para: ' . $session['producto']->nombre . ' (Disponible en almacén: 100, Stock global: 100)');

        $this->assertDatabaseMissing('ventas', [
            'user_id' => $session['user']->id,
        ]);
        $this->assertDatabaseMissing('almacen_movimientos', [
            'producto_id' => $session['producto']->id,
            'tipo' => 'salida',
        ]);
    }

    public function test_api_store_rejects_discount_above_50_for_non_privileged(): void
    {
        $session = $this->session;
        $session['producto']->update(['precio' => 100.00, 'itbis_porcentaje' => 0]);

        $token = $this->tokenFor($session, ['role' => 'empleado']);

        $payload = $this->payload($session);
        $payload['detalles'][0]['descuento'] = 60;
        $payload['detalles'][0]['descuento_tipo'] = 'porcentaje';

        $response = $this->withToken($token)->postJson('/api/sales', $payload);

        $response->assertStatus(422);
        $response->assertJsonPath('message', 'Descuentos superiores al 50% requieren autorización de administrador.');
    }

    public function test_api_store_persists_line_discount_and_itbis(): void
    {
        $session = $this->session;
        $session['producto']->update(['precio' => 100.00, 'itbis_porcentaje' => 18]);

        $payload = $this->payload($session);
        $payload['detalles'][0]['descuento'] = 10;
        $payload['detalles'][0]['descuento_tipo'] = 'porcentaje';

        $response = $this->withToken($this->tokenFor($session))
            ->postJson('/api/sales', $payload);

        $response->assertCreated();

        $this->assertDatabaseHas('venta_detalles', [
            'producto_id' => $session['producto']->id,
            'descuento' => '10.00',
            'descuento_tipo' => 'porcentaje',
            'itbis_porcentaje' => '18.00',
        ]);

        $venta = Venta::where('user_id', $session['user']->id)->first();
        $this->assertNotNull($venta);
        $this->assertSame('100.00', $venta->subtotal);
        $this->assertSame('10.00', $venta->descuento);
        $this->assertSame('16.20', $venta->impuestos);
        $this->assertSame('106.20', $venta->total);
    }

    public function test_api_store_decrements_stock_and_creates_salida(): void
    {
        $session = $this->session;
        $session['producto']->update(['itbis_porcentaje' => 0]);

        $payload = $this->payload($session);
        $payload['detalles'][0]['cantidad'] = 3;

        $response = $this->withToken($this->tokenFor($session))
            ->postJson('/api/sales', $payload);

        $response->assertCreated();

        $this->assertSame(97, $session['producto']->fresh()->stock);

        $this->assertDatabaseHas('almacen_movimientos', [
            'producto_id' => $session['producto']->id,
            'almacen_id' => $session['almacen']->id,
            'tipo' => 'salida',
            'cantidad' => 3,
        ]);

        $salidas = AlmacenMovimiento::where('producto_id', $session['producto']->id)
            ->where('tipo', 'salida')
            ->count();
        $this->assertSame(1, $salidas);
    }
}
