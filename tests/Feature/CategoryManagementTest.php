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
}
