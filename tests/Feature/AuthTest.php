<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_page(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_login_and_access_dashboard(): void
    {
        $user = User::factory()->create([
            'name' => 'Pemilik Warung',
            'username' => 'owner',
            'password' => 'password123',
        ]);

        $this->post(route('login'), [
            'username' => 'owner',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }
}
