<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Section;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;

class SectionManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Return section input data.
     * 
     * @return array $input
     */
    protected function input()
    {
        return [
            'title' => 'Hírek',
            'title_visibility' => true,
            'position' => 1,
            'page_id' => 1
        ];
    }

    /**
     * Test render new section screen.
     *
     * @return void
     */
    public function test_render_new_section_screen()
    {
       //$this->withoutExceptionHandling();

       Page::create([
            'title' => 'Oldal1',
            'title_visibility' => true,
            'slug' => '',
            'category_id' => 1,
            'position' => 1
        ]);

        $page = Page::create([
            'title' => 'Oldal2',
            'title_visibility' => true,
            'slug' => '',
            'category_id' => 1,
            'position' => 2
        ]);
         
        $user1 = User::factory()->create([
            'access_level' => 2
        ]);
     
        $user2 = User::factory()->create([
            'access_level' => 3
        ]);
    
        $response1 = $this->actingAs($user2)->get('/'.$page->id.'/create-section');
       
        $response2 = $this->actingAs($user1)->get('/'.$page->id.'/create-section');

        $response2->assertViewIs('section.create');
        $response2->assertViewHas([
            'pageId' => 2
        ]);
        $response2->assertViewHas([
            'next' => 1
        ]);
        
        $response1->assertStatus(403);
    }
    
    /**
     * Test a section can be created by a manager.
     *
     * @return void
     */
    public function test_a_section_can_be_created_by_manager()
    {
        //$this->withoutExceptionHandling();

        $user1 = User::factory()->create([
            'access_level' => 2
        ]);

        $user2 = User::factory()->create([
            'access_level' => 3
        ]);

        $this->actingAs($user1)->post('/page', [
            'title' => 'Főoldal',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $response1 = $this->actingAs($user2)->post('/section', $this->input());

        $this->assertCount(0, Section::all());
        $response1->assertStatus(403);

        $response2 = $this->actingAs($user1)->post('/section', $this->input());

        $this->assertCount(1, Section::all());
        $this->assertEquals('Hírek', Section::first()->title);
        $response2->assertRedirect('/fooldal');
    }

     /**
     * Test render update section screen.
     *
     * @return void
     */
    public function test_render_update_section_screen()
    {
       //$this->withoutExceptionHandling();

        $user1 = User::factory()->create([
            'access_level' => 1
        ]);

        $user2 = User::factory()->create([
            'access_level' => 3
        ]);

        Page::create([
            'title' => 'Főoldal',
            'slug' => '',
            'title_visibility' => true,
            'position' => 1,
            'category_id' => 1
        ]);

        $section = Section::create([
            'title' => 'Szekció',
            'slug' => '',
            'title_visibility' => true,
            'position' => 1,
            'page_id' => 1
        ]);

        $response1 = $this->actingAs($user1)->get('/update-section/'.$section->id);

        $response2 = $this->actingAs($user2)->get('/update-section/'.$section->id);

        $response1->assertViewIs('section.update');
        $response1->assertViewHas('section', function($section){
            return $section->id == 1;
        });
        $response1->assertViewHas([
            'max' => 1
        ]);
        
        $response2->assertStatus(403);
    }


    /**
     * Test a section can be updated by a manager.
     *
     * @return void
     */
    public function test_a_section_can_be_updated_by_manager()
    {
        //$this->withoutExceptionHandling();

        $user1 = User::factory()->create([
            'access_level' => 2
        ]);

        $user2 = User::factory()->create([
            'access_level' => 3
        ]);

        $this->actingAs($user1)->post('/page', [
            'title' => 'Főoldal',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->post('/section', $this->input());

        $this->assertCount(1, Section::all());
        
        $section = Section::first();

        $response1 = $this->actingAs($user2)->patch('/section/'.$section->id, [
            'title' => 'Érdekességek',
            'title_visibility' => false,
            'position' => 1,
            'page_id' => 1
        ]);

        $this->assertEquals('Hírek', Section::first()->title);
        $response1->assertStatus(403);

        $response2 = $this->actingAs($user1)->patch('/section/'.$section->id, [
            'title' => 'Érdekességek',
            'title_visibility' => false,
            'position' => 1,
            'page_id' => 1
        ]);

        $this->assertEquals('Érdekességek', Section::first()->title);
        $this->assertEquals('erdekessegek', Section::first()->slug);
        $this->assertFalse(Section::first()->title_visibility);
        $response2->assertRedirect('/fooldal');
    }

     /**
     * Test a section can be deleted by a manager.
     *
     * @return void
     */
    public function test_a_section_can_be_deleted_by_mamager()
    {
        //$this->withoutExceptionHandling();

        $user1 = User::factory()->create([
            'access_level' => 2
        ]);

        $user2 = User::factory()->create([
            'access_level' => 3
        ]);

        $this->actingAs($user1)->post('/page', [
            'title' => 'Főoldal',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->post('/section', $this->input());

        $section = Section::first();

        $response1 = $this->actingAs($user2)->delete('/section/'.$section->id);

        $this->assertCount(1, Section::all());
        $response1->assertStatus(403);

        $response2 = $this->actingAs($user1)->delete('/section/'.$section->id);

        $this->assertCount(0, Section::all());
        $response2->assertRedirect('/fooldal');
    }

    /**
     * Test validation of input data.
     * 
     * @return void
     */
    public function test_section_input_data_validation()
    {
        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $response = $this->from('/fooldal')->actingAs($user)->post('/section', [
            'title' => '',
            'title_visibility' => 'data',
            'position' => 'data',
            'page_id' => 'two'
        ]);

        $response->assertSessionHasErrors('title');
        $response->assertSessionHasErrors('title_visibility');
        $response->assertSessionHasErrors('position');
        $response->assertSessionHasErrors('page_id');
        $response->assertRedirect('/fooldal');
    }

    /**
     * Test get next section position.
     * 
     * @return void
     */
    public function test_get_next_section_position()
    {
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

        Section::create([
            'title' => 'Szekció1',
            'slug' => '',
            'title_visibility' => true,
            'position' => 1,
            'page_id' => 1
        ]);

        Section::create([
            'title' => 'Szekció2',
            'slug' => '',
            'title_visibility' => true,
            'position' => 2,
            'page_id' => 1
        ]);

        Section::create([
            'title' => 'Szekció3',
            'slug' => '',
            'title_visibility' => true,
            'position' => 1,
            'page_id' => 2
        ]);

        $next1 = Section::getNextPosition(1);

        $next2 = Section::getNextPosition(2);

        $next3 = Section::getNextPosition(3);

        $this->assertEquals(3, $next1);
        $this->assertEquals(2, $next2);
        $this->assertEquals(1, $next3);
    }

    /**
     * Test set next position.
     * 
     * @return void
     */
    public function test_set_next_section_position()
    {
        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Főoldal',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->post('/section', $this->input());
        
        $this->post('/section', [
            'title' => 'Érdekességek',
            'title_visibility' => true,
            'position' => Section::getNextPosition(1),
            'page_id' => 1
        ]);

        $this->assertEquals(2, Section::find(2)->position);
    }

    /**
     * Test set default title visibility
     * 
     * @return void
     */
    public function test_set_default_section_title_visibility()
    {
        $this->withoutExceptionHandling();

        Page::create([
            'title' => 'Főoldal',
            'slug' => '',
            'title_visibility' => true,
            'position' => Page::getNextPosition(1),
            'category_id' => 1
        ]);

        Section::create([
            'title' => 'Hírek',
            'slug' => '',
            'position' => 1,
            'page_id' => 1
        ]);

        $this->assertEquals(1, Section::first()->title_visibility);
    }

    /**
     * Test set next position if position attribute is null.
     * 
     * @return void
     */
    public function test_section_position_is_null()
    {
        Page::create([
            'title' => 'Főoldal',
            'slug' => '',
            'title_visibility' => true,
            'position' => Page::getNextPosition(1),
            'category_id' => 1
        ]);

        Section::create([
            'title' => 'Hírek',
            'slug' => '',
            'title_visibility' => true,
            'page_id' => 1,
            'position' => ''
        ]);

        $this->assertEquals(1, Section::first()->position);
    }

    /**
     * Test retool positions if the request input position already exists.
     * 
     * @return void
     */
    public function test_retool_section_positions()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'access_level' => 2
        ]);
        
        $this->actingAs($user)->post('/page', [
            'title' => 'Főoldal',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Kapcsolat',
            'title_visibility' => true,
            'category_id' => 2
        ]);
        
        $this->post('/section', [
            'title' => 'Szkció1',
            'title_visibility' => true,
            'position' => 1,
            'page_id' => 1
        ]);

        $this->post('/section', [
            'title' => 'Szekció2',
            'title_visibility' => true,
            'position' => 2,
            'page_id' => 1
        ]);

        $this->post('/section', [
            'title' => 'Szekció3',
            'title_visibility' => true,
            'position' => 3,
            'page_id' => 1
        ]);

        $occupied = Section::where('position', 2)->first();
        $this->assertNotNull($occupied);

        $occupiedItems = Section::where('position', '>=', 2)->get();
        $this->assertCount(2, $occupiedItems);

        $this->post('/section', [
            'title' => 'Szekció4',
            'title_visibility' => true,
            'position' => 2,
            'page_id' => 1
        ]);

        $this->post('/section', [
            'title' => 'Szekció1',
            'title_visibility' => true,
            'position' => 1,
            'page_id' => 2
        ]);

        $first = Section::first();
        $third = Section::find(2);
        $forth = Section::find(3);
        $second = Section::find(4);
        $firstAtSecond = Section::find(5);

        $this->assertCount(5, Section::all());
        $this->assertEquals(1, $firstAtSecond->position);
        $this->assertEquals(1, $first->position);
        $this->assertEquals(2, $second->position);
        $this->assertEquals(3, $third->position);
        $this->assertEquals(4, $forth->position);
    }

     /**
     * Test get all sections for a page.
     * 
     * @return void
     */
    public function test_get_all_sections_for_a_page()
    {
        $this->withoutExceptionHandling();
        
        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Főoldal',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->post('/section', [
            'title' => 'Szekció1',
            'title_visibility' => true,
            'position' => Section::getNextPosition(1),
            'page_id' => 1
        ]);

        $this->post('/section', [
            'title' => 'Szekció2',
            'title_visibility' => true,
            'position' => Section::getNextPosition(1),
            'page_id' => 1
        ]);

        $this->assertCount(1, Page::all());
        $this->assertCount(2, Section::all());

        $sections = Page::find(1)->sections;

        $this->assertEquals(2, $sections->count());
        $this->assertEquals('Szekció1', $sections->find(1)->title);
        $this->assertEquals('Szekció2', $sections->find(2)->title);
    }

    /**
     * Test get section of the post.
     * 
     * @return void
     */
    public function test_get_section_of_the_post()
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
            'position' => Section::getNextPosition(1),
            'page_id' => 1
        ]);

        $this->post('/section', [
            'title' => 'Szekció2',
            'title_visibility' => true,
            'position' => Section::getNextPosition(1),
            'page_id' => 1
        ]);

        $this->post('/post', [
            'title' => 'Post1',
            'title_visibility' => true,
            'description' => '',
            'content' => 'Tartalom',
            'post_image' => '',
            'position' => 1,
            'section_id' => 2
        ]);

        $this->assertCount(1, Page::all());
        $this->assertCount(2, Section::all());
        $this->assertCount(1, Post::all());

        $section2 = Post::first()->section;
        
        $this->assertEquals('Szekció2', $section2->title);
        $this->assertNotEquals($section2->id, Section::first()->id);
    }

    /**
     * Test the section title is unique on page while storing.
     * 
     * @return void
     */
    public function test_title_is_unique_on_page_while_stroing()
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

        $this->post('/section', $this->input());

        $response = $this->post('/section', $this->input());

        $this->assertCount(1, Section::all());

        $response->assertSessionHasErrors('title');
    }

    /**
     * Test the section title is unique on page while updating.
     * 
     * @return void
     */
    public function test_title_is_unique_on_page_while_updating()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Főoldal',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->post('/section', $this->input());

        $section = Section::first();

        $this->patch('/section/'.$section->id, [
            'title' => 'Hírek',
            'title_visibility' => false,
            'position' => 1,
            'page_id' => 1
        ]);

        $this->assertCount(1, Section::all());
        $this->assertFalse(Section::first()->title_visibility);

        $this->post('/section', [
            'title' => 'Rólunk',
            'title_visibility' => false,
            'position' => 2,
            'page_id' => 1
        ]);

        $response = $this->patch('/section/'.$section->id, [
            'title' => 'Rólunk',
            'title_visibility' => false,
            'position' => 2,
            'page_id' => 1
        ]);

        $this->assertCount(2, Section::all());
        $this->assertEquals('Hírek', Section::first()->title);
        $this->assertEquals(1, Section::first()->position);
        $response->assertSessionHasErrors('title');
    }
}
