<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test render dashboard screen and data.
     *
     * @return void
     */
    public function test_render_dashboard_screen()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'name' => 'Admin01',
            'access_level' => 1
        ]);
        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertViewIs('dashboard');
        $response->assertViewHas('users', function($users){
            $name = '';
            
            foreach($users as $user)
            {
                $name .= $user->name;
            }
            
            return $name == 'Admin01';
        });
    }
}
