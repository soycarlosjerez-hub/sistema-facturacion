<?php

namespace Tests\Feature;

use App\Models\Mesa;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RestauranteCocinaTest extends TestCase
{
    use RefreshDatabase;

    private function setupMesaAbierta(int $productos = 1): array
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

        for ($i = 1; $i <= $productos; $i++) {
            $producto = $i === 1 ? $session['producto'] : Producto::factory()->create([
                'tenant_id'         => $session['businessInstance']->id,
                'precio'            => 100.00,
                'itbis_porcentaje'  => 18,
                'stock'             => 100,
            ]);
            $this->actingAs($session['user'])
                ->postJson(route('restaurante.mesa.agregar', $mesa), [
                    'producto_id' => $producto->id,
                    'cantidad'    => 1,
                    'curso'       => 'fuerte',
                    'notas'       => '',
                ])->assertStatus(200);
        }

        $session['mesa'] = $mesa;
        $session['orden'] = Venta::find($orden->id);

        return $session;
    }

    public function test_agregar_item_queda_pendiente_y_aparece_en_kds(): void
    {
        $session = $this->setupMesaAbierta();
        $detalle = $session['orden']->detalles()->first();

        $this->assertSame('pendiente', $detalle->estado_cocina);

        $response = $this->actingAs($session['user'])
            ->get(route('restaurante.kds.orders'));

        $response->assertOk();
        $response->assertJsonCount(1, 'ordenes');
        $this->assertSame($session['orden']->id, $response->json('ordenes.0.id'));
    }

    public function test_enviar_cocina_por_detalle_reenvia_no_enviado_y_aparece_en_kds(): void
    {
        $session = $this->setupMesaAbierta();
        $detalle = $session['orden']->detalles()->first();
        $detalle->update(['estado_cocina' => 'no_enviado']);

        $response = $this->actingAs($session['user'])
            ->postJson(route('restaurante.mesa.cocina', $session['mesa']), [
                'detalle_id' => $detalle->id,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'enviados' => 1]);

        $this->assertDatabaseHas('venta_detalles', [
            'id'            => $detalle->id,
            'estado_cocina' => 'pendiente',
        ]);

        $response = $this->actingAs($session['user'])
            ->get(route('restaurante.kds.orders'));
        $response->assertOk();
        $this->assertNotEmpty($response->json('ordenes'));
        $this->assertSame($session['orden']->id, $response->json('ordenes.0.id'));
    }

    public function test_agregar_item_envia_automaticamente_todos_los_platos(): void
    {
        $session = $this->setupMesaAbierta(2);

        $this->assertSame(2, VentaDetalle::where('venta_id', $session['orden']->id)->where('estado_cocina', 'pendiente')->count());
        $this->assertSame(0, VentaDetalle::where('venta_id', $session['orden']->id)->where('estado_cocina', 'no_enviado')->count());

        $response = $this->actingAs($session['user'])
            ->get(route('restaurante.kds.orders'));
        $response->assertOk();
        $this->assertNotEmpty($response->json('ordenes'));
    }

    public function test_kds_muestra_todos_los_platos_enviados_automaticamente(): void
    {
        $session = $this->setupMesaAbierta(2);

        $response = $this->actingAs($session['user'])
            ->get(route('restaurante.kds.orders'));

        $response->assertOk();
        $idsKds = collect($response->json('ordenes.0.cursos.fuerte', []))->pluck('id')->all();
        $this->assertCount(2, $idsKds);
        $this->assertContains($session['orden']->detalles()->get()[0]->id, $idsKds);
        $this->assertContains($session['orden']->detalles()->get()[1]->id, $idsKds);
    }

    public function test_producto_sin_incluir_kds_queda_servido_y_no_aparece_en_kds(): void
    {
        $session = $this->createBasicTestData();
        session(['sucursal_id' => $session['sucursal']->id]);
        Auth::login($session['user']);

        $producto = $session['producto'];
        $producto->update(['precio' => 100.00, 'itbis_porcentaje' => 18, 'stock' => 100, 'incluir_kds' => false]);

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

        $this->actingAs($session['user'])
            ->postJson(route('restaurante.mesa.agregar', $mesa), [
                'producto_id' => $producto->id,
                'cantidad'    => 1,
                'curso'       => 'fuerte',
                'notas'       => '',
            ])->assertStatus(200);

        $detalle = $orden->detalles()->first();
        $this->assertSame('servido', $detalle->estado_cocina);

        $response = $this->actingAs($session['user'])
            ->get(route('restaurante.kds.orders'));
        $response->assertOk();
        $this->assertCount(0, $response->json('ordenes', []));

        // "Enviar a cocina" no tiene nada pendiente por este producto
        $response = $this->actingAs($session['user'])
            ->postJson(route('restaurante.mesa.cocina', $mesa), []);
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'enviados' => 0]);
    }

    public function test_limpiar_kds_marca_todo_como_servido(): void
    {
        $session = $this->setupMesaAbierta(2);

        $response = $this->actingAs($session['user'])
            ->postJson(route('restaurante.kds.limpiar'));

        $response->assertOk();
        $this->assertSame(2, $response->json('limpiados'));

        $this->assertSame(2, VentaDetalle::where('venta_id', $session['orden']->id)->where('estado_cocina', 'servido')->count());

        $response = $this->actingAs($session['user'])
            ->get(route('restaurante.kds.orders'));
        $response->assertOk();
        $this->assertCount(0, $response->json('ordenes', []));
    }

    public function test_producto_store_guarda_incluir_kds(): void
    {
        $session = $this->createBasicTestData();
        Auth::login($session['user']);

        $response = $this->actingAs($session['user'])
            ->post(route('productos.store'), [
                'nombre'        => 'Agua Embotellada',
                'precio'        => 50,
                'stock'         => 20,
                'incluir_kds'   => '0',
                'activo'        => '1',
            ]);

        $response->assertRedirect(route('productos.index'));

        $this->assertDatabaseHas('productos', [
            'nombre'      => 'Agua Embotellada',
            'incluir_kds' => 0,
        ]);
    }
}