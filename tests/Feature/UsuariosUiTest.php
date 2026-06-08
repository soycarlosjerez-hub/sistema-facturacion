<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\DemoUsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuariosUiTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $gerente;
    protected $vendedor;
    protected $almacen;
    protected $contador;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
        $this->seed(\Database\Seeders\RolesYUsuariosSeeder::class);
        $this->seed(DemoUsersSeeder::class);

        // Asegurar todos los roles con usuarios
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
        $this->almacen  = User::where('email', 'almacen@test.com')->first();
        $this->contador = User::where('email', 'contador@test.com')->first();
    }

    public function test_index_admin_ok()
    {
        $r = $this->actingAs($this->admin)->get('/usuarios');
        $r->assertStatus(200);
        $r->assertSee('Gestión de Usuarios');
        $r->assertSee('Admins');
    }

    public function test_index_gerente_403()
    {
        $r = $this->actingAs($this->gerente)->get('/usuarios');
        $r->assertStatus(403);
    }

    public function test_index_vendedor_403()
    {
        $r = $this->actingAs($this->vendedor)->get('/usuarios');
        $r->assertStatus(403);
    }

    public function test_create_admin_ok()
    {
        $r = $this->actingAs($this->admin)->get('/usuarios/create');
        $r->assertStatus(200);
        $r->assertSee('Crear Usuario');
        $r->assertSee('Asignar Rol');
        $r->assertSee('Vista previa de permisos');
    }

    public function test_show_admin_ok()
    {
        $r = $this->actingAs($this->admin)->get('/usuarios/1');
        $r->assertStatus(200);
        $r->assertSee('Permisos del Usuario');
    }

    public function test_edit_admin_ok()
    {
        $r = $this->actingAs($this->admin)->get('/usuarios/1/edit');
        $r->assertStatus(200);
        $r->assertSee('Rol Asignado');
    }

    public function test_filter_by_role()
    {
        $r = $this->actingAs($this->admin)->get('/usuarios?rol=admin');
        $r->assertStatus(200);
        $r->assertSee('admin@test.com');
    }

    public function test_search()
    {
        $r = $this->actingAs($this->admin)->get('/usuarios?buscar=admin');
        $r->assertStatus(200);
    }

    public function test_store_new_user()
    {
        $r = $this->actingAs($this->admin)->post('/usuarios', [
            'name' => 'Test User UI',
            'email' => 'test_ui@demo.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => 'vendedor',
        ]);
        $r->assertRedirect('/usuarios');
        $r->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['email' => 'test_ui@demo.com']);
    }

    public function test_store_validation_fails()
    {
        $r = $this->actingAs($this->admin)->post('/usuarios', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => '123',
            'password_confirmation' => '456',
            'role' => 'invalid',
        ]);
        $r->assertSessionHasErrors(['name', 'email', 'password', 'role']);
    }

    public function test_update_changes_role()
    {
        $user = User::where('email', 'vendedor@test.com')->first();
        $r = $this->actingAs($this->admin)->put("/usuarios/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => '',
            'role' => 'almacen',
        ]);
        $r->assertRedirect('/usuarios');
        $user->refresh();
        $this->assertTrue($user->hasRole('almacen'));
    }

    public function test_destroy_block_self()
    {
        $r = $this->actingAs($this->admin)->delete("/usuarios/{$this->admin->id}");
        $r->assertSessionHas('error');
    }
}
