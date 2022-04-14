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
        
        $user = User::factory()->create([
            'access_level' => 1
        ]);

        $response = $this->actingAs($user)->get('/register');

        $response->assertStatus(200);
    }

    public function test_first_registration_screen_can_be_rendered()
    {
        $response = $this->get('/first-register');

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
        $user = User::factory()->create([
            'access_level' => 1
        ]);

        $response = $this->actingAs($user)->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'access_level' => ''
        ]);

        $this->assertCount(1, User::all());
        $response->assertSessionHasErrors('access_level');
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

    /**
     * Test only an admin can register a new user.
     * 
     * @return void
     */
    public function test_only_an_admin_can_register_a_new_user()
    {
        $admin = User::factory()->create([
            'access_level' => 1
        ]);

        $manager = User::factory()->create([
            'access_level' => 2
        ]);

        $editor = User::factory()->create([
            'access_level' => 3
        ]);
        
        $response1 = $this->actingAs($admin)->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'access_level' => 3
        ]);

        $response2 = $this->actingAs($manager)->post('/register', [
            'name' => 'Test User2',
            'email' => 'test2@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'access_level' => 3
        ]);

        $response3 = $this->actingAs($editor)->post('/register', [
            'name' => 'Test User3',
            'email' => 'test3@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'access_level' => 3
        ]);

        $this->assertCount(4, User::all());
        $this->assertEquals('Test User', User::find(4)->name);
        $response1->assertRedirect(RouteServiceProvider::HOME);
        $response2->assertStatus(403);
        $response3->assertStatus(403);
    }
}
