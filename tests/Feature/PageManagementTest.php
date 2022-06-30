<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Page;
use App\Models\Category;
use App\Models\Section;
use App\Models\User;

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
            'title' => 'Főoldal',
            'title_visibility' => true,
            'category_id' => 1
        ];
    }

    /**
     * Test render new page screen.
     *
     * @return void
     */
    public function test_render_new_page_screen()
    {
       //$this->withoutExceptionHandling();

        $user1 = User::factory()->create([
            'access_level' => 1
        ]);

        $user2 = User::factory()->create([
            'access_level' => 3
        ]);

        Category::create([
            'title' => 'Főoldal',
            'position' => 2
        ]);

        Category::create([
            'title' => 'Kapcsolat',
            'position' => 1
        ]);

        $response1 = $this->actingAs($user1)->get('/create-page');

        $response2 = $this->actingAs($user2)->get('/create-page');

        $response1->assertViewIs('page.create');
        $response1->assertViewHas('categories', function($categories){
            $name = '';
            
            foreach($categories as $category)
            {
                $name .= $category->title;
            }
            
            return $name == 'FőoldalKapcsolat';
        });
        
        $response2->assertStatus(403);
    }

    /**
     * Test render page template view with first positioned page as index if there is user with admin access.
     * 
     * @return void
     */
    public function test_render_first_page_view()
    {
        $this->withoutExceptionHandling();

        User::factory()->create([
            'access_level' => 1
        ]);

        $response = $this->get('/');

        $response->assertViewIs('page.index');
    }

    /**
     * A user with manager access can create a page.
     *
     * @return void
     */
    public function test_a_manager_can_create_a_page()
    {
        //$this->withoutExceptionHandling();

        $user1 = User::factory()->create([
            'access_level' => 2
        ]);

        $user2 = User::factory()->create([
            'access_level' => 3
        ]);

        $response1 = $this->actingAs($user1)->post('/page', $this->input());

        $response2 = $this->actingAs($user2)->post('/page', [
            'title' => 'Kapcsolat',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->assertCount(1, Page::all());
        $this->assertEquals('fooldal', Page::first()->slug);
        $this->assertTrue(Page::first()->title_visibility);
        $this->assertEquals(1, Page::first()->position);
        $this->assertEquals(1, Page::first()->category_id);

        $response1->assertRedirect('/dashboard');
        $response2->assertStatus(403);
    }

    /**
     * A category can be atomatically added.
     *
     * @return void
     */
    public function test_a_category_can_be_atomatically_added()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'access_level' => 2
        ]);

        Category::create([
            'title' => 'Főoldal',
            'position' => 1
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Főoldal',
            'title_visibility' => true,
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

        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Főoldal',
            'title_visibility' => true,
            'category_id' => ''
        ]);

        $page = Page::first();
        $category = Category::first();

        $this->assertCount(1, Category::all());
        $this->assertEquals($category->id, $page->category_id);
        $this->assertEquals($category->title, $page->title);
        $this->assertEquals(1, $category->position);
    }

     /**
     * Test render update page screen.
     *
     * @return void
     */
    public function test_render_update_page_screen()
    {
       //$this->withoutExceptionHandling();

        $user1 = User::factory()->create([
            'access_level' => 1
        ]);

        $user2 = User::factory()->create([
            'access_level' => 3
        ]);

        Category::create([
            'title' => 'Főoldal',
            'position' => 2
        ]);

        Category::create([
            'title' => 'Kapcsolat',
            'position' => 1
        ]);

        $page = Page::create([
            'title' => 'Főoldal',
            'slug' => '',
            'title_visibility' => true,
            'position' => 1,
            'category_id' => 1
        ]
        );

        $response1 = $this->actingAs($user1)->get('/update-page/'.$page->id);

        $response2 = $this->actingAs($user2)->get('/update-page/'.$page->id);

        $response1->assertViewIs('page.update');
        $response1->assertViewHas('page', function($page){
            return $page->id == 1;
        });
        $response1->assertViewHas('categories', function($categories){
            $name = '';
            
            foreach($categories as $category)
            {
                $name .= $category->title;
            }
            
            return $name == 'FőoldalKapcsolat';
        });
        
        $response2->assertStatus(403);
    }

    /**
     * Test a page can be updated by a manager access.
     * 
     * @return void
     */
    public function test_a_page_can_be_updated_by_manager()
    {
        //$this->withoutExceptionHandling();
        
        $user1 = User::factory()->create([
            'access_level' => 2
        ]);

        $user2 = User::factory()->create([
            'access_level' => 3
        ]);

        $this->actingAs($user1)->post('/page', $this->input());
        
        $page = Page::first();

        $response1 = $this->actingAs($user2)->patch('/page/'.$page->id, [
            'title' => 'Elérhetőségeink',
            'title_visibility' => false,
            'position' => 2,
            'category_id' => 1
        ]);

        $this->assertEquals('Főoldal', Page::first()->title);

        $response1->assertStatus(403);

        $response2 = $this->actingAs($user1)->patch('/page/'.$page->id, [
            'title' => 'Elérhetőségeink',
            'title_visibility' => false,
            'position' => 2,
            'category_id' => 1
        ]);

        $this->assertCount(1, Page::all());
        $this->assertEquals('Elérhetőségeink', Page::first()->title);
        $this->assertEquals('elerhetosegeink', Page::first()->slug);
        $this->assertEquals(0, Page::first()->title_visibility);
        $this->assertEquals(2, Page::first()->position);
        $this->assertEquals(1, Page::first()->category_id);
        $this->assertEquals(1, Category::all()->count());
        $this->assertEquals(Page::first()->category_id, Category::first()->id);

        $response2->assertRedirect('/dashboard');
    }

    /**
     * Test a page can be deleted by a manager access.
     * 
     * @return void
     */
    public function test_a_page_can_be_deleted_by_manager()
    {
        //$this->withoutExceptionHandling();

        $user1 = User::factory()->create([
            'access_level' => 2
        ]);

        $user2 = User::factory()->create([
            'access_level' => 3
        ]);

        $this->actingAs($user1)->post('/page', $this->input());

        $page = Page::first();

        $this->assertCount(1, Page::all());

        $response1 = $this->actingAs($user2)->delete('page/'.$page->id);

        $this->assertCount(1, Page::all());
        $response1->assertStatus(403);

        $response2 = $this->actingAs($user1)->delete('page/'.$page->id);

        $this->assertCount(0, Page::all());
        $response2->assertRedirect('/dashboard');
    }

    /**
     * Test input data for storing are valaid.
     * 
     * @return void
     */
    public function test_input_data_for_storing_are_valid()
    {
        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $response = $this->actingAs($user)->post('/page', [
            'title' => '',
            'title_visibility' => '',
            'category_id' => ''
        ]);

        $response->assertSessionHasErrors('title');
        $response->assertSessionHasErrors('title_visibility');
        $response->assertSessionDoesntHaveErrors('position');
    }

    /**
     * Test input data for updating are valaid.
     * 
     * @return void
     */
    public function test_input_data_for_updating_are_valid()
    {
        //$this->withoutExceptionHandling();
        
        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $page = Page::create([
            'title' => 'Főoldal',
            'slug' => '',
            'title_visibility' => true,
            'category_id' => 1,
            'position' => ''
        ]);

        $response = $this->actingAs($user)->patch('/page/'.$page->id, [
            'title' => '',
            'title_visibility' => '',
            'position' => '',
            'category_id' => ''
        ]);

        $response->assertSessionHasErrors('title');
        $response->assertSessionHasErrors('title_visibility');
        $response->assertSessionHasErrors('position');
    }

    /**
     * Test a page title must be unique.
     * 
     * @return void
     */
    public function test_a_page_title_must_be_unique()
    {
        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $this->actingAs($user)->post('/page', $this->input());

        $response = $this->actingAs($user)->post('/page', $this->input());

        $this->assertCount(1, Page::all());
        $response->assertSessionHasErrors('title');
    }

    /**
     * Test get next page position.
     * 
     * @return void
     */
    public function test_get_next_page_position()
    {
        $this->withoutExceptionHandling();

        Category::create([
            'title' => 'Főoldal',
            'position' => 2
        ]);

        Category::create([
            'title' => 'Kapcsolat',
            'position' => 1
        ]);

        Page::create([
            'title' => 'Oldal1',
            'slug' => '',
            'title_visibility' => true,
            'position' => 1,
            'category_id' => 1
        ]);

        Page::create([
            'title' => 'Oldal2',
            'slug' => '',
            'title_visibility' => true,
            'position' => 2,
            'category_id' => 1
        ]);

        Page::create([
            'title' => 'Oldal3',
            'slug' => '',
            'title_visibility' => true,
            'position' => 1,
            'category_id' => 2
        ]);

        $next1 = Page::getNextPosition(1);

        $next2 = Page::getNextPosition(2);

        $next3 = Page::getNextPosition(3);

        $this->assertEquals(3, $next1);
        $this->assertEquals(2, $next2);
        $this->assertEquals(1, $next3);
    }

    /**
     * Test set next position.
     * 
     * @return void
     */
    public function test_set_next_page_position()
    {
        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Főoldal',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->assertEquals(1, Page::first()->position);
    }

    /**
     * Test set next position if position attribute is null.
     * 
     * @return void
     */
    public function test_page_position_is_null()
    {
        Page::create([
            'title' => 'Főoldal',
            'slug' => '',
            'title_visibility' => true,
            'category_id' => 1,
            'position' => ''
        ]);

        $this->assertEquals(1, Page::first()->position);
    }

     /**
     * Test set next position in the selected category if the category property has been changed in update form.
     * 
     * @return void
     */
    public function test_set_next_position_if_the_category_changed()
    {
        $this->withoutExceptionHandling();
        
        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $page1 = Page::create([
            'title' => 'Főoldal',
            'slug' => '',
            'title_visibility' => true,
            'category_id' => 1,
            'position' => ''
        ]);

        $page2 = Page::create([
            'title' => 'Kapcsolat',
            'slug' => '',
            'title_visibility' => true,
            'category_id' => 2,
            'position' => ''
        ]);

        $page3 = Page::create([
            'title' => 'Elérhetőségeink',
            'slug' => '',
            'title_visibility' => true,
            'category_id' => 2,
            'position' => ''
        ]);

        $response = $this->actingAs($user)->patch('/page/'.$page1->id, [
            'title' => 'Fpoldal',
            'title_visibility' => true,
            'position' => 2,
            'category_id' => 2
        ]);

        $this->assertEquals(3, Page::find(1)->position);
    }

    /**
     * Test set default title visibility
     * 
     * @return void
     */
    public function test_set_default_title_visibility()
    {
        $this->withoutExceptionHandling();
        
        Page::create([
            'title' => 'Főoldal',
            'slug' => '',
            'category_id' => 1,
            'position' => 1
        ]);

        $this->assertEquals(1, Page::first()->title_visibility);
    }

    /**
     * Test get all pages for a category.
     * 
     * @return void
     */
    public function test_get_all_pages_for_a_category()
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
            'category_id' => 1
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Szolgáltatás2',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->assertCount(1, Category::all());
        $this->assertCount(2, Page::all());
        $this->assertEquals('Szolgáltatás2', Page::find(2)->title);

        $pages = Category::find(1)->pages;

        $this->assertEquals(2, $pages->count());
        $this->assertEquals('Szolgáltatás1', $pages->find(1)->title);
        $this->assertEquals('Szolgáltatás2', $pages->find(2)->title);
    }

    /**
     * Test retool positions if the request input position already exists.
     * 
     * @return void
     */
    public function test_retool_page_positions()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Szolgáltatás1',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Szolgáltatás2',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Szolgáltatás3',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $occupied = Page::where('position', 2)->first();
        $this->assertNotNull($occupied);

        $occupiedItems = Page::where('position', '>=', 2)->get();
        $this->assertCount(2, $occupiedItems);

        $this->actingAs($user)->post('/page', [
            'title' => 'Szolgáltatás4',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Szolgáltatás5',
            'title_visibility' => true,
            'category_id' => 2
        ]);

        $first = Page::first();
        $third = Page::find(2);
        $forth = Page::find(3);
        $second = Page::find(4);

        $this->actingAs($user)->patch('/page/'.$second->id, [
            'title' => 'Szolgáltatás4',
            'title_visibility' => true,
            'position' => 2,
            'category_id' => 1
        ]);

        $this->assertCount(5, Page::all());
        $this->assertEquals(1, Page::find(5)->position);
        $this->assertEquals(1, Page::first()->position);
        $this->assertEquals(2, Page::find(4)->position);
        $this->assertEquals(3, Page::find(2)->position);
        $this->assertEquals(4, Page::find(3)->position);
    }

    /**
     * Test get page of the section.
     * 
     * @return void
     */
    public function test_get_page_of_the_section()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Fpoldal',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->post('/section', [
            'title' => 'Szekció1',
            'title_visibility' => true,
            'position' => Section::getNextPosition(),
            'page_id' => 1
        ]);

        $this->post('/section', [
            'title' => 'Szekció2',
            'title_visibility' => true,
            'position' => Section::getNextPosition(),
            'page_id' => 1
        ]);

        $this->assertCount(1, Page::all());
        $this->assertCount(2, Section::all());

        $page1 = Section::find(1)->page;
        $page2 = Section::find(2)->page;

        $this->assertEquals($page1->id, $page2->id);
    }

    /**
     * Test a page is unique while storing.
     * 
     * @return void
     */
    public function test_page_is_unique_while_storing()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $this->actingAs($user)->post('/page', $this->input());
        
        $response = $this->actingAs($user)->post('/page', [
            'title' => 'Főoldal',
            'title_visibility' => false,
            'category_id' => 1
        ]);
        
        $this->assertCount(1, Page::all());
        $response->assertSessionHasErrors('title');
    }

    /**
     * Test a page is unique while updating.
     * 
     * @return void
     */
    public function test_page_is_unique_while_updating()
    {
        $user = User::factory()->create([
            'access_level' => 2
        ]);
        
        $this->actingAs($user)->post('/page', $this->input());

        $page = Page::first();

        $this->patch('/page/'.$page->id, [
            'title' => 'Főoldal',
            'title_visibility' => false,
            'position' => 1,
            'category_id' => 1
        ]);

        $this->assertFalse(Page::first()->title_visibility);
        
        $this->actingAs($user)->post('/page', [
            'title' => 'Rólunk',
            'title_visibility' => false,
            'category_id' => 1
        ]);

        $response = $this->patch('/page/'.$page->id, [
            'title' => 'Rólunk',
            'title_visibility' => false,
            'position' => 1,
            'category_id' => 1
        ]);
        
        $this->assertEquals('Főoldal', Page::first()->title);
        $response->assertSessionHasErrors('title');
    }
}
