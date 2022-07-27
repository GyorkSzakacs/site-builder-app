<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Page;

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
        //$this->withoutExceptionHandling();

        $user1 = User::factory()->create([
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

        Page::create([
            'title' => 'Főoldal',
            'slug' => 'fooldal',
            'title_visibility' => true,
            'position' => 1,
            'category_id' => 1
        ]);

        $response1 = $this->actingAs($user1)->get('/dashboard');

        $response1->assertViewIs('dashboard');
        $response1->assertViewHas('users', function($users){
            $name = '';
            
            foreach($users as $user)
            {
                $name .= $user->name;
            }
            
            return $name == 'Admin01';
        });
        $response1->assertViewHas('categories', function($categories){
            $title = '';
            
            foreach($categories as $category)
            {
                $title .= $category->title;
            }
            
            return $title == 'KapcsolatFőoldal';
        });
        $response1->assertViewHas('pages', function($pages){
            $title = '';
            
            foreach($pages as $page)
            {
                $title .= $page->title;
            }
            
            return $title == 'Főoldal';
        });

        $user2 = User::factory()->create([
            'access_level' => 3
        ]);
        
        $response2 = $this->actingAs($user2)->get('/dashboard');

        $response2->assertStatus(403);
    }
}
