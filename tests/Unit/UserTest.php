<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting the selected user access level.
     *
     * @return void
     */
    public function test_get_user_access()
    {
        $user = User::factory()->create([
            'access_level' => 1
        ]);

        $this->assertEquals(1, $user->getAccess());
    }

    /**
     * Test current user has admin access.
     *
     * @return void
     */
    public function test_current_user_is_admin()
    {
        $user1 = User::factory()->create([
            'access_level' => 1
        ]);

        $user2 = User::factory()->create([
            'access_level' => 2
        ]);

        $this->assertTrue($user1->hasAdminAccess());
        $this->assertFalse($user2->hasAdminAccess());
    }

    /**
     * Test current user has manager access.
     *
     * @return void
     */
    public function test_current_user_is_manager()
    {
        $user1 = User::factory()->create([
            'access_level' => 1
        ]);

        $user2 = User::factory()->create([
            'access_level' => 2
        ]);

        $user3 = User::factory()->create([
            'access_level' => 3
        ]);

        $this->assertTrue($user1->hasManagerAccess());
        $this->assertTrue($user2->hasManagerAccess());
        $this->assertFalse($user3->hasManagerAccess());
    }

    /**
     * Test current user has editor access.
     *
     * @return void
     */
    public function test_current_user_is_editor()
    {
        $user1 = User::factory()->create([
            'access_level' => 1
        ]);

        $user2 = User::factory()->create([
            'access_level' => 2
        ]);

        $user3 = User::factory()->create([
            'access_level' => 3
        ]);

        $this->assertTrue($user1->hasEditorAccess());
        $this->assertTrue($user2->hasEditorAccess());
        $this->assertTrue($user3->hasEditorAccess());
    }

    /**
     * Test current user has the given  access.
     *
     * @return void
     */
    public function test_current_user_has_the_given_access()
    {
        $user1 = User::factory()->create([
            'access_level' => 1
        ]);

        $user2 = User::factory()->create([
            'access_level' => 2
        ]);

        $user3 = User::factory()->create([
            'access_level' => 3
        ]);

        $admin = 1;
        $manager = 2;
        $editor = 3;

        $this->assertTrue($user1->hasAccess($admin));
        $this->assertTrue($user1->hasAccess($manager));
        $this->assertTrue($user1->hasAccess($editor));

        $this->assertTrue($user2->hasAccess($manager));
        $this->assertTrue($user2->hasAccess($editor));
        $this->assertFalse($user2->hasAccess($admin));

        $this->assertTrue($user3->hasAccess($editor));
        $this->assertFalse($user3->hasAccess($admin));
        $this->assertFalse($user3->hasAccess($manager));
    }
}
