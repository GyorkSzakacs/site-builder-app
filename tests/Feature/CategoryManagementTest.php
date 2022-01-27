<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;

class CategoryManagementTest extends TestCase
{
    
    use RefreshDatabase;

    /**
     * Can a User create a Category
     *
     * @return void
     */
    public function test_a_user_can_create_category()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/category',[
            'tittle' => 'Home',
            'position' => 1
        ]);

        $response->assertOk();

        $this->assertCount(1, Category::all());
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
            'position' => 1
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

        $this->post('/category',[
            'tittle' => 'HOme',
            'position' => 1
        ]);

        $category = Category::first();

        $response = $this->patch('/category/'.$category->id,[
            'tittle' => 'Contact',
            'position' => 2
        ]);


        $this->assertEquals('Contact', Category::first()->tittle);
        $this->assertEquals(2, Category::first()->position);
    }

    /**
     * A category can be deleted
     *
     * @return void
     */
    public function test_a_category_can_be_deleted()
    {
        
        $this->withoutExceptionHandling();

        $this->post('/category',[
            'tittle' => 'HOme',
            'position' => 1
        ]);

        $category = Category::first();

        $this->assertCount(1, Category::all());
        
        $response = $this->delete('/category/'.$category->id);

       $this->assertCount(0, Category::all());
    }
}
