<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\DemoUsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolesManagerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $gerente;
    protected $vendedor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
        $this->seed(DemoUsersSeeder::class);

        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['name' => 'Administrador', 'password' => bcrypt('123456'), 'role' => 'admin']
        )->syncRoles(['admin']);

        User::firstOrCreate(
            ['email' => 'vendedor@test.com'],
            ['name' => 'Vendedor', 'password' => bcrypt('123456'), 'role' => 'vendedor']
        )->syncRoles(['vendedor']);

        $this->admin    = User::where('email', 'admin@test.com')->first();
        $this->gerente  = User::where('email', 'gerente@test.com')->first();
        $this->vendedor = User::where('email', 'vendedor@test.com')->first();
    }

    public function test_index_admin_ok()
    {
        $r = $this->actingAs($this->admin)->get('/roles');
        $r->assertStatus(200);
        $r->assertSee('Manejador de Roles');
        $r->assertSee('Admin');
        $r->assertSee('Gerente');
    }

    public function test_index_gerente_403()
    {
        $r = $this->actingAs($this->gerente)->get('/roles');
        $r->assertStatus(403);
    }

    public function test_index_vendedor_403()
    {
        $r = $this->actingAs($this->vendedor)->get('/roles');
        $r->assertStatus(403);
    }

    public function test_create_admin_ok()
    {
        $r = $this->actingAs($this->admin)->get('/roles/create');
        $r->assertStatus(200);
        $r->assertSee('Crear Rol');
        $r->assertSee('Asignar Permisos');
    }

    public function test_store_creates_role()
    {
        $perms = ['dashboard.view', 'productos.view'];
        $r = $this->actingAs($this->admin)->post('/roles', [
            'name' => 'supervisor',
            'permissions' => $perms,
        ]);
        $r->assertRedirect('/roles');
        $r->assertSessionHas('success');
        $rol = Role::where('name', 'supervisor')->first();
        $this->assertNotNull($rol);
        $this->assertEquals(2, $rol->permissions->count());
    }

    public function test_store_validation_fails_duplicate()
    {
        $r = $this->actingAs($this->admin)->post('/roles', [
            'name' => 'admin',
            'permissions' => [],
        ]);
        $r->assertSessionHasErrors(['name']);
    }

    public function test_show_admin_ok()
    {
        $rol = Role::where('name', 'admin')->first();
        $r = $this->actingAs($this->admin)->get("/roles/{$rol->id}");
        $r->assertStatus(200);
        $r->assertSee('Permisos Asignados');
        $r->assertSee('Usuarios Asignados');
    }

    public function test_edit_admin_ok()
    {
        $rol = Role::where('name', 'gerente')->first();
        $r = $this->actingAs($this->admin)->get("/roles/{$rol->id}/edit");
        $r->assertStatus(200);
        $r->assertSee('gerente');
    }

    public function test_update_changes_permissions()
    {
        $rol = Role::where('name', 'gerente')->first();
        $originalCount = $rol->permissions->count();
        $r = $this->actingAs($this->admin)->put("/roles/{$rol->id}", [
            'name' => 'gerente',
            'permissions' => ['dashboard.view', 'productos.view'],
        ]);
        $r->assertRedirect('/roles');
        $rol->refresh();
        $this->assertEquals(2, $rol->permissions->count());
    }

    public function test_update_cannot_change_admin_perms()
    {
        $rol = Role::where('name', 'admin')->first();
        $r = $this->actingAs($this->admin)->put("/roles/{$rol->id}", [
            'name' => 'admin',
            'permissions' => ['dashboard.view'],
        ]);
        $rol->refresh();
        $this->assertGreaterThan(1, $rol->permissions->count());
    }

    public function test_destroy_empty_role()
    {
        $rol = Role::create(['name' => 'temp_role', 'guard_name' => 'web']);
        $r = $this->actingAs($this->admin)->delete("/roles/{$rol->id}");
        $r->assertRedirect('/roles');
        $this->assertDatabaseMissing('roles', ['id' => $rol->id]);
    }

    public function test_destroy_cannot_delete_admin()
    {
        $rol = Role::where('name', 'admin')->first();
        $r = $this->actingAs($this->admin)->delete("/roles/{$rol->id}");
        $r->assertSessionHas('error');
        $this->assertDatabaseHas('roles', ['id' => $rol->id]);
    }

    public function test_destroy_cannot_delete_with_users()
    {
        $rol = Role::where('name', 'vendedor')->first();
        $r = $this->actingAs($this->admin)->delete("/roles/{$rol->id}");
        $r->assertSessionHas('error');
    }

    public function test_matrix_view_ok()
    {
        $r = $this->actingAs($this->admin)->get('/roles-matrix');
        $r->assertStatus(200);
        $r->assertSee('Matriz de Permisos');
    }
}
