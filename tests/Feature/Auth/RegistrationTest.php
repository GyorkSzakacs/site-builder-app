<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'access_level' => 1
        ]);

        $this->assertCount(1, User::all());
        $this->assertEquals(1, User::first()->access_level);
        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_new_users_can_register_with_valid_access_level()
    {
        User::factory()->create([
            'access_level' => 1
        ]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'access_level' => ''
        ]);

        $this->assertCount(1, User::all());
        $response->assertSessionHasErrors('access_level');
        $this->assertGuest();
    }

    /**
     * Test new user only register as admin if there aren't any users, esle can't register yourself.
     * 
     * @return void
     */
    public function test_first_users_can_register_as_admin()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $this->assertCount(1, User::all());
        $this->assertEquals(1, User::first()->access_level);
        $this->assertAuthenticated();
    }

}
