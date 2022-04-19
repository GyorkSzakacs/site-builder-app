<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test a user can update it's own profile.
     *
     * @return void
     */
    public function test_a_user_can_update_its_own_profile()
    {
        $user1 = User::factory()->create([
            'access_level' => 1
        ]);

        $user2 = User::factory()->create([
            'access_level' => 2
        ]);

        $response1 = $this->actingAs($user2)->patch('/account/'.$user2->id, [
            'name' => 'Test User1',
            'email' => 'test1@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response2 = $this->actingAs($user1)->patch('/account/'.$user2->id, [
            'name' => 'Test User2',
            'email' => 'test1@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $this->assertEquals('Test User1', User::find(2)->name);
        $response1->assertStatus(200);
        $response2->assertStatus(403);
    }

    /**
     * Test an admin can update a user's access.
     *
     * @return void
     */
    public function test_an_admin_can_update_access()
    {
       $user1 = User::factory()->create([
            'access_level' => 1
        ]);

        $user2 = User::factory()->create([
            'access_level' => 2
        ]);

        $response1 = $this->actingAs($user1)->patch('/account-access/'.$user2->id, [
            'access_level' => 3
        ]);

        $response2 = $this->actingAs($user2)->patch('/account-access/'.$user2->id, [
            'access_level' => 1
        ]);

        $this->assertEquals(3, User::find(2)->access_level);
        $response1->assertStatus(200);
        $response2->assertStatus(403);
    }

    /**
     * Test an admin can't update own access.
     *
     * @return void
     */
    public function test_an_admin_cant_update_own_access()
    {
       $user = User::factory()->create([
            'access_level' => 1
        ]);

        $response = $this->actingAs($user)->patch('/account-access/'.$user->id, [
            'access_level' => 2
        ]);

        $this->assertEquals(1, User::first()->access_level);
        $response->assertStatus(403);
    }
}
