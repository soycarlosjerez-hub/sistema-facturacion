<?php

namespace Tests\Feature;

use App\Models\BusinessInstance;
use App\Models\BusinessType;
use App\Models\InstanceApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class InstanceApiKeyControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private BusinessInstance $instance;

    protected function setUp(): void
    {
        parent::setUp();

        $bizType = BusinessType::create([
            'nombre' => 'Test Type',
            'slug' => 'test-type',
            'activo' => true,
        ]);

        $this->owner = User::factory()->create();
        $this->instance = BusinessInstance::create([
            'nombre' => 'Test Instance',
            'slug' => 'test-instance-'.Str::random(6),
            'business_type_id' => $bizType->id,
            'owner_user_id' => $this->owner->id,
            'activo' => true,
        ]);
        $this->owner->update(['business_instance_id' => $this->instance->id]);
        // La autorización solo evalúa roles Spatie (como los seeders).
        $this->darRolSpatie($this->owner, 'owner');
        // La policy exige permisos granulares owner.instances.*.
        foreach (['owner.instances.view', 'owner.instances.edit'] as $perm) {
            \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'web',
            ]);
        }
        $this->owner->givePermissionTo(['owner.instances.view', 'owner.instances.edit']);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->owner = $this->owner->fresh();
    }

    /** @test */
    public function owner_can_view_api_keys_index(): void
    {
        InstanceApiKey::factory()->count(3)->create(['business_instance_id' => $this->instance->id]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.instances.api-keys', $this->instance));

        $response->assertOk();
        $response->assertViewIs('owner.instances.api-keys');
        $response->assertViewHas('instance', $this->instance);
    }

    /** @test */
    public function can_create_api_key(): void
    {
        $payload = ['name' => 'web-shop-integration'];

        $response = $this->actingAs($this->owner)
            ->post(route('owner.instances.api-keys.generate', $this->instance), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'API Key creada correctamente.');
        $response->assertSessionHas('new_api_key');

        $newKey = InstanceApiKey::where('business_instance_id', $this->instance->id)
            ->where('name', 'web-shop-integration')
            ->first();

        $this->assertNotNull($newKey);
        $this->assertTrue($newKey->is_active);
        $this->assertEquals($this->owner->id, $newKey->created_by);
        $this->assertStringStartsWith('iak_', session('new_api_key'));

        $this->assertAuditLog('API_KEY_CREATE', $newKey->id);
    }

    /** @test */
    public function name_must_be_unique_per_instance(): void
    {
        InstanceApiKey::create([
            'business_instance_id' => $this->instance->id,
            'name' => 'duplicate-name',
            'key' => hash('sha256', 'iak_test'),
            'created_by' => $this->owner->id,
        ]);

        $response = $this->actingAs($this->owner)
            ->post(route('owner.instances.api-keys.generate', $this->instance), [
                'name' => 'duplicate-name',
            ]);

        $response->assertSessionHasErrors('name');
        $this->assertEquals(
            'Ya existe una API Key con ese nombre para esta instancia.',
            $response->session()->get('errors')->first('name')
        );
    }

    /** @test */
    public function name_must_match_regex(): void
    {
        $response = $this->actingAs($this->owner)
            ->post(route('owner.instances.api-keys.generate', $this->instance), [
                'name' => 'bad<script>alert()</script>',
            ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function owner_can_regenerate_api_key(): void
    {
        $apiKey = InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
            'created_by' => $this->owner->id,
        ]);

        $oldHash = $apiKey->key;

        $response = $this->actingAs($this->owner)
            ->post(route('owner.instances.api-keys.regenerate', [$this->instance, $apiKey]));

        $response->assertRedirect();
        $response->assertSessionHas('new_api_key');

        $apiKey->refresh();
        $this->assertNotEquals($oldHash, $apiKey->key);
        $this->assertStringStartsWith('iak_', session('new_api_key'));

        $this->assertAuditLog('API_KEY_REGENERATE', $apiKey->id);
    }

    /** @test */
    public function owner_can_toggle_api_key(): void
    {
        $apiKey = InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
            'created_by' => $this->owner->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->owner)
            ->post(route('owner.instances.api-keys.toggle', [$this->instance, $apiKey]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'API Key "'.$apiKey->name.'" desactivada correctamente.');

        $apiKey->refresh();
        $this->assertFalse($apiKey->is_active);

        $this->assertAuditLog('API_KEY_TOGGLED', $apiKey->id);

        $response = $this->actingAs($this->owner)
            ->post(route('owner.instances.api-keys.toggle', [$this->instance, $apiKey]));

        $response->assertSessionHas('success', 'API Key "'.$apiKey->name.'" activada correctamente.');
        $apiKey->refresh();
        $this->assertTrue($apiKey->is_active);
    }

    /** @test */
    public function owner_can_soft_delete_api_key(): void
    {
        $apiKey = InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
            'created_by' => $this->owner->id,
        ]);

        $response = $this->actingAs($this->owner)
            ->delete(route('owner.instances.api-keys.destroy', [$this->instance, $apiKey]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'API Key "'.$apiKey->name.'" eliminada correctamente.');

        $this->assertNotNull($apiKey->fresh()->deleted_at);
        $this->assertAuditLog('API_KEY_DELETE', $apiKey->id);
    }

    /** @test */
    public function index_shows_trashed_when_requested(): void
    {
        $apiKey = InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
            'created_by' => $this->owner->id,
        ]);
        $apiKey->delete();

        $response = $this->actingAs($this->owner)
            ->get(route('owner.instances.api-keys', ['instance' => $this->instance, 'trashed' => 'only']));

        $response->assertOk();
        $response->assertViewIs('owner.instances.api-keys');
    }

    /** @test */
    public function index_searches_by_name_and_creator(): void
    {
        $creator = User::factory()->create(['name' => 'John Doe']);

        $activeKey = InstanceApiKey::create([
            'business_instance_id' => $this->instance->id,
            'name' => 'My Secret Key',
            'key' => hash('sha256', 'iak_test'),
            'is_active' => true,
            'created_by' => $creator->id,
        ]);

        $inactiveKey = InstanceApiKey::create([
            'business_instance_id' => $this->instance->id,
            'name' => 'Old Key',
            'key' => hash('sha256', 'iak_old'),
            'is_active' => false,
            'created_by' => $this->owner->id,
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.instances.api-keys', ['instance' => $this->instance, 'search' => 'Secret']));

        $viewKeys = $response->viewData('apiKeys');
        $this->assertCount(1, $viewKeys);
        $this->assertEquals($activeKey->id, $viewKeys->first()->id);
    }

    /** @test */
    public function index_filters_by_status(): void
    {
        InstanceApiKey::create([
            'business_instance_id' => $this->instance->id,
            'name' => 'Active Key',
            'key' => hash('sha256', 'iak_active'),
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        InstanceApiKey::create([
            'business_instance_id' => $this->instance->id,
            'name' => 'Inactive Key',
            'key' => hash('sha256', 'iak_inactive'),
            'is_active' => false,
            'created_by' => $this->owner->id,
        ]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.instances.api-keys', ['instance' => $this->instance, 'status' => 'inactive']));

        $viewKeys = $response->viewData('apiKeys');
        $this->assertCount(1, $viewKeys);
        $this->assertFalse($viewKeys->first()->is_active);
    }

    /** @test */
    public function api_key_is_soft_deleted_not_hard_deleted(): void
    {
        $apiKey = InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
            'created_by' => $this->owner->id,
        ]);

        $this->actingAs($this->owner)
            ->delete(route('owner.instances.api-keys.destroy', [$this->instance, $apiKey]));

        // Should still be recoverable (not hard deleted)
        $this->assertNotNull(InstanceApiKey::withTrashed()->find($apiKey->id));
        $this->assertNull(InstanceApiKey::find($apiKey->id));
    }

    /** @test */
    public function owner_can_force_delete_api_key(): void
    {
        $apiKey = InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
            'created_by' => $this->owner->id,
        ]);
        $apiKey->delete();

        $response = $this->actingAs($this->owner)
            ->delete(route('owner.instances.api-keys.force', [$this->instance, $apiKey]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'API Key "'.$apiKey->name.'" eliminada permanentemente.');

        // Should be permanently gone (not even in trashed)
        $this->assertNull(InstanceApiKey::withTrashed()->find($apiKey->id));
        $this->assertAuditLog('API_KEY_FORCE_DELETE', $apiKey->id);
    }

    private function assertAuditLog(string $action, ?int $modelId = null): void
    {
        $this->assertDatabaseHas('audit_logs', [
            'action' => $action,
            'model_type' => InstanceApiKey::class,
            'model_id' => $modelId,
        ]);
    }
}
