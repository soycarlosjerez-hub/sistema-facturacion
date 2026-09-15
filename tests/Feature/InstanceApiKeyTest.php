<?php

namespace Tests\Feature;

use App\Models\BusinessInstance;
use App\Models\BusinessType;
use App\Models\InstanceApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class InstanceApiKeyTest extends TestCase
{
    use RefreshDatabase;

    private BusinessInstance $instance;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $bizType = BusinessType::create([
            'nombre' => 'Biz Type',
            'slug' => 'biz-type',
            'activo' => true,
        ]);

        $this->owner = User::factory()->create();
        $this->instance = BusinessInstance::create([
            'nombre' => 'Instance',
            'slug' => 'instance-'.Str::random(6),
            'business_type_id' => $bizType->id,
            'owner_user_id' => $this->owner->id,
            'activo' => true,
        ]);
        $this->owner->update(['business_instance_id' => $this->instance->id]);
    }

    /** @test */
    public function mask_hides_most_of_the_key(): void
    {
        $apiKey = InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
            'key' => 'a'.str_repeat('b', 56).'z',
        ]);

        $masked = $apiKey->mask();

        $this->assertNotEquals($apiKey->key, $masked);
        $this->assertStringContainsString('***', $masked);
        // Sin key_raw: primeros 8 + **** + últimos 4.
        $this->assertSame(
            substr($apiKey->key, 0, 8).'****'.substr($apiKey->key, -4),
            $masked
        );
    }

    /** @test */
    public function active_scope_returns_only_active_keys(): void
    {
        InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
            'is_active' => true,
        ]);

        InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
            'is_active' => false,
        ]);

        $activeKeys = InstanceApiKey::active()->get();

        $this->assertCount(1, $activeKeys);
        $this->assertTrue($activeKeys->first()->is_active);
    }

    /** @test */
    public function search_scope_finds_by_name(): void
    {
        $key = InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
            'name' => 'My Custom Key',
        ]);

        $results = InstanceApiKey::search('Custom')->get();

        $this->assertCount(1, $results);
        $this->assertEquals($key->id, $results->first()->id);
    }

    /** @test */
    public function search_scope_finds_by_creator_name(): void
    {
        $creator = User::factory()->create(['name' => 'John Doe']);

        InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
            'name' => 'Some Key',
            'created_by' => $creator->id,
        ]);

        $results = InstanceApiKey::search('John')->get();

        $this->assertCount(1, $results);
    }

    /** @test */
    public function search_scope_returns_all_when_empty(): void
    {
        InstanceApiKey::factory()->count(3)->create([
            'business_instance_id' => $this->instance->id,
        ]);

        $results = InstanceApiKey::search(null)->get();

        $this->assertCount(3, $results);
    }

    /** @test */
    public function by_status_returns_active(): void
    {
        InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
            'is_active' => true,
        ]);

        InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
            'is_active' => false,
        ]);

        $results = InstanceApiKey::byStatus('active')->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is_active);
    }

    /** @test */
    public function by_status_returns_inactive(): void
    {
        InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
            'is_active' => false,
        ]);

        $results = InstanceApiKey::byStatus('inactive')->get();

        $this->assertCount(1, $results);
        $this->assertFalse($results->first()->is_active);
    }

    /** @test */
    public function by_status_returns_all_when_empty(): void
    {
        InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
        ]);

        $results = InstanceApiKey::byStatus(null)->get();

        $this->assertGreaterThanOrEqual(1, $results->count());
    }

    /** @test */
    public function soft_delete_works(): void
    {
        $apiKey = InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
        ]);

        $apiKey->delete();

        $this->assertNull(InstanceApiKey::find($apiKey->id));
        $this->assertNotNull(InstanceApiKey::withTrashed()->find($apiKey->id));
        $this->assertNotNull(InstanceApiKey::onlyTrashed()->find($apiKey->id));
    }

    /** @test */
    public function factory_creates_valid_instance(): void
    {
        $apiKey = InstanceApiKey::factory()->create([
            'business_instance_id' => $this->instance->id,
        ]);

        $this->assertNotNull($apiKey->id);
        $this->assertNotNull($apiKey->name);
        $this->assertNotNull($apiKey->key);
        $this->assertNotNull($apiKey->business_instance_id);
    }
}
