<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Page;
use App\Models\User;

class CategoryManagementTest extends TestCase
{
    
    use RefreshDatabase;

    /**
     * Return input data
     * 
     * @return array
     */
    protected function input()
    {

        return [
            'title' => 'Főoldal',
            'position' => 2
        ];

    }

    /**
     * Test render new category screen.
     *
     * @return void
     */
    public function test_render_new_category_screen()
    {
       //$this->withoutExceptionHandling();

        $user1 = User::factory()->create([
            'access_level' => 1
        ]);

        $user2 = User::factory()->create([
            'access_level' => 3
        ]);

        $response1 = $this->actingAs($user1)->get('/create-category');

        $response2 = $this->actingAs($user2)->get('/create-category');

        $response1->assertViewIs('category.create');
        $response1->assertViewHas([
            'next' => 1
        ]);
        $response2->assertStatus(403);
    }

    /**
     * Test a User with manager access can create a Category
     *
     * @return void
     */
    public function test_a_manager_can_create_category()
    {
        $user1 = User::factory()->create([
            'access_level' => 2
        ]);

        $user2 = User::factory()->create([
            'access_level' => 3
        ]);

        $response1 = $this->actingAs($user1)->post('/category', $this->input());

        $response2 = $this->actingAs($user2)->post('/category', $this->input());

        $this->assertCount(1, Category::all());
        $this->assertEquals(2, Category::first()->position);
        $response1->assertRedirect('/dashboard');
        $response2->assertStatus(403);

    }

    /**
     * Validate request data
     *
     * @return void
     */
    public function test_request_data_is_valid()
    {
        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $response = $this->actingAs($user)->post('/category',[
            'title' => '',
            'position' => 'data'
        ]);

        $response->assertSessionHasErrors('title');
        $response->assertSessionHasErrors('position');
    }

    /**
     * Test render update category screen.
     *
     * @return void
     */
    public function test_render_update_category_screen()
    {
       //$this->withoutExceptionHandling();

        $user1 = User::factory()->create([
            'access_level' => 1
        ]);

        $user2 = User::factory()->create([
            'access_level' => 3
        ]);

        $category = Category::create($this->input());

        $response1 = $this->actingAs($user1)->get('/update-category/'.$category->id);

        $response2 = $this->actingAs($user2)->get('/update-category/'.$category->id);

        $response1->assertViewIs('category.update');
        $response1->assertViewHas('category', function($category){
            return $category->id == 1;
        });
        $response1->assertViewHas([
            'max' => 2
        ]);
        $response2->assertStatus(403);
    }

    /**
     * A category can be updated by an user with manager access.
     *
     * @return void
     */
    public function test_a_category_can_be_updated_by_a_manager()
    {
        $user1 = User::factory()->create([
            'access_level' => 2
        ]);

        $user2 = User::factory()->create([
            'access_level' => 3
        ]);

        $category = Category::create($this->input());

        $response1 = $this->actingAs($user1)->patch('/category/'.$category->id,[
            'title' => 'Kapcsolat',
            'position' => 2
        ]);

        $response2 = $this->actingAs($user2)->patch('/category/'.$category->id,[
            'title' => 'Valami más',
            'position' => 3
        ]);

        $this->assertEquals('Kapcsolat', Category::first()->title);
        $this->assertEquals(2, Category::first()->position);

        $response1->assertRedirect('/dashboard');
        $response2->assertStatus(403);

    }

    /**
     * A category can be deleted by a manager.
     *
     * @return void
     */
    public function test_a_category_can_be_deleted_by_a_manager()
    { 
       $user1 = User::factory()->create([
            'access_level' => 3
        ]);

        $user2 = User::factory()->create([
            'access_level' => 2
        ]);

        $category = Category::create($this->input());

        $this->assertCount(1, Category::all());
        
        $response1 = $this->actingAs($user1)->delete('/category/'.$category->id);

       $this->assertCount(1, Category::all());
        $response1->assertStatus(403);

       $response2 = $this->actingAs($user2)->delete('/category/'.$category->id);

       $this->assertCount(0, Category::all());
       $response2->assertRedirect('/dashboard');

    }

    /**
     * Test set next position if position attribute is null.
     * 
     * @return void
     */
    public function test_category_position_is_null()
    {
        Category::create([
            'title' => 'Főoldal',
            'position' => ''
        ]);

        $this->assertEquals(1, Category::first()->position);
    }

    /**
     * Get next category position test.
     *
     * @return void
     */
    public function test_get_next_category_position()
    {
        
        $next = Category::getNextPosition();

        $this->assertEquals(1, $next);
    }

     /**
     * Test get category of the page.
     * 
     * @return void
     */
    public function test_get_category_of_the_page()
    {
        $this->withoutExceptionHandling();
        
        Category::create([
            'title' => 'Szolgáltatások',
            'position' => 1
        ]);

        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Szolgáltatás1',
            'title_visibility' => true,
            'position' => Page::getNextPosition(),
            'category_id' => 1
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Szolgáltatás2',
            'title_visibility' => true,
            'position' => Page::getNextPosition(),
            'category_id' => 1
        ]);

        $this->assertCount(1, Category::all());
        $this->assertCount(2, Page::all());

        $category1 = Page::find(1)->category;
        $category2 = Page::find(2)->category;

        $this->assertEquals($category1->id, $category2->id);
    }

    /**
     * Test retool positions if the request input position already exists.
     * 
     * @return void
     */
    public function test_retool_positions()
    {
        $this->withoutExceptionHandling();
        
        Category::create([
            'title' => 'Főoldal',
            'position' => 1
        ]);

        Category::create([
            'title' => 'Rólunk',
            'position' => 2
        ]);

        Category::create([
            'title' => 'Szolgáltatások',
            'position' => 3
        ]);

        $occupied = Category::where('position', 2)->first();
        $this->assertNotNull($occupied);

        $occupiedItems = Category::where('position', '>=', 2)->get();
        $this->assertCount(2, $occupiedItems);

        Category::create([
            'title' => 'Kapcsolat',
            'position' => 2
        ]);

        $first = Category::first();
        $third = Category::find(2);
        $forth = Category::find(3);
        $second = Category::find(4);

        $this->assertCount(4, Category::all());
        $this->assertEquals(1, $first->position);
        $this->assertEquals(2, $second->position);
        $this->assertEquals(3, $third->position);
        $this->assertEquals(4, $forth->position);
    }
}
