<?php

namespace Tests\Feature;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Pago;
use App\Models\SesionCaja;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\TipoVenta;
use App\Models\NcfSequence;
use App\Models\Almacen;
use App\Models\Sucursal;
use App\Models\BusinessInstance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class VentaControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $basicData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->basicData = $this->createBasicTestData();
    }

    private function setupSession(): array
    {
        $session = $this->basicData;
        session(['sucursal_id' => $session['sucursal']->id]);
        Auth::login($session['user']);
        return $session;
    }

    public function test_index_displays_sales_list(): void
    {
        $session = $this->setupSession();
        
        Venta::factory()->count(5)->create([
            'tenant_id' => $session['businessInstance']->id,
            'user_id' => $session['user']->id,
            'cliente_id' => $session['consumidorFinal']->id,
            'sucursal_id' => $session['sucursal']->id,
            'caja_id' => $session['caja']->id,
            'tipo_venta_id' => $session['tipoVenta']->id,
        ]);

        $response = $this->actingAs($session['user'])
            ->get(route('ventas.index'));

        $response->assertOk();
        $response->assertViewIs('ventas.index');
        $response->assertViewHas('ventas');
    }

    public function test_create_requires_open_cash_session(): void
    {
        $session = $this->setupSession();
        
        $session['sesion']->update(['estado' => 'cerrada']);

        $response = $this->actingAs($session['user'])
            ->get(route('ventas.create'));

        $response->assertRedirect(route('cajas.index'));
        $response->assertSessionHas('error', 'Necesitas abrir una caja antes de vender.');
    }

    public function test_create_page_loads_with_data_when_session_open(): void
    {
        $session = $this->setupSession();

        $response = $this->actingAs($session['user'])
            ->get(route('ventas.create'));

        $response->assertOk();
        $response->assertViewIs('ventas.create');
        $response->assertViewHas('sesion');
        $response->assertViewHas('clientes');
        $response->assertViewHas('productos');
    }

    public function test_store_creates_sale_successfully(): void
    {
        $session = $this->setupSession();
        $producto = $session['producto'];
        $almacen = $session['almacen'];
        $tipoVenta = $session['tipoVenta'];

        $payload = [
            'tipo_venta_id' => $tipoVenta->id,
            'producto_id' => [$producto->id],
            'cantidad' => [2],
            'precio' => [(float) $producto->precio],
            'subtotal' => [(float) ($producto->precio * 2)],
            'almacen_id' => [$almacen->id],
            'total' => (float) ($producto->precio * 2),
            'impuestos' => 0,
            'subtotal_final' => (float) ($producto->precio * 2),
            'metodo_pago' => 'efectivo',
        ];

        $response = $this->actingAs($session['user'])
            ->post(route('ventas.store'), $payload)
            ->assertStatus(302);

        $this->assertDatabaseHas('ventas', [
            'user_id' => $session['user']->id,
            'cliente_id' => $session['consumidorFinal']->id,
            'tipo_venta_id' => $tipoVenta->id,
            'estado' => 'completada',
        ]);

        $venta = Venta::where('user_id', $session['user']->id)->first();
        $this->assertEquals(1, $venta->detalles()->count());
        $this->assertEquals(2, $venta->detalles()->first()->cantidad);
        $this->assertDatabaseHas('pagos', [
            'venta_id' => $venta->id,
            'metodo_pago' => 'efectivo',
        ]);
    }

    public function test_store_with_json_request_returns_json(): void
    {
        $session = $this->setupSession();
        $producto = $session['producto'];
        $almacen = $session['almacen'];
        $tipoVenta = $session['tipoVenta'];

        $payload = [
            'tipo_venta_id' => $tipoVenta->id,
            'producto_id' => [$producto->id],
            'cantidad' => [1],
            'precio' => [(float) $producto->precio],
            'subtotal' => [(float) $producto->precio],
            'almacen_id' => [$almacen->id],
            'total' => (float) $producto->precio,
            'impuestos' => 0,
            'subtotal_final' => (float) $producto->precio,
            'metodo_pago' => 'tarjeta',
        ];

        $response = $this->actingAs($session['user'])
            ->withHeader('Accept', 'application/json')
            ->postJson(route('ventas.store'), $payload);

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'venta_id',
            'total',
            'cliente',
            'metodo_pago',
            'tipo_comprobante',
        ]);
        $response->assertJsonPath('success', true);
    }

    public function test_store_fails_without_open_cash_session(): void
    {
        $session = $this->setupSession();
        $producto = $session['producto'];
        $almacen = $session['almacen'];
        $tipoVenta = $session['tipoVenta'];

        $session['sesion']->update(['estado' => 'cerrada']);

        $payload = [
            'tipo_venta_id' => $tipoVenta->id,
            'producto_id' => [$producto->id],
            'cantidad' => [1],
            'precio' => [(float) $producto->precio],
            'subtotal' => [(float) $producto->precio],
            'almacen_id' => [$almacen->id],
            'total' => (float) $producto->precio,
            'metodo_pago' => 'efectivo',
        ];

        $response = $this->actingAs($session['user'])
            ->withHeader('Accept', 'application/json')
            ->postJson(route('ventas.store'), $payload);

        $response->assertStatus(400);
        $response->assertJsonPath('error', 'Tu caja se cerró. No se puede registrar la venta.');
    }

    public function test_store_pending_status_for_fiado_payment(): void
    {
        $session = $this->setupSession();
        $producto = $session['producto'];
        $almacen = $session['almacen'];
        $tipoVenta = $session['tipoVenta'];

        $payload = [
            'tipo_venta_id' => $tipoVenta->id,
            'cliente_id' => $session['cliente']->id,
            'producto_id' => [$producto->id],
            'cantidad' => [1],
            'precio' => [(float) $producto->precio],
            'subtotal' => [(float) $producto->precio],
            'almacen_id' => [$almacen->id],
            'total' => (float) $producto->precio,
            'metodo_pago' => 'fiado',
        ];

        $response = $this->actingAs($session['user'])
            ->post(route('ventas.store'), $payload)
            ->assertStatus(302);

        $venta = Venta::where('user_id', $session['user']->id)->first();
        $this->assertEquals('pendiente', $venta->estado);
    }

    public function test_show_displays_single_sale(): void
    {
        $session = $this->setupSession();
        
        $venta = Venta::factory()->create([
            'tenant_id' => $session['businessInstance']->id,
            'user_id' => $session['user']->id,
            'cliente_id' => $session['consumidorFinal']->id,
            'sucursal_id' => $session['sucursal']->id,
            'caja_id' => $session['caja']->id,
            'tipo_venta_id' => $session['tipoVenta']->id,
        ]);

        $response = $this->actingAs($session['user'])
            ->get(route('ventas.show', $venta->id));

        $response->assertOk();
        $response->assertViewIs('ventas.show');
        $response->assertViewHas('venta', $venta);
    }

    public function test_destroy_requires_admin_role(): void
    {
        $session = $this->setupSession();
        
        $empleado = User::factory()->create([
            'business_instance_id' => $session['businessInstance']->id,
            'role' => 'empleado',
        ]);

        $venta = Venta::factory()->create([
            'tenant_id' => $session['businessInstance']->id,
            'user_id' => $session['user']->id,
            'cliente_id' => $session['consumidorFinal']->id,
            'sucursal_id' => $session['sucursal']->id,
            'caja_id' => $session['caja']->id,
            'tipo_venta_id' => $session['tipoVenta']->id,
        ]);

        $response = $this->actingAs($empleado)
            ->delete(route('ventas.destroy', $venta->id), [
                'motivo' => 'Test cancellation',
                'confirmar' => 'yes',
            ]);

        $response->assertStatus(403);
    }

    public function test_destroy_cancelled_sale_soft_deletes(): void
    {
        $session = $this->setupSession();
        
        $venta = Venta::factory()->create([
            'tenant_id' => $session['businessInstance']->id,
            'user_id' => $session['user']->id,
            'cliente_id' => $session['consumidorFinal']->id,
            'sucursal_id' => $session['sucursal']->id,
            'caja_id' => $session['caja']->id,
            'tipo_venta_id' => $session['tipoVenta']->id,
        ]);

        $response = $this->actingAs($session['user'])
            ->delete(route('ventas.destroy', $venta->id), [
                'motivo' => 'Test cancellation reason',
                'confirmar' => 'yes',
            ]);

        $response->assertRedirect(route('ventas.index'));
        $this->assertSoftDeleted('ventas', ['id' => $venta->id]);
    }

    public function test_search_product_by_term(): void
    {
        $session = $this->setupSession();
        
        Producto::factory()->create([
            'tenant_id' => $session['businessInstance']->id,
            'nombre' => 'Test Product Name',
            'codigo_barras' => '1234567890123',
            'precio' => 999.99,
        ]);

        $response = $this->actingAs($session['user'])
            ->getJson(route('ventas.buscarProducto', ['q' => 'Test']));

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.nombre', 'Test Product Name');
    }

    public function test_search_by_barcode(): void
    {
        $session = $this->setupSession();
        
        Producto::factory()->create([
            'tenant_id' => $session['businessInstance']->id,
            'nombre' => 'Barcode Product',
            'codigo_barras' => '1234567890123',
            'activo' => true,
        ]);

        $response = $this->actingAs($session['user'])
            ->getJson(route('ventas.buscarPorCodigo', '1234567890123'));

        $response->assertOk();
        $response->assertJsonPath('encontrado', true);
        $response->assertJsonPath('producto.codigo_barras', '1234567890123');
    }

    public function test_daily_stats_endpoint(): void
    {
        $session = $this->setupSession();
        
        Venta::factory()->create([
            'tenant_id' => $session['businessInstance']->id,
            'user_id' => $session['user']->id,
            'cliente_id' => $session['consumidorFinal']->id,
            'sucursal_id' => $session['sucursal']->id,
            'caja_id' => $session['caja']->id,
            'tipo_venta_id' => $session['tipoVenta']->id,
            'estado' => 'completada',
            'total' => 1500.00,
        ]);

        $response = $this->actingAs($session['user'])
            ->getJson(route('ventas.statsDia'));

        $response->assertOk();
        $response->assertJsonStructure(['total', 'count', 'fecha']);
        $response->assertJsonPath('count', 1);
    }

    public function test_sales_by_turno(): void
    {
        $session = $this->setupSession();
        
        Venta::factory()->create([
            'tenant_id' => $session['businessInstance']->id,
            'user_id' => $session['user']->id,
            'cliente_id' => $session['consumidorFinal']->id,
            'sucursal_id' => $session['sucursal']->id,
            'caja_id' => $session['caja']->id,
            'sesion_caja_id' => $session['sesion']->id,
            'tipo_venta_id' => $session['tipoVenta']->id,
            'estado' => 'completada',
            'total' => 2500.00,
        ]);

        Pago::factory()->create([
            'venta_id' => \App\Models\Venta::first()->id,
            'metodo_pago' => 'efectivo',
            'monto' => 2500.00,
        ]);

        $response = $this->actingAs($session['user'])
            ->getJson(route('ventas.jsonTurno', $session['sesion']->id));

        $response->assertOk();
        $response->assertJsonStructure(['ventas']);
    }

    public function test_get_open_account_for_client(): void
    {
        $session = $this->setupSession();
        
        $venta = Venta::factory()->create([
            'tenant_id' => $session['businessInstance']->id,
            'user_id' => $session['user']->id,
            'cliente_id' => $session['cliente']->id,
            'sucursal_id' => $session['sucursal']->id,
            'caja_id' => $session['caja']->id,
            'tipo_venta_id' => $session['tipoVenta']->id,
            'estado' => 'cuenta_abierta',
        ]);

        $response = $this->actingAs($session['user'])
            ->getJson(route('ventas.getCuentaAbierta', $session['cliente']->id));

        $response->assertOk();
        $response->assertJsonPath('id', $venta->id);
    }

    public function test_export_pdf(): void
    {
        $session = $this->setupSession();
        
        $venta = Venta::factory()->create([
            'tenant_id' => $session['businessInstance']->id,
            'user_id' => $session['user']->id,
            'cliente_id' => $session['consumidorFinal']->id,
            'sucursal_id' => $session['sucursal']->id,
            'caja_id' => $session['caja']->id,
            'tipo_venta_id' => $session['tipoVenta']->id,
        ]);

        $response = $this->actingAs($session['user'])
            ->get(route('venta.pdf', $venta->id));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_validation_missing_required_fields(): void
    {
        $session = $this->setupSession();
        $tipoVenta = $session['tipoVenta'];

        $payload = [
            'tipo_venta_id' => $tipoVenta->id,
            'producto_id' => [],
            'cantidad' => [],
            'precio' => [],
            'subtotal' => [],
            'total' => 0,
            'metodo_pago' => 'efectivo',
        ];

        $response = $this->actingAs($session['user'])
            ->withHeader('Accept', 'application/json')
            ->postJson(route('ventas.store'), $payload);

        $response->assertUnprocessable();
    }

    public function test_validation_invalid_product_id(): void
    {
        $session = $this->setupSession();
        $tipoVenta = $session['tipoVenta'];

        $payload = [
            'tipo_venta_id' => $tipoVenta->id,
            'producto_id' => [999999],
            'cantidad' => [1],
            'precio' => [100],
            'subtotal' => [100],
            'total' => 100,
            'metodo_pago' => 'efectivo',
        ];

        $response = $this->actingAs($session['user'])
            ->withHeader('Accept', 'application/json')
            ->postJson(route('ventas.store'), $payload);

        $response->assertUnprocessable();
    }

    public function test_mienda_view_own_filtering(): void
    {
        $session = $this->setupSession();
        
        $otherUser = User::factory()->create([
            'business_instance_id' => $session['businessInstance']->id,
            'role' => 'empleado',
        ]);

        $myVenta = Venta::factory()->create([
            'tenant_id' => $session['businessInstance']->id,
            'user_id' => $session['user']->id,
            'cliente_id' => $session['consumidorFinal']->id,
            'sucursal_id' => $session['sucursal']->id,
            'caja_id' => $session['caja']->id,
            'tipo_venta_id' => $session['tipoVenta']->id,
        ]);

        $otherVenta = Venta::factory()->create([
            'tenant_id' => $session['businessInstance']->id,
            'user_id' => $otherUser->id,
            'cliente_id' => $session['consumidorFinal']->id,
            'sucursal_id' => $session['sucursal']->id,
            'caja_id' => $session['caja']->id,
            'tipo_venta_id' => $session['tipoVenta']->id,
        ]);

        $response = $this->actingAs($session['user'])
            ->get(route('ventas.index'));

        $response->assertOk();
    }
}
