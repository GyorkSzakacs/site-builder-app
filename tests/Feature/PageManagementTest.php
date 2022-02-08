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

        $response->assertStatus(200);
        $this->assertCount(1, Page::all());
        $this->assertEquals('fooldal', Page::first()->slug);
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

        $this->patch('/page/'.$page->id, [
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
    }

    /**
     * Test a page can be deleted.
     * 
     * @return void
     */
    public function test_a_page_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        $this->post('page', $this->input());

        $page = Page::first();

        $this->assertCount(1, Page::all());

        $this->delete('page/'.$page->id);

        $this->assertCount(0, Page::all());
    }
}
