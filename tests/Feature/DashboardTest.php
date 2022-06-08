<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;

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

        Category::create([
            'title' => 'Főoldal',
            'position' => 2
        ]);

        Category::create([
            'title' => 'Kapcsolat',
            'position' => 1
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
        $response->assertViewHas('categories', function($categories){
            $title = '';
            
            foreach($categories as $category)
            {
                $title .= $category->title;
            }
            
            return $title == 'KapcsolatFőoldal';
        });
    }
}
