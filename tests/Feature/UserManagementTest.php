<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
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
            'email' => 'test1@example.com'
        ]);

        $response2 = $this->actingAs($user1)->patch('/account/'.$user2->id, [
            'name' => 'Test User2',
            'email' => 'test1@example.com'
        ]);

        $this->assertEquals('Test User1', User::find(2)->name);
        $response1->assertStatus(200);
        $response2->assertStatus(403);
    }

    /**
     * Test validation of profile updating request.
     *
     * @return void
     */
    public function test_validation_of_profile_updating_request()
    {
        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $response = $this->actingAs($user)->patch('/account/'.$user->id, [
            'name' => '',
            'email' => 'test1example'
        ]);

        $this->assertNotEquals('test1example', User::first()->email);
        $response->assertSessionHasErrors('name');
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test the given email is unique for updating.
     *
     * @return void
     */
    public function test_given_email_is_unique_for_updating()
    {
        $user1 = User::factory()->create([
            'email' => 'test@example.com',
            'access_level' => 2
        ]);

        $user2 = User::factory()->create([
            'email' => 'test2@example.com',
            'access_level' => 2
        ]);

        $response = $this->actingAs($user2)->patch('/account/'.$user2->id, [
            'name' => 'User2',
            'email' => 'test@example.com'
        ]);

        $this->assertNotEquals('test@example.com', User::find(2)->email);
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test a user can update it's own password.
     *
     * @return void
     */
    public function test_a_user_can_update_its_own_password()
    {
        $user1 = User::factory()->create([
            'access_level' => 1
        ]);

        $user2 = User::factory()->create([
            'password' => Hash::make('oldpassword'),
            'access_level' => 2
        ]);

        $response1 = $this->actingAs($user2)->post('/update-password/'.$user2->id, [
            'old_password' => 'oldpassword',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response2 = $this->actingAs($user1)->post('/update-password/'.$user2->id, [
            'old_password' => 'oldpassword',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $this->assertTrue(Hash::check('password', User::find(2)->password));
        $response1->assertStatus(200);
        $response2->assertStatus(403);
    }

    /**
     * Test a user can't update it's own password with wrong old password.
     *
     * @return void
     */
    public function test_a_user_cant_update_password_with_wrong_old()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
            'access_level' => 2
        ]);

        $response = $this->actingAs($user)->post('/update-password/'.$user->id, [
            'old_password' => 'wrongpassword',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $this->assertTrue(Hash::check('oldpassword', User::first()->password));
        $response->assertSessionHasErrors('old_password');
    }

    /**
     * Password validation test.
     *
     * @return void
     */
    public function test_the_given_password_is_valid()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
            'access_level' => 2
        ]);

        $response = $this->actingAs($user)->post('/update-password/'.$user->id, [
            'old_password' => 'oldpassword',
            'password' => 'password',
            'password_confirmation' => ''
        ]);

        $this->assertTrue(Hash::check('oldpassword', User::first()->password));
        $response->assertSessionHasErrors('password');
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

    /**
     * Test validation of user's access request.
     *
     * @return void
     */
    public function test_validation_of_update_access_request()
    {
       $user1 = User::factory()->create([
            'access_level' => 1
        ]);

        $user2 = User::factory()->create([
            'access_level' => 2
        ]);

        $response = $this->actingAs($user1)->patch('/account-access/'.$user2->id, [
            'access_level' => ''
        ]);

        $this->assertEquals(2, User::find(2)->access_level);
        $response->assertSessionHasErrors('access_level');
    }


    /**
     * Test an admin can delete another user.
     *
     * @return void
     */
    public function test_a_user_can_be_deleted()
    {
        $user1 = User::factory()->create([
            'access_level' => 1
        ]);

        $user2 = User::factory()->create([
            'access_level' => 3
        ]);

        $response1 = $this->actingAs($user2)->delete('/account/'.$user2->id);

        $this->assertCount(2, User::all());
        $response1->assertStatus(403);

        $response2 = $this->actingAs($user1)->delete('/account/'.$user2->id);

        $this->assertCount(1, User::all());
        $this->assertEquals(1, User::first()->access_level);
        $response2->assertStatus(200);
    }

    /**
     * Test an admin can't delete his/her own account.
     *
     * @return void
     */
    public function test_an_admin_cant_be_deleted_itself()
    {
        $user1 = User::factory()->create([
            'access_level' => 1
        ]);

        $user2 = User::factory()->create([
            'access_level' => 1
        ]);

        $response1 = $this->actingAs($user2)->delete('/account/'.$user2->id);

        $this->assertCount(2, User::all());
        $response1->assertStatus(403);

        $response2 = $this->actingAs($user1)->delete('/account/'.$user2->id);

        $this->assertCount(1, User::all());
        $response2->assertStatus(200);
    }
}
