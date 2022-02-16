<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Page;
use App\Models\Category;

class PageManagementTest extends TestCase
{
    
    use RefreshDatabase;

    /**
     * Return input datas.
     * 
     * @return array $input
     */
    protected function input()
    {
        return [
            'tittle' => 'Főoldal',
            'slug' => '',
            'tittle_visibility' => true,
            'position' => 1,
            'category_id' => 1
        ];
    }

    /**
     * A user can create a page.
     *
     * @return void
     */
    public function test_user_can_create_a_page()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/page', $this->input());

        $this->assertCount(1, Page::all());
        $this->assertEquals('fooldal', Page::first()->slug);

        $response->assertRedirect('/dashboard');
    }

    /**
     * A category can be atomatically added.
     *
     * @return void
     */
    public function test_a_category_can_be_atomatically_added()
    {
        $this->withoutExceptionHandling();

        $this->post('/category', [
            'tittle' => 'Főoldal',
            'position' => 1
        ]);

        $this->post('/page', [
            'tittle' => 'Főoldal',
            'slug' => '',
            'tittle_visibility' => true,
            'position' => 1,
            'category_id' => 1
        ]);

        $page = Page::first();
        $category = Category::first();

        $this->assertEquals($category->id, $page->category_id);
    }

     /**
     * A category can be atomatically created.
     *
     * @return void
     */
    public function test_a_category_can_be_atomatically_created()
    {
        $this->withoutExceptionHandling();

        $this->post('/page', [
            'tittle' => 'Főoldal',
            'slug' => '',
            'tittle_visibility' => true,
            'position' => 1,
            'category_id' => ''
        ]);

        $page = Page::first();
        $category = Category::first();

        $this->assertCount(1, Category::all());
        $this->assertEquals($category->id, $page->category_id);
        $this->assertEquals($category->tittle, $page->tittle);
        $this->assertEquals(1, $category->position);
    }

    /**
     * Test a page can be updated.
     * 
     * @return void
     */
    public function test_a_page_can_be_updated()
    {
        $this->withoutExceptionHandling();
        
        $this->post('/page', $this->input());

        $page = Page::first();

        $response = $this->patch('/page/'.$page->id, [
            'tittle' => 'Elérhetőségeink',
            'slug' => '',
            'tittle_visibility' => false,
            'position' => 2,
            'category_id' => 2
        ]);

        $this->assertCount(1, Page::all());
        $this->assertEquals('Elérhetőségeink', Page::first()->tittle);
        $this->assertEquals('elerhetosegeink', Page::first()->slug);
        $this->assertEquals(0, Page::first()->tittle_visibility);
        $this->assertEquals(2, Page::first()->position);
        $this->assertEquals(2, Page::first()->category_id);
        $this->assertEquals(2, Category::all()->count());
        $this->assertEquals(Page::first()->category_id, Category::all()->find(2)->id);

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test a page can be deleted.
     * 
     * @return void
     */
    public function test_a_page_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        $this->post('/page', $this->input());

        $page = Page::first();

        $this->assertCount(1, Page::all());

        $response = $this->delete('page/'.$page->id);

        $this->assertCount(0, Page::all());

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test input data are valaid.
     * 
     * @return void
     */
    public function test_input_data_are_valid()
    {
        $response = $this->post('/page', [
            'tittle' => '',
            'slug' => '',
            'tittle_visibility' => '',
            'position' => '',
            'category_id' => ''
        ]);

        $response->assertSessionHasErrors('tittle');
        $response->assertSessionHasErrors('tittle_visibility');
        $response->assertSessionHasErrors('position');
    }

    /**
     * Test get next page position.
     * 
     * @return void
     */
    public function test_get_next_page_position()
    {
        $next = Page::getNextPosition();

        $this->assertEquals(1, $next);
    }

    /**
     * Testset next position.
     * 
     * @return void
     */
    public function test_set_next_page_position()
    {
        $this->post('/page', [
            'tittle' => 'Főoldal',
            'slug' => '',
            'tittle_visibility' => true,
            'position' => Page::getNextPosition(),
            'category_id' => 1
        ]);

        $this->assertEquals(1, Page::first()->position);
    }

    /**
     * Testset default tittle visibility
     * 
     * @return void
     */
    public function test_set_default_tittle_visibility()
    {
        $this->withoutExceptionHandling();
        
        $this->post('/page', [
            'tittle' => 'Főoldal',
            'slug' => '',
            'position' => 1,
            'category_id' => 1
        ]);

        $this->assertEquals(1, Page::first()->tittle_visibility);
    }

    /**
     * Test get all pages for a category.
     * 
     * @return void
     */
    public function test_get_all_pages_for_a_category()
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
            'position' => Page::getNextPosition(),
            'category_id' => 1
        ]);

        $this->post('/page', [
            'tittle' => 'Szolgáltatás2',
            'slug' => '',
            'tittle_visibility' => true,
            'position' => Page::getNextPosition(),
            'category_id' => 1
        ]);

        $this->assertCount(1, Category::all());
        $this->assertCount(2, Page::all());
        $this->assertEquals('Szolgáltatás2', Page::find(2)->tittle);

        $pages = Category::find(1)->pages;

        $this->assertEquals(2, $pages->count());
        $this->assertEquals('Szolgáltatás1', $pages->find(1)->tittle);
        $this->assertEquals('Szolgáltatás2', $pages->find(2)->tittle);
    }

    /**
     * Test retool positions if the request input position already exists.
     * 
     * @return void
     */
    public function test_retool_page_positions()
    {
        $this->withoutExceptionHandling();
        
        $this->post('/page', [
            'tittle' => 'Szolgáltatás1',
            'slug' => '',
            'tittle_visibility' => true,
            'position' => 1,
            'category_id' => 1
        ]);

        $this->post('/page', [
            'tittle' => 'Szolgáltatás2',
            'slug' => '',
            'tittle_visibility' => true,
            'position' => 2,
            'category_id' => 1
        ]);

        $this->post('/page', [
            'tittle' => 'Szolgáltatás3',
            'slug' => '',
            'tittle_visibility' => true,
            'position' => 3,
            'category_id' => 1
        ]);

        $occupied = Page::where('position', 2)->first();
        $this->assertNotNull($occupied);

        $occupiedItems = Page::where('position', '>=', 2)->get();
        $this->assertCount(2, $occupiedItems);

        $this->post('/page', [
            'tittle' => 'Szolgáltatás4',
            'slug' => '',
            'tittle_visibility' => true,
            'position' => 2,
            'category_id' => 1
        ]);

        $first = Page::first();
        $third = Page::find(2);
        $forth = Page::find(3);
        $second = Page::find(4);

        $this->assertCount(4, Page::all());
        $this->assertEquals(1, $first->position);
        $this->assertEquals(2, $second->position);
        $this->assertEquals(3, $third->position);
        $this->assertEquals(4, $forth->position);
    }
}
