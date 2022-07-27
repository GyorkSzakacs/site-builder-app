<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen()
    {
        $user3 = User::factory()->create([
            'access_level' => 3
        ]);

        $response3 = $this->post('/login', [
            'email' => $user3->email,
            'password' => 'password',
        ]);
        
        $user1 = User::factory()->create([
            'access_level' => 1
        ]);

        $response1 = $this->post('/login', [
            'email' => $user1->email,
            'password' => 'password',
        ]);
        
        $user2 = User::factory()->create([
            'access_level' => 2
        ]);


        $response2 = $this->post('/login', [
            'email' => $user2->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response1->assertRedirect(RouteServiceProvider::HOME);
        $response2->assertRedirect(RouteServiceProvider::HOME);
        $response3->assertRedirect('/');
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create([
            'access_level' => 1
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }
}
