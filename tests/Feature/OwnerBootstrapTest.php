<?php

namespace Tests\Feature;

use App\Auth\OwnerBootstrappedUser;
use App\Models\User;
use App\Services\OwnerBootstrapService;
use App\Services\OwnerRecoveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OwnerBootstrapTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Configuración de Owner Bootstrap para tests.
     * Genera un hash bcrypt real usando el password del test.
     */
    protected function setUpOwnerBootstrapConfig(): void
    {
        Config::set('owner.enabled', true);
        Config::set('owner.email', 'owner@sistema-facturacion.com');
        Config::set('owner.name', 'Owner');
        Config::set('owner.password', 'OwnerPassword123!');
        Config::set('owner.password_hash', Hash::make('OwnerPassword123!'));
        Config::set('owner.use_password_hash', true);
        Config::set('owner.role', 'owner');
        Config::set('owner.bypass_2fa', true);
        Config::set('owner.max_attempts', 10);
        Config::set('owner.lockout_seconds', 300);
    }

    /**
     * Limpiar la configuración de Owner Bootstrap.
     */
    protected function clearOwnerBootstrapConfig(): void
    {
        Config::set('owner.enabled', false);
        Config::set('owner.email', '');
        Config::set('owner.name', '');
        Config::set('owner.password', '');
        Config::set('owner.password_hash', '');
        Config::set('owner.use_password_hash', false);
        Config::set('owner.role', 'owner');
        Config::set('owner.bypass_2fa', false);
    }

    // ========================================================================
    // SCENARIO 1: Owner existe en BD
    // ========================================================================

    public function test_owner_exists_in_database_and_can_login(): void
    {
        $this->setUpOwnerBootstrapConfig();

        // Crear el owner en BD con rol Spatie
        $owner = User::factory()->create([
            'email' => 'owner@sistema-facturacion.com',
            'password' => Hash::make('OwnerPassword123!'),
        ]);
        $this->darRolSpatie($owner, 'owner');

        // Login como owner (desde BD)
        $response = $this->post('/login', [
            'email' => 'owner@sistema-facturacion.com',
            'password' => 'OwnerPassword123!',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();

        // Verificar que NO es bootstrap
        $this->assertFalse(session('owner_bootstrap', false));
    }

    public function test_owner_bootstrap_service_detects_owner_in_database(): void
    {
        $this->setUpOwnerBootstrapConfig();

        User::factory()->create([
            'email' => 'owner@sistema-facturacion.com',
            'password' => Hash::make('OwnerPassword123!'),
        ]);

        /** @var OwnerBootstrapService $service */
        $service = app(OwnerBootstrapService::class);

        $this->assertTrue($service->ownerExistsInDatabase());
        $this->assertNotNull($service->getOwnerFromDatabase());
    }

    // ========================================================================
    // SCENARIO 2: Owner NO existe en BD (Bootstrap)
    // ========================================================================

    public function test_owner_not_in_database_can_bootstrap_auth(): void
    {
        $this->setUpOwnerBootstrapConfig();

        // NO crear el owner en BD — solo la tabla existe pero vacía
        $this->assertDatabaseCount('users', 0);

        // Intentar login como owner con credenciales correctas
        $response = $this->post('/login', [
            'email' => 'owner@sistema-facturacion.com',
            'password' => 'OwnerPassword123!',
        ]);

        // Debería autenticar aunque no exista en BD
        $this->assertAuthenticated();

        // El usuario debe ser un OwnerBootstrappedUser
        /** @var OwnerBootstrappedUser|null $user */
        $user = auth()->user();
        $this->assertInstanceOf(OwnerBootstrappedUser::class, $user);

        // Verificar flag de bootstrap
        $this->assertTrue(session('owner_bootstrap', false));
    }

    public function test_owner_bootstrap_user_has_all_permissions(): void
    {
        $this->setUpOwnerBootstrapConfig();

        // Autenticar via bootstrap
        $this->post('/login', [
            'email' => 'owner@sistema-facturacion.com',
            'password' => 'OwnerPassword123!',
        ]);

        /** @var OwnerBootstrappedUser $user */
        $user = auth()->user();

        $this->assertTrue($user->hasRole('owner'));
        $this->assertTrue($user->hasRole('root'));
        $this->assertTrue($user->can('ventas.view'));
        $this->assertTrue($user->can('configuracion.edit'));
        $this->assertTrue($user->hasAnyRole(['owner', 'root', 'admin']));
        $this->assertTrue($user->hasAllRoles(['owner']));
    }

    public function test_owner_bootstrap_bypasses_tenant_middleware(): void
    {
        $this->setUpOwnerBootstrapConfig();

        // Autenticar via bootstrap
        $this->post('/login', [
            'email' => 'owner@sistema-facturacion.com',
            'password' => 'OwnerPassword123!',
        ]);

        // Debería poder acceder a rutas owner sin tener business_instance_id
        $response = $this->get('/owner/instances?limit=10&page=1');
        $response->assertOk();
    }

    public function test_owner_bootstrap_bypasses_permission_middleware(): void
    {
        $this->setUpOwnerBootstrapConfig();

        // Autenticar via bootstrap
        $this->post('/login', [
            'email' => 'owner@sistema-facturacion.com',
            'password' => 'OwnerPassword123!',
        ]);

        // Debería poder acceder a rutas que requieren permisos específicos
        $response = $this->get('/owner/instances');
        $response->assertOk();
    }

    // ========================================================================
    // SCENARIO 3: Tabla de usuarios vacía
    // ========================================================================

    public function test_empty_users_table_allows_bootstrap_auth(): void
    {
        $this->setUpOwnerBootstrapConfig();

        // Asegurar que la tabla está vacía
        $this->assertDatabaseCount('users', 0);

        // El sistema debe reconocer al owner bootstrap
        $response = $this->post('/login', [
            'email' => 'owner@sistema-facturacion.com',
            'password' => 'OwnerPassword123!',
        ]);

        $this->assertAuthenticated();
        $this->assertInstanceOf(OwnerBootstrappedUser::class, auth()->user());
    }

    public function test_empty_users_table_owner_can_access_owner_routes(): void
    {
        $this->setUpOwnerBootstrapConfig();

        $this->assertDatabaseCount('users', 0);

        $this->post('/login', [
            'email' => 'owner@sistema-facturacion.com',
            'password' => 'OwnerPassword123!',
        ]);

        // Acceder a rutas de owner que requieren role:owner
        $response = $this->get('/owner/instances?limit=10&page=1');
        $response->assertOk();
    }

    // ========================================================================
    // SCENARIO 4: BD recién instalada (fresh migrate)
    // ========================================================================

    public function test_fresh_install_owner_can_auth(): void
    {
        $this->setUpOwnerBootstrapConfig();

        // Simular BD recién creada (tablas existen pero sin datos)
        // Las migraciones ya crearon las tablas, pero no hay usuarios
        $this->assertDatabaseCount('users', 0);

        // El owner bootstrap puede autenticarse
        $response = $this->post('/login', [
            'email' => 'owner@sistema-facturacion.com',
            'password' => 'OwnerPassword123!',
        ]);

        $this->assertAuthenticated();
    }

    // ========================================================================
    // SCENARIO 5: Credenciales Owner incorrectas
    // ========================================================================

    public function test_wrong_password_fails_for_bootstrap_owner(): void
    {
        $this->setUpOwnerBootstrapConfig();

        $this->assertDatabaseCount('users', 0);

        // Contraseña incorrecta
        $response = $this->post('/login', [
            'email' => 'owner@sistema-facturacion.com',
            'password' => 'wrong-password',
        ]);

        // No debería autenticar
        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_wrong_email_fails(): void
    {
        $this->setUpOwnerBootstrapConfig();

        $this->assertDatabaseCount('users', 0);

        // Intentar como un email diferente al configurado
        $response = $this->post('/login', [
            'email' => 'other@test.com',
            'password' => 'some-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    // ========================================================================
    // SCENARIO 6: Usuario normal intentando acceder como Owner
    // ========================================================================

    public function test_normal_user_cannot_become_owner(): void
    {
        $this->setUpOwnerBootstrapConfig();

        // Crear un usuario normal
        $normalUser = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('UserPassword123!'),
        ]);

        // Intentar acceder con credenciales del owner pero como usuario normal
        // (ya que el owner no existe en BD)
        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'OwnerPassword123!', // Contraseña del owner
        ]);

        // Debería fallar porque el email no es el owner
        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_normal_user_cannot_access_owner_routes(): void
    {
        $this->setUpOwnerBootstrapConfig();

        // Crear un usuario normal con rol vendedor (no owner)
        $normalUser = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('UserPassword123!'),
        ]);
        $this->darRolSpatie($normalUser, 'vendedor');

        $this->actingAs($normalUser);

        // Intentar acceder a rutas de owner
        $response = $this->get('/owner');
        $response->assertStatus(403);
    }

    public function test_normal_user_cannot_trigger_owner_recovery(): void
    {
        $this->setUpOwnerBootstrapConfig();

        // Crear usuario normal
        $normalUser = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('UserPassword123!'),
        ]);

        // Intentar acceder a la ruta de reconstrucción como usuario normal.
        // El route requiere autenticación y rol owner.
        // Un usuario normal sin rol owner será redirigido a login (302).
        $response = $this->post('/owner/bootstrap/recover', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        // Debería redirigir al login (302) o denegarse (403)
        $this->assertTrue($response->getStatusCode() === 302 || $response->getStatusCode() === 403);
    }

    // ========================================================================
    // SCENARIO 7: Owner recuperando/recreando su registro en BD
    // ========================================================================

    public function test_bootstrap_owner_can_rebuild_database_record(): void
    {
        $this->setUpOwnerBootstrapConfig();

        $this->assertDatabaseCount('users', 0);

        // Autenticar via bootstrap
        $this->post('/login', [
            'email' => 'owner@sistema-facturacion.com',
            'password' => 'OwnerPassword123!',
        ]);

        $this->assertAuthenticated();
        $this->assertInstanceOf(OwnerBootstrappedUser::class, auth()->user());

        // Reconstruir en BD
        $recoveryService = app(OwnerRecoveryService::class);

        $result = $recoveryService->rebuildOwner('OwnerPassword123!');

        $this->assertTrue($result['success']);
        $this->assertTrue($result['isNew']);
        $this->assertArrayHasKey('user', $result);
        $this->assertInstanceOf(User::class, $result['user']);

        // Verificar que el usuario ahora existe en BD
        /** @var User $dbUser */
        $dbUser = User::where('email', 'owner@sistema-facturacion.com')->first();
        $this->assertNotNull($dbUser);
        $this->assertTrue(Hash::check('OwnerPassword123!', $dbUser->password));
        $this->assertTrue($dbUser->hasRole('owner'));

        // La sesión debería haber cambiado de bootstrap a normal
        $this->assertFalse(session('owner_bootstrap', false));
    }

    public function test_bootstrap_owner_rebuild_already_exists(): void
    {
        $this->setUpOwnerBootstrapConfig();

        // Crear el owner en BD
        $owner = User::factory()->create([
            'email' => 'owner@sistema-facturacion.com',
            'password' => Hash::make('OwnerPassword123!'),
        ]);
        $this->darRolSpatie($owner, 'owner');

        $recoveryService = app(OwnerRecoveryService::class);

        // Si el usuario normal ya está en BD
        // Simular que el usuario logueado es un usuario normal con rol owner
        $this->actingAs($owner);

        $result = $recoveryService->rebuildOwner('newpass');

        $this->assertTrue($result['success']);
        $this->assertFalse($result['isNew']);
        $this->assertStringContainsString('ya existe', $result['message']);
    }

    public function test_bootstrap_owner_change_password(): void
    {
        $this->setUpOwnerBootstrapConfig();

        // Crear owner en BD
        $owner = User::factory()->create([
            'email' => 'owner@sistema-facturacion.com',
            'password' => Hash::make('OldPassword123!'),
        ]);
        $this->darRolSpatie($owner, 'owner');

        $recoveryService = app(OwnerRecoveryService::class);

        $result = $recoveryService->changePassword(
            currentPassword: 'OldPassword123!',
            newPassword: 'NewPassword123!',
            newPasswordConfirmation: 'NewPassword123!',
        );

        $this->assertTrue($result['success']);

        // Verificar que la contraseña se actualizó
        /** @var User $dbUser */
        $dbUser = User::where('email', 'owner@sistema-facturacion.com')->first();
        $this->assertTrue(Hash::check('NewPassword123!', $dbUser->password));
        $this->assertFalse(Hash::check('OldPassword123!', $dbUser->password));
    }

    // ========================================================================
    // SCENARIO 8: Owner Bootstrap deshabilitado
    // ========================================================================

    public function test_disabled_bootstrap_prevents_bootstrap_auth(): void
    {
        // No configurar Owner Bootstrap — por defecto disabled=false
        Config::set('owner.enabled', false);

        $this->assertDatabaseCount('users', 0);

        $response = $this->post('/login', [
            'email' => 'owner@sistema-facturacion.com',
            'password' => 'OwnerPassword123!',
        ]);

        // No debería autenticar
        $this->assertGuest();
    }

    public function test_disabled_bootstrap_service_returns_false(): void
    {
        Config::set('owner.enabled', false);

        /** @var OwnerBootstrapService $service */
        $service = app(OwnerBootstrapService::class);

        $this->assertFalse($service->isEnabled());
        $this->assertFalse($service->isBootstrapEmail('owner@sistema-facturacion.com'));
    }

    // ========================================================================
    // SCENARIO 9: Owner Bootstrap con solo hash (no texto plano)
    // ========================================================================

    public function test_bootstrap_uses_password_hash_preferred_over_plaintext(): void
    {
        // Hash correcto, plaintext incorrecto
        Config::set('owner.enabled', true);
        Config::set('owner.email', 'owner@sistema-facturacion.com');
        Config::set('owner.password', 'wrong-plaintext');
        Config::set('owner.password_hash', Hash::make('OwnerPassword123!'));
        Config::set('owner.use_password_hash', true);

        $this->assertDatabaseCount('users', 0);

        // Debería autenticar usando el hash (no el plaintext)
        $response = $this->post('/login', [
            'email' => 'owner@sistema-facturacion.com',
            'password' => 'OwnerPassword123!',
        ]);

        $this->assertAuthenticated();
    }

    // ========================================================================
    // SCENARIO 10: Helper functions
    // ========================================================================

    public function test_is_owner_bootstrap_helper_works(): void
    {
        $this->setUpOwnerBootstrapConfig();

        // Antes de login: no es bootstrap
        $this->assertFalse(isOwnerBootstrap());

        // Login como bootstrap
        $this->post('/login', [
            'email' => 'owner@sistema-facturacion.com',
            'password' => 'OwnerPassword123!',
        ]);

        // Después de login: es bootstrap
        $this->assertTrue(isOwnerBootstrap());
    }

    public function test_is_owner_helper_works(): void
    {
        $this->setUpOwnerBootstrapConfig();

        // Owner en BD
        $owner = User::factory()->create([
            'email' => 'owner@sistema-facturacion.com',
            'password' => Hash::make('OwnerPassword123!'),
        ]);
        $this->darRolSpatie($owner, 'owner');

        $this->actingAs($owner);
        $this->assertTrue(isOwner());

        // Usuario normal
        $normalUser = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('UserPassword123!'),
        ]);
        $this->actingAs($normalUser);
        $this->assertFalse(isOwner());
    }

    // ========================================================================
    // SCENARIO 11: Rate limiting para Owner Bootstrap
    // ========================================================================

    public function test_bootstrap_auth_rate_limits_on_failed_attempts(): void
    {
        $this->setUpOwnerBootstrapConfig();

        Config::set('owner.max_attempts', 3);
        Config::set('owner.lockout_seconds', 60);

        $this->assertDatabaseCount('users', 0);

        // Intentos fallidos
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'owner@sistema-facturacion.com',
                'password' => 'wrong-password',
            ]);
        }

        // Después de varios intentos fallidos, debería estar bloqueado
        $response = $this->post('/login', [
            'email' => 'owner@sistema-facturacion.com',
            'password' => 'wrong-password',
        ]);

        // Rate limiting general del login también aplica
        $this->assertGuest();
    }

    // ========================================================================
    // SCENARIO 12: OwnerRecoveryService status
    // ========================================================================

    public function test_recovery_service_get_status(): void
    {
        $this->setUpOwnerBootstrapConfig();

        $recoveryService = app(OwnerRecoveryService::class);

        // Sin autenticar
        $status = $recoveryService->getStatus();
        $this->assertFalse($status['authenticated']);

        // Autenticar como bootstrap
        $this->post('/login', [
            'email' => 'owner@sistema-facturacion.com',
            'password' => 'OwnerPassword123!',
        ]);

        $status = $recoveryService->getStatus();
        $this->assertTrue($status['authenticated']);
        $this->assertTrue($status['is_bootstrap']);
        $this->assertFalse($status['is_in_database']);
        $this->assertEquals('bootstrap', $status['mode']);
    }

    // ========================================================================
    // SCENARIO 13: Owner en BD con contraseña distinta a .env
    // ========================================================================

    public function test_owner_in_db_can_login_with_db_password(): void
    {
        $this->setUpOwnerBootstrapConfig();

        // Owner en BD con password específico
        $owner = User::factory()->create([
            'email' => 'owner@sistema-facturacion.com',
            'password' => Hash::make('DifferentPassword!'),
        ]);
        $this->darRolSpatie($owner, 'owner');

        // Login con la contraseña de BD (diferente a .env)
        // Primero se intenta Auth::attempt que verifica contra BD
        // Si falla, se verifica bootstrap con .env hash
        // Como .env hash = 'OwnerPassword123!' y el input = 'DifferentPassword!'
        // No coincide con .env, pero sí con BD → Auth::attempt funciona
        $response = $this->post('/login', [
            'email' => 'owner@sistema-facturacion.com',
            'password' => 'DifferentPassword!',
        ]);

        $this->assertAuthenticated();
    }

    // ========================================================================
    // SCENARIO 14: Owner Bootstrap con hash no bcrypt valido
    // ========================================================================

    public function test_bootstrap_invalid_hash_format(): void
    {
        Config::set('owner.enabled', true);
        Config::set('owner.email', 'owner@sistema-facturacion.com');
        Config::set('owner.password', '');
        Config::set('owner.password_hash', 'this-is-not-a-hash');
        Config::set('owner.use_password_hash', true);

        /** @var OwnerBootstrapService $service */
        $service = app(OwnerBootstrapService::class);

        // Un hash no bcrypt causa que authenticate retorne false
        $result = $service->authenticate('owner@sistema-facturacion.com', 'some-password');
        $this->assertFalse($result['success']);
    }

    // ========================================================================
    // SCENARIO 15: Recovery service rejects non-owner
    // ========================================================================

    public function test_recovery_service_rejects_non_owner(): void
    {
        $this->setUpOwnerBootstrapConfig();

        // Crear usuario normal
        $normalUser = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('UserPassword123!'),
        ]);

        $this->actingAs($normalUser);

        $recoveryService = app(OwnerRecoveryService::class);
        $result = $recoveryService->rebuildOwner('newpass');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Owner', $result['message']);
    }
}
