<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwoFactorAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_factor_index_returns_success(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get(route('two-factor.index'));

        $response->assertOk();
        $response->assertViewIs('auth.two-factor');
    }

    public function test_two_factor_enable_generates_qr(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post(route('two-factor.enable'), [], 'JSON');

        $response->assertJsonStructure([
            'success', 
            'qr_code_url', 
            'recovery_codes'
        ]);
        
        $response->assertJsonPath('success', true);
        $this->assertNotNull($user->fresh()->two_factor_secret);
    }

    public function test_two_factor_disable_requires_password(): void
    {
        $user = User::factory()->create();
        
        // First enable 2FA
        $this->actingAs($user)->post(route('two-factor.enable'), [], 'JSON');
        
        $response = $this->actingAs($user)
            ->post(route('two-factor.disable'), [
                'password' => 'wrong-password'
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('password');
    }

    public function test_two_factor_recovery_codes_generated(): void
    {
        $user = User::factory()->create();
        
        $this->actingAs($user)->post(route('two-factor.enable'), [], 'JSON');
        
        $response = $this->actingAs($user)
            ->post(route('two-factor.recovery'), [
                'password' => $user->password
            ], 'JSON');

        $response->assertJsonStructure([
            'success', 
            'recovery_codes'
        ]);
        
        $response->assertJsonPath('success', true);
        
        $codes = json_decode($user->fresh()->two_factor_recovery_codes, true);
        $this->assertCount(8, $codes);
        $this->assertEquals(8, strlen($codes[0]));
    }
}
