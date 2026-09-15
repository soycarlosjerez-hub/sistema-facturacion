<?php

namespace Tests\Feature;

use App\Models\BusinessInstance;
use App\Models\BusinessType;
use App\Models\InstanceApiKey;
use App\Models\User;
use App\Policies\InstanceApiKeyPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class InstanceApiKeyPolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $otherOwner;

    private BusinessInstance $instance;

    private BusinessInstance $otherInstance;

    protected function setUp(): void
    {
        parent::setUp();

        $bizType = BusinessType::create([
            'nombre' => 'Biz Type',
            'slug' => 'biz-type',
            'activo' => true,
        ]);

        $this->owner = User::factory()->create();
        $this->otherOwner = User::factory()->create();

        $this->instance = BusinessInstance::create([
            'nombre' => 'My Instance',
            'slug' => 'my-instance-'.Str::random(6),
            'business_type_id' => $bizType->id,
            'owner_user_id' => $this->owner->id,
            'activo' => true,
        ]);

        $this->otherInstance = BusinessInstance::create([
            'nombre' => 'Other Instance',
            'slug' => 'other-instance-'.Str::random(6),
            'business_type_id' => $bizType->id,
            'owner_user_id' => $this->otherOwner->id,
            'activo' => true,
        ]);

        // Roles/permisos Spatie como en producción (la policy los exige).
        foreach (['owner.instances.view', 'owner.instances.edit'] as $perm) {
            \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'web',
            ]);
        }
        $this->darRolSpatie($this->owner, 'owner');
        $this->darRolSpatie($this->otherOwner, 'owner');
        $this->owner->givePermissionTo(['owner.instances.view', 'owner.instances.edit']);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /** @test */
    public function owner_can_view_any_api_keys_for_own_instance(): void
    {
        $policy = app(InstanceApiKeyPolicy::class);

        $this->assertTrue($policy->viewAny($this->owner, $this->instance));
        $this->assertFalse($policy->viewAny($this->otherOwner, $this->instance));
    }

    /** @test */
    public function owner_can_create_api_keys_for_own_instance(): void
    {
        $policy = app(InstanceApiKeyPolicy::class);

        $this->assertTrue($policy->create($this->owner, $this->instance));
        $this->assertFalse($policy->create($this->otherOwner, $this->instance));
    }

    /** @test */
    public function owner_can_update_api_keys_for_own_instance(): void
    {
        $apiKey = InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
            'created_by' => $this->owner->id,
        ]);

        $policy = app(InstanceApiKeyPolicy::class);

        $this->assertTrue($policy->update($this->owner, $apiKey));
        $this->assertFalse($policy->update($this->otherOwner, $apiKey));
    }

    /** @test */
    public function owner_can_delete_api_keys_for_own_instance(): void
    {
        $apiKey = InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
            'created_by' => $this->owner->id,
        ]);

        $policy = app(InstanceApiKeyPolicy::class);

        $this->assertTrue($policy->delete($this->owner, $apiKey));
        $this->assertFalse($policy->delete($this->otherOwner, $apiKey));
    }

    /** @test */
    public function policy_denies_access_on_null_instance(): void
    {
        $policy = app(InstanceApiKeyPolicy::class);

        $policy->update($this->owner, InstanceApiKey::factory()->make());
    }

    /** @test */
    public function owner_can_force_delete_api_keys_for_own_instance(): void
    {
        $apiKey = InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
            'created_by' => $this->owner->id,
        ]);
        $apiKey->delete();

        $policy = app(InstanceApiKeyPolicy::class);

        $this->assertTrue($policy->forceDelete($this->owner, $apiKey));
        $this->assertFalse($policy->forceDelete($this->otherOwner, $apiKey));
    }
}
