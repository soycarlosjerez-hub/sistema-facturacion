<?php

namespace Tests\Feature;

use App\Models\BusinessInstance;
use App\Models\BusinessType;
use App\Models\Cliente;
use App\Models\ClientApiToken;
use App\Models\InstanceApiKey;
use App\Models\Orden;
use App\Models\OrdenDetalle;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ClienteApiTest extends TestCase
{
    use RefreshDatabase;

    private string $apiKeyPlain;
    private BusinessInstance $instance;
    private User $staffUser;

    protected function setUp(): void
    {
        parent::setUp();

        $bizType = BusinessType::create([
            'nombre' => 'Default', 'slug' => 'default', 'activo' => true,
        ]);

        $this->staffUser = User::factory()->create();

        $this->instance = BusinessInstance::create([
            'nombre' => 'Test Instance',
            'slug' => 'test-instance-' . Str::random(6),
            'business_type_id' => $bizType->id,
            'owner_user_id' => $this->staffUser->id,
            'activo' => true,
        ]);

        $this->staffUser->update(['business_instance_id' => $this->instance->id]);

        $this->apiKeyPlain = 'iak_test-' . Str::random(20);
        InstanceApiKey::create([
            'business_instance_id' => $this->instance->id,
            'name' => 'Test Key',
            'key' => hash('sha256', $this->apiKeyPlain),
            'is_active' => true,
            'created_by' => $this->staffUser->id,
        ]);
    }

    // ─── helpers ──────────────────────────────────────────────

    private function apiKeyHeaders(): array
    {
        return ['Authorization' => 'Bearer ' . $this->apiKeyPlain];
    }

    private function createClientWithToken(array $overrides = []): array
    {
        $email = $overrides['email'] ?? ('client-' . Str::random(8) . '@test.com');
        $telefono = $overrides['telefono'] ?? ('809555' . Str::random(4));

        $cliente = Cliente::create([
            'nombre' => 'Test Client',
            'email' => $email,
            'telefono' => $telefono,
            'password' => 'Password123!',
            'tenant_id' => $this->instance->id,
            'activo' => true,
            'acceso_api' => $overrides['acceso_api'] ?? true,
            'email_verified_at' => now(),
            'tipo_cliente' => 'consumo',
            'tipo_documento' => '1',
        ]);

        $plain = bin2hex(random_bytes(32));
        $token = ClientApiToken::create([
            'cliente_id' => $cliente->id,
            'name' => 'test-token',
            'token' => hash('sha256', $plain),
            'abilities' => ['*'],
            'expires_at' => $overrides['expires_at'] ?? null,
        ]);
        $token->plain_text = $plain;

        return ['cliente' => $cliente, 'token' => $token, 'plain_text' => $plain];
    }

    private function getClientTokenHeader(array $opts = []): array
    {
        $result = $this->createClientWithToken($opts);
        return [
            'Authorization' => 'Bearer ' . $result['plain_text'],
            '_cliente' => $result['cliente'],
            '_token' => $result['token'],
        ];
    }

    private function bearer(array $headers): array
    {
        return ['Authorization' => $headers['Authorization']];
    }

    // ─── Phase 1: Registration ────────────────────────────────

    public function test_register_requires_api_key(): void
    {
        $response = $this->postJson('/api/auth/cliente/register', [
            'nombre' => 'Test', 'email' => 'a@b.com', 'telefono' => '8095550001',
            'password' => 'SecurePass123!', 'password_confirmation' => 'SecurePass123!',
        ]);
        $response->assertStatus(401);
    }

    public function test_register_success(): void
    {
        $response = $this->withHeaders($this->apiKeyHeaders())
            ->postJson('/api/auth/cliente/register', [
                'nombre' => 'Juan Perez',
                'email' => 'juan@example.com',
                'telefono' => '8095550001',
                'password' => 'SecurePass123!',
                'password_confirmation' => 'SecurePass123!',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message', 'cliente' => ['id', 'nombre', 'email', 'telefono'],
                'access_token', 'token_type',
            ]);
        $this->assertDatabaseHas('clientes', ['email' => 'juan@example.com']);
    }

    public function test_register_duplicate_email(): void
    {
        $this->withHeaders($this->apiKeyHeaders())->postJson('/api/auth/cliente/register', [
            'nombre' => 'First', 'email' => 'dup@example.com', 'telefono' => '8095550001',
            'password' => 'SecurePass123!', 'password_confirmation' => 'SecurePass123!',
        ])->assertStatus(201);

        $response = $this->withHeaders($this->apiKeyHeaders())->postJson('/api/auth/cliente/register', [
            'nombre' => 'Second', 'email' => 'dup@example.com', 'telefono' => '8095550002',
            'password' => 'SecurePass123!', 'password_confirmation' => 'SecurePass123!',
        ]);
        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_register_duplicate_phone(): void
    {
        $this->withHeaders($this->apiKeyHeaders())->postJson('/api/auth/cliente/register', [
            'nombre' => 'First', 'email' => 'a1@example.com', 'telefono' => '8095551111',
            'password' => 'SecurePass123!', 'password_confirmation' => 'SecurePass123!',
        ])->assertStatus(201);

        $response = $this->withHeaders($this->apiKeyHeaders())->postJson('/api/auth/cliente/register', [
            'nombre' => 'Second', 'email' => 'a2@example.com', 'telefono' => '8095551111',
            'password' => 'SecurePass123!', 'password_confirmation' => 'SecurePass123!',
        ]);
        $response->assertStatus(422)->assertJsonValidationErrors(['telefono']);
    }

    // ─── Phase 2: Login ───────────────────────────────────────

    public function test_login_requires_api_key(): void
    {
        $response = $this->postJson('/api/auth/cliente/login', [
            'telefono' => '8095550001', 'password' => 'x',
        ]);
        $response->assertStatus(401);
    }

    public function test_login_success(): void
    {
        Cliente::create([
            'nombre' => 'Test', 'email' => 'login@test.com', 'telefono' => '8095550001',
            'password' => 'CorrectPass123!', 'tenant_id' => $this->instance->id,
            'activo' => true, 'acceso_api' => true, 'email_verified_at' => now(),
            'tipo_cliente' => 'consumo', 'tipo_documento' => '1',
        ]);

        $response = $this->withHeaders($this->apiKeyHeaders())
            ->postJson('/api/auth/cliente/login', [
                'telefono' => '8095550001', 'password' => 'CorrectPass123!',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message', 'cliente' => ['id', 'nombre'], 'access_token', 'token_type',
            ]);
    }

    public function test_login_wrong_password(): void
    {
        Cliente::create([
            'nombre' => 'Test', 'email' => 'wrong@test.com', 'telefono' => '8095550002',
            'password' => 'RealPass123!', 'tenant_id' => $this->instance->id,
            'activo' => true, 'acceso_api' => true, 'email_verified_at' => now(),
            'tipo_cliente' => 'consumo', 'tipo_documento' => '1',
        ]);

        $response = $this->withHeaders($this->apiKeyHeaders())
            ->postJson('/api/auth/cliente/login', [
                'telefono' => '8095550002', 'password' => 'WrongPass123!',
            ]);
        $response->assertStatus(422);
    }

    public function test_login_acceso_api_disabled(): void
    {
        Cliente::create([
            'nombre' => 'Test', 'email' => 'noapi@test.com', 'telefono' => '8095550003',
            'password' => 'Pass1234!', 'tenant_id' => $this->instance->id,
            'activo' => true, 'acceso_api' => false, 'email_verified_at' => now(),
            'tipo_cliente' => 'consumo', 'tipo_documento' => '1',
        ]);

        $response = $this->withHeaders($this->apiKeyHeaders())
            ->postJson('/api/auth/cliente/login', [
                'telefono' => '8095550003', 'password' => 'Pass1234!',
            ]);
        $response->assertStatus(403);
    }

    public function test_login_unverified_email(): void
    {
        Cliente::create([
            'nombre' => 'Test', 'email' => 'unver@test.com', 'telefono' => '8095550004',
            'password' => 'Pass1234!', 'tenant_id' => $this->instance->id,
            'activo' => true, 'acceso_api' => true, 'email_verified_at' => null,
            'tipo_cliente' => 'consumo', 'tipo_documento' => '1',
        ]);

        $response = $this->withHeaders($this->apiKeyHeaders())
            ->postJson('/api/auth/cliente/login', [
                'telefono' => '8095550004', 'password' => 'Pass1234!',
            ]);
        $response->assertStatus(403);
    }

    // ─── Phase 3: Protected routes auth ───────────────────────

    public function test_me_without_token(): void
    {
        $response = $this->getJson('/api/cliente/me');
        $response->assertStatus(401);
    }

    public function test_me_with_invalid_token(): void
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer invalid-token-xxx'])
            ->getJson('/api/cliente/me');
        $response->assertStatus(401);
    }

    public function test_me_with_expired_token(): void
    {
        $headers = $this->getClientTokenHeader([
            'expires_at' => now()->subDay(),
        ]);
        $response = $this->withHeaders($this->bearer($headers))
            ->getJson('/api/cliente/me');
        $response->assertStatus(401);
    }

    public function test_me_with_disabled_accesso_api(): void
    {
        $headers = $this->getClientTokenHeader([
            'acceso_api' => false,
        ]);
        $response = $this->withHeaders($this->bearer($headers))
            ->getJson('/api/cliente/me');
        $response->assertStatus(403);
    }

    public function test_me_success(): void
    {
        $headers = $this->getClientTokenHeader();
        $cliente = $headers['_cliente'];

        $response = $this->withHeaders($this->bearer($headers))
            ->getJson('/api/cliente/me');

        $response->assertStatus(200)
            ->assertJsonPath('cliente.id', $cliente->id)
            ->assertJsonPath('cliente.nombre', $cliente->nombre)
            ->assertJsonPath('cliente.email', $cliente->email);
    }

    // ─── Phase 4: Profile ─────────────────────────────────────

    public function test_update_profile(): void
    {
        $headers = $this->getClientTokenHeader();
        $cliente = $headers['_cliente'];

        $response = $this->withHeaders($this->bearer($headers))
            ->putJson('/api/cliente/profile', [
                'nombre' => 'Updated Name',
                'direccion' => 'Calle Nueva 123',
                'ciudad' => 'Santo Domingo',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('cliente.nombre', 'Updated Name');

        $cliente->refresh();
        $this->assertEquals('Updated Name', $cliente->nombre);
        $this->assertEquals('Calle Nueva 123', $cliente->direccion);
    }

    public function test_update_profile_duplicate_email(): void
    {
        Cliente::create([
            'nombre' => 'Other', 'email' => 'other@test.com', 'telefono' => '8095559999',
            'password' => 'Pass1234!', 'tenant_id' => $this->instance->id,
            'activo' => true, 'tipo_cliente' => 'consumo', 'tipo_documento' => '1',
        ]);

        $headers = $this->getClientTokenHeader(['email' => 'my@test.com']);

        $response = $this->withHeaders($this->bearer($headers))
            ->putJson('/api/cliente/profile', [
                'email' => 'other@test.com',
            ]);
        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    // ─── Phase 5: Change Password ──────────────────────────────

    public function test_change_password_success(): void
    {
        $result = $this->createClientWithToken();
        $cliente = $result['cliente'];
        $cliente->password = 'CurrentPass1!';
        $cliente->save();

        $plain = $result['plain_text'];
        $headers = ['Authorization' => 'Bearer ' . $plain];

        $response = $this->withHeaders($headers)
            ->postJson('/api/cliente/change-password', [
                'current_password' => 'CurrentPass1!',
                'new_password' => 'NewSecurePass1!',
                'new_password_confirmation' => 'NewSecurePass1!',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'access_token', 'token_type']);

        $this->assertDatabaseMissing('client_api_tokens', ['id' => $result['token']->id]);
    }

    public function test_change_password_wrong_current(): void
    {
        $result = $this->createClientWithToken();
        $cliente = $result['cliente'];
        $cliente->password = 'CorrectPass1!';
        $cliente->save();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $result['plain_text']])
            ->postJson('/api/cliente/change-password', [
                'current_password' => 'WrongPass1!',
                'new_password' => 'NewPass1234!',
                'new_password_confirmation' => 'NewPass1234!',
            ]);
        $response->assertStatus(422);
    }

    // ─── Phase 6: Logout ──────────────────────────────────────

    public function test_logout(): void
    {
        $result = $this->createClientWithToken();
        $headers = ['Authorization' => 'Bearer ' . $result['plain_text']];

        $response = $this->withHeaders($headers)
            ->postJson('/api/cliente/logout');
        $response->assertStatus(200);

        $this->assertDatabaseMissing('client_api_tokens', ['id' => $result['token']->id]);

        // Token no longer works
        $response2 = $this->withHeaders($headers)
            ->getJson('/api/cliente/me');
        $response2->assertStatus(401);
    }

    // ─── Phase 7: Orders ──────────────────────────────────────

    public function test_pedidos_empty(): void
    {
        $headers = $this->getClientTokenHeader();

        $response = $this->withHeaders($this->bearer($headers))
            ->getJson('/api/cliente/pedidos');

        $response->assertStatus(200)
            ->assertJsonPath('pagination.total', 0);
    }

    public function test_pedidos_with_orders(): void
    {
        $headers = $this->getClientTokenHeader();
        $cliente = $headers['_cliente'];

        $sucursal = Sucursal::create([
            'codigo' => 'SUC-' . Str::random(6),
            'nombre' => 'Sucursal Test',
            'tenant_id' => $this->instance->id,
        ]);

        $orden = Orden::create([
            'cliente_id' => $cliente->id,
            'sucursal_id' => $sucursal->id,
            'user_id' => $this->staffUser->id,
            'tipo_orden' => 'delivery',
            'estado' => 'pendiente',
            'subtotal' => 100,
            'impuestos' => 18,
            'total' => 118,
            'tenant_id' => $this->instance->id,
        ]);

        $response = $this->withHeaders($this->bearer($headers))
            ->getJson('/api/cliente/pedidos');

        $response->assertStatus(200)
            ->assertJson(['pagination' => ['total' => 1]])
            ->assertJson(['pedidos' => ['data' => [['id' => $orden->id]]]]);
    }

    public function test_pedidos_show_own_order(): void
    {
        $headers = $this->getClientTokenHeader();
        $cliente = $headers['_cliente'];

        $sucursal = Sucursal::create([
            'codigo' => 'SUC-' . Str::random(6),
            'nombre' => 'Sucursal Test',
            'tenant_id' => $this->instance->id,
        ]);

        $orden = Orden::create([
            'cliente_id' => $cliente->id,
            'sucursal_id' => $sucursal->id,
            'user_id' => $this->staffUser->id,
            'tipo_orden' => 'delivery',
            'estado' => 'pendiente',
            'subtotal' => 100,
            'impuestos' => 18,
            'total' => 118,
            'tenant_id' => $this->instance->id,
        ]);

        $response = $this->withHeaders($this->bearer($headers))
            ->getJson("/api/cliente/pedidos/{$orden->id}");

        $response->assertStatus(200)
            ->assertJsonPath('pedido.id', $orden->id);
    }

    public function test_pedidos_cannot_see_other_client_order(): void
    {
        $headersA = $this->getClientTokenHeader();
        $clienteA = $headersA['_cliente'];

        $headersB = $this->getClientTokenHeader();
        $clienteB = $headersB['_cliente'];

        $sucursal = Sucursal::create([
            'codigo' => 'SUC-' . Str::random(6),
            'nombre' => 'Sucursal Test',
            'tenant_id' => $this->instance->id,
        ]);

        $ordenA = Orden::create([
            'cliente_id' => $clienteA->id,
            'sucursal_id' => $sucursal->id,
            'user_id' => $this->staffUser->id,
            'tipo_orden' => 'delivery',
            'estado' => 'pendiente',
            'subtotal' => 100,
            'impuestos' => 18,
            'total' => 118,
            'tenant_id' => $this->instance->id,
        ]);

        // Client B cannot see A's order
        $response = $this->withHeaders($this->bearer($headersB))
            ->getJson("/api/cliente/pedidos/{$ordenA->id}");
        $response->assertStatus(404);
    }

    public function test_pedidos_not_found(): void
    {
        $headers = $this->getClientTokenHeader();

        $response = $this->withHeaders($this->bearer($headers))
            ->getJson('/api/cliente/pedidos/99999');
        $response->assertStatus(404);
    }

    // ─── Phase 8: Forgot / Reset Password ─────────────────────

    public function test_forgot_password(): void
    {
        $cliente = Cliente::create([
            'nombre' => 'Test', 'email' => 'forgot@test.com', 'telefono' => '8095550001',
            'password' => 'Pass1234!', 'tenant_id' => $this->instance->id,
            'activo' => true, 'tipo_cliente' => 'consumo', 'tipo_documento' => '1',
        ]);

        $response = $this->withHeaders($this->apiKeyHeaders())
            ->postJson('/api/auth/cliente/forgot-password', [
                'email' => 'forgot@test.com',
            ]);
        $response->assertStatus(200);
    }

    public function test_forgot_password_nonexistent_email(): void
    {
        $response = $this->withHeaders($this->apiKeyHeaders())
            ->postJson('/api/auth/cliente/forgot-password', [
                'email' => 'noexiste@test.com',
            ]);
        $response->assertStatus(422);
    }

    // ─── Phase 9: Sanctum token as auth ───────────────────────

    public function test_auth_with_sanctum_token_for_register(): void
    {
        $tokenPlain = $this->staffUser->createToken('test-sanctum')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $tokenPlain])
            ->postJson('/api/auth/cliente/register', [
                'nombre' => 'Sanctum Register',
                'email' => 'sanctum@test.com',
                'telefono' => '8095558888',
                'password' => 'SecurePass123!',
                'password_confirmation' => 'SecurePass123!',
            ]);

        $response->assertStatus(201);
    }

    // ─── Extra: invalid api key ───────────────────────────────

    public function test_invalid_api_key_returns_401(): void
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer iak_invalid-key'])
            ->postJson('/api/auth/cliente/register', [
                'nombre' => 'Test', 'email' => 'test@test.com', 'telefono' => '8095550001',
                'password' => 'SecurePass123!', 'password_confirmation' => 'SecurePass123!',
            ]);
        $response->assertStatus(401);
    }
}
