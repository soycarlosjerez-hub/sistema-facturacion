<?php

namespace Tests\Feature;

use App\Models\Mesa;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class RestauranteSinItbisTest extends TestCase
{
    use RefreshDatabase;

    private function setupMesaAbierta(): array
    {
        $session = $this->createBasicTestData();
        session(['sucursal_id' => $session['sucursal']->id]);
        Auth::login($session['user']);

        $session['producto']->update(['precio' => 100.00, 'itbis_porcentaje' => 18, 'stock' => 100]);

        $mesa = Mesa::create([
            'numero'      => '1',
            'capacidad'   => 4,
            'estado'      => 'disponible',
            'sucursal_id' => $session['sucursal']->id,
            'tenant_id'   => $session['businessInstance']->id,
        ]);

        $orden = Venta::create([
            'user_id'             => $session['user']->id,
            'sucursal_id'         => $session['sucursal']->id,
            'mesa_id'             => $mesa->id,
            'caja_id'             => $session['caja']->id,
            'sesion_caja_id'      => $session['sesion']->id,
            'cliente_id'          => $session['consumidorFinal']->id,
            'tipo_venta_id'       => \App\Models\TipoVenta::RESTAURANTE,
            'fecha'               => now(),
            'subtotal'            => 0,
            'impuestos'           => 0,
            'total'               => 0,
            'estado'              => 'abierta',
            'tipo_orden'          => 'mesa',
            'tenant_id'           => $session['businessInstance']->id,
        ]);
        $mesa->update(['estado' => 'ocupada']);

        // Agregar 1 unidad del producto (subtotal 100, itbis 18%)
        $response = $this->actingAs($session['user'])
            ->postJson(route('restaurante.mesa.agregar', $mesa), [
                'producto_id' => $session['producto']->id,
                'cantidad'    => 1,
                'curso'       => 'fuerte',
                'notas'       => '',
            ]);
        $response->assertStatus(200);

        $session['mesa'] = $mesa;
        $session['orden'] = Venta::find($orden->id);
        $session['detalle'] = $session['orden']->detalles()->first();

        return $session;
    }

    private function adminToken(array $session): string
    {
        return Crypt::encryptString(json_encode([
            'email'     => $session['user']->email,
            'tenant_id' => $session['businessInstance']->id,
            'exp'       => now()->addMinutes(5)->timestamp,
        ]));
    }

    public function test_toggle_sin_itbis_requires_admin_token(): void
    {
        $session = $this->setupMesaAbierta();

        $response = $this->actingAs($session['user'])
            ->postJson(route('restaurante.mesa.sin-itbis', [$session['mesa'], $session['detalle']]));

        $response->assertStatus(422);
        $response->assertJson(['error' => 'Se requiere autorización de un administrador para quitar el ITBIS.']);

        $this->assertDatabaseHas('venta_detalles', [
            'id'        => $session['detalle']->id,
            'sin_itbis' => 0,
        ]);
    }

    public function test_toggle_sin_itbis_with_valid_token_recomputes_totals(): void
    {
        $session = $this->setupMesaAbierta();
        $this->assertSame('100.00', $session['orden']->subtotal);
        $this->assertSame('18.00', $session['orden']->impuestos);
        $this->assertSame('118.00', $session['orden']->total);

        $response = $this->actingAs($session['user'])
            ->postJson(route('restaurante.mesa.sin-itbis', [$session['mesa'], $session['detalle']]), [
                'admin_token' => $this->adminToken($session),
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $orden = Venta::find($session['orden']->id);
        $this->assertSame('100.00', $orden->subtotal);
        $this->assertSame('0.00', $orden->impuestos);
        $this->assertSame('100.00', $orden->total);
        $this->assertDatabaseHas('venta_detalles', [
            'id'        => $session['detalle']->id,
            'sin_itbis' => 1,
        ]);
    }

    public function test_toggle_sin_itbis_restores_itbis_when_disabled(): void
    {
        $session = $this->setupMesaAbierta();

        $this->actingAs($session['user'])
            ->postJson(route('restaurante.mesa.sin-itbis', [$session['mesa'], $session['detalle']]), [
                'admin_token' => $this->adminToken($session),
            ])->assertStatus(200);

        // Restaurar ITBIS no requiere token admin
        $response = $this->actingAs($session['user'])
            ->postJson(route('restaurante.mesa.sin-itbis', [$session['mesa'], $session['detalle']]));

        $response->assertStatus(200);

        $orden = Venta::find($session['orden']->id);
        $this->assertSame('100.00', $orden->subtotal);
        $this->assertSame('18.00', $orden->impuestos);
        $this->assertSame('118.00', $orden->total);
        $this->assertDatabaseHas('venta_detalles', [
            'id'        => $session['detalle']->id,
            'sin_itbis' => 0,
        ]);
    }

    public function test_cobrar_with_sin_itbis_requires_admin_token(): void
    {
        $session = $this->setupMesaAbierta();

        $this->actingAs($session['user'])
            ->postJson(route('restaurante.mesa.sin-itbis', [$session['mesa'], $session['detalle']]), [
                'admin_token' => $this->adminToken($session),
            ])->assertStatus(200);

        $response = $this->actingAs($session['user'])
            ->postJson(route('restaurante.mesa.cobrar', $session['mesa']), [
                'metodo_pago'  => 'efectivo',
                'monto_recibido' => 100,
            ]);

        $response->assertStatus(422);
        $response->assertJson(['error' => 'Se requiere autorización de un administrador para quitar el ITBIS.']);

        $this->assertSame('abierta', Venta::find($session['orden']->id)->estado);
    }

    public function test_cobrar_with_sin_itbis_and_valid_token_succeeds(): void
    {
        $session = $this->setupMesaAbierta();

        $this->actingAs($session['user'])
            ->postJson(route('restaurante.mesa.sin-itbis', [$session['mesa'], $session['detalle']]), [
                'admin_token' => $this->adminToken($session),
            ])->assertStatus(200);

        $response = $this->actingAs($session['user'])
            ->postJson(route('restaurante.mesa.cobrar', $session['mesa']), [
                'metodo_pago'    => 'efectivo',
                'monto_recibido' => 100,
                'admin_token'    => $this->adminToken($session),
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $orden = Venta::find($session['orden']->id);
        $this->assertSame('completada', $orden->estado);
        $this->assertSame('100.00', $orden->total);
        $this->assertSame('0.00', $orden->impuestos);
        $this->assertSame('disponible', $session['mesa']->fresh()->estado);
    }

    public function test_facturar_ecf_rejected_when_venta_has_sin_itbis_lines(): void
    {
        $session = $this->setupMesaAbierta();

        $this->actingAs($session['user'])
            ->postJson(route('restaurante.mesa.sin-itbis', [$session['mesa'], $session['detalle']]), [
                'admin_token' => $this->adminToken($session),
            ])->assertStatus(200);

        $this->actingAs($session['user'])
            ->postJson(route('restaurante.mesa.cobrar', $session['mesa']), [
                'metodo_pago'    => 'efectivo',
                'monto_recibido' => 100,
                'admin_token'    => $this->adminToken($session),
            ])->assertStatus(200);

        $response = $this->actingAs($session['user'])
            ->postJson(route('restaurante.mesa.facturar', $session['mesa']), [
                'venta_id' => $session['orden']->id,
            ]);

        $response->assertStatus(422);
        $response->assertJson(['error' => 'No se puede facturar un e-CF con líneas sin ITBIS. Aplica el ITBIS a todas las líneas antes de facturar.']);

        $this->assertNull(Venta::find($session['orden']->id)->ecf);
    }
}