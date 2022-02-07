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
     * A user can create a page.
     *
     * @return void
     */
    public function test_user_can_create_a_page()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/page', [
            'tittle' => 'Főoldal',
            'slug' => '',
            'tittle_visibility' => true,
            'position' => 1,
            'category_id' => 1
        ]);

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
}
