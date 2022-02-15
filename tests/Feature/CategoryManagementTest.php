<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Page;

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
            'tittle' => 'Főoldal',
            'position' => 2
        ];

    }

    /**
     * Can a User create a Category
     *
     * @return void
     */
    public function test_a_user_can_create_category()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/category', $this->input());

        $this->assertCount(1, Category::all());
        $this->assertEquals(2, Category::first()->position);
        $response->assertRedirect('/dashboard');

    }

    /**
     * Validate request data
     *
     * @return void
     */
    public function test_request_data_is_valid()
    {
        
        $response = $this->post('/category',[
            'tittle' => '',
            'position' => ''
        ]);

        $response->assertSessionHasErrors('tittle');
    }

    /**
     * A category can be updated
     *
     * @return void
     */
    public function test_a_category_can_be_updated()
    {
        
        $this->withoutExceptionHandling();

        $this->post('/category', $this->input());

        $category = Category::first();

        $response = $this->patch('/category/'.$category->id,[
            'tittle' => 'Kapcsolat',
            'position' => 2
        ]);


        $this->assertEquals('Kapcsolat', Category::first()->tittle);
        $this->assertEquals(2, Category::first()->position);

        $response->assertRedirect('/dashboard');

    }

    /**
     * A category can be deleted
     *
     * @return void
     */
    public function test_a_category_can_be_deleted()
    {
        
        $this->withoutExceptionHandling();

        $this->post('/category', $this->input());

        $category = Category::first();

        $this->assertCount(1, Category::all());
        
        $response = $this->delete('/category/'.$category->id);

       $this->assertCount(0, Category::all());
       $response->assertRedirect('/dashboard');

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
        
        $this->post('/category', [
            'tittle' => 'Szolgáltatások',
            'position' => 1
        ]);

        $this->post('/page', [
            'tittle' => 'Szolgáltatás1',
            'slug' => '',
            'tittle_visibility' => true,
            'position' => '',
            'category_id' => 1
        ]);

        $this->post('/page', [
            'tittle' => 'Szolgáltatás2',
            'slug' => '',
            'tittle_visibility' => true,
            'position' => '',
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
        
        $this->post('/category', [
            'tittle' => 'Főoldal',
            'position' => 1
        ]);

        $this->post('/category', [
            'tittle' => 'Rólunk',
            'position' => 2
        ]);

        $this->post('/category', [
            'tittle' => 'Szolgáltatások',
            'position' => 3
        ]);

        $occupied = Category::where('position', 2)->first();
        $this->assertNotNull($occupied);

        $occupiedItems = Category::where('position', '>=', 2)->get();
        $this->assertCount(2, $occupiedItems);

        $this->post('/category', [
            'tittle' => 'Kapcsolat',
            'position' => 2
        ]);

        $first = Category::first();
        $third = Category::find(2);
        $forth = Category::find(3);
        $second = Category::find(4);

        $this->assertCount(4, Category::all());
        $this->assertEquals(1, $first->position);
        $this->assertEquals(2, $second->position);
        //$this->assertEquals(3, $third->position);
        //$this->assertEquals(4, $forth->position);
    }
}
