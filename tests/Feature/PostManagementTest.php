<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;

class PostManagementTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Create parent builder parts for a post.
     * 
     * @return void
     */
    protected function createParents()
    {
        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Főoldal',
            'title_visibility' => true,
            'category_id' => 1
        ]);

        $this->post('/section', [
            'title' => 'Szekció',
            'title_visibility' => true,
            'position' => Section::getNextPosition(1),
            'page_id' => 1
        ]);
    }

    /**
     * Test render view post screen.
     *
     * @return void
     */
    public function test_render_view_post_screen()
    {
       $this->withoutExceptionHandling();

        $page = Page::create([
            'title' => 'Főoldal',
            'slug' => '',
            'title_visibility' => true,
            'category_id' => 1,
            'position' => 1
        ]);

        $section = Section::create([
            'title' => 'Szekció',
            'slug' => '',
            'title_visibility' => true,
            'page_id' => 1,
            'position' => Section::getNextPosition(1)
        ]);

        $post = Post::create([
            'title' => 'Poszt',
            'slug' => '',
            'title_visibility' => true,
            'description' => '',
            'content' => 'Ez az első bejegyzés',
            'post_image' => '',
            'section_id' => 1,
            'position' => Section::getNextPosition(1)
        ]);
    
        $response = $this->get('/'.$page->slug.'/'.$section->slug.'/'.$post->slug);
       
        $response->assertViewIs('post.show');
        $response->assertViewHas('post', function($post){
            return $post->title == 'Poszt';
        });
    }

     /**
     * Test render new post screen.
     *
     * @return void
     */
    public function test_render_new_post_screen()
    {
       //$this->withoutExceptionHandling();

       Page::create([
            'title' => 'Főoldal',
            'slug' => '',
            'title_visibility' => true,
            'category_id' => 1,
            'position' => 1
        ]);

        $section = Section::create([
            'title' => 'Szekció',
            'slug' => '',
            'title_visibility' => true,
            'page_id' => 1,
            'position' => Section::getNextPosition(1)
        ]);
    
        $response1 = $this->get('/'.$section->id.'/create-post');
       
        $user = User::factory()->create([
            'access_level' => 3
        ]);

        $response2 = $this->actingAs($user)->get('/'.$section->id.'/create-post');

        $response2->assertViewIs('post.create');
        $response2->assertViewHas([
            'sectionId' => 1
        ]);
        /*$response2->assertViewHas([
            'next' => 1
        ]);*/
        
        $response1->assertStatus(403);
    }

    /**
     * Test a post can be created by a user with exitor access.
     *
     * @return void
     */
    public function test_a_post_can_be_created_by_editor()
    {
        //$this->withoutExceptionHandling();
        
        $user1 = User::factory()->create([
            'access_level' => 3
        ]);

        $user2 = User::factory()->create([
            'access_level' => 4
        ]);

        $image = UploadedFile::fake()->image('image.jpg');

        $this->createParents();

        $response1 = $this->actingAs($user2)->post('/post', [
            'title' => 'Első cikkem',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.',
            'post_image' => $image,
            'position' => 1,
            'section_id' => 1
        ]);

        $this->assertCount(0, Post::all());
        Storage::disk('local')->assertMissing('images/'.$image->hashName());
        $response1->assertStatus(403);

        $response2 = $this->actingAs($user1)->post('/post', [
            'title' => 'Első cikkem',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.',
            'post_image' => $image,
            'position' => 1,
            'section_id' => 1
        ]);

        $this->assertCount(1, Post::all());

        Storage::disk('local')->assertExists('images/'.$image->hashName());
        $this->assertEquals('images/'.$image->hashName(), Post::first()->post_image);
        
        $this->assertEquals('elso-cikkem', Post::first()->slug);
        $this->assertEquals(1, Post::first()->section_id);
        $this->assertTrue(Post::first()->title_visibility);

        $response2->assertRedirect('/fooldal/szekcio/elso-cikkem');
    }

    /**
     * Test input data requirement validation.
     * 
     * @return void
     */
    public function test_post_input_data_requirement_validation()
    {
        $user = User::factory()->create([
            'access_level' => 1
        ]);

        $response = $this->actingAs($user)->post('/post', [
            'title' => '',
            'title_visibility' => '',
            'description' => '',
            'content' => '',
            'post_image' => '',
            'position' => '',
            'section_id' => ''
        ]);

        $response->assertSessionHasErrors('title');
        $response->assertSessionHasErrors('title_visibility');
        $response->assertSessionHasErrors('content');
        $response->assertSessionHasErrors('position');
        $response->assertSessionHasErrors('section_id');
    }

    /**
     * Test input data type validation.
     * 
     * @return void
     */
    public function test_post_input_data_type_validation()
    {
        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $response = $this->actingAs($user)->post('/post', [
            'title' => 1,
            'title_visibility' => 'data',
            'description' => 1,
            'content' => 1,
            'post_image' => 'data',
            'position' => 'data',
            'section_id' => 'data'
        ]);

        $response->assertSessionHasErrors('title');
        $response->assertSessionHasErrors('title_visibility');
        $response->assertSessionHasErrors('description');
        $response->assertSessionHasErrors('content');
        $response->assertSessionHasErrors('post_image');
        $response->assertSessionHasErrors('position');
        $response->assertSessionHasErrors('section_id');
    }

    /**
     * Test a post can be created without post image and description.
     * 
     * @return void
     */
    public function test_post_without_pi_and_desc()
    {
        $this->withoutExceptionHandling();
        
        $this->createParents();

        $response = $this->post('/post', [
            'title' => 'Első cikkem',
            'title_visibility' => true,
            'description' => '',
            'content' => 'Ez az első cikkem.',
            'post_image' => '',
            'position' => 1,
            'section_id' => 1
        ]);

        $this->assertCount(1, Post::all());

        $this->assertEquals('', Post::first()->post_image);
        $this->assertEquals('', Post::first()->description);
    }

    /**
     * Test uploaded post image validation.
     * 
     * @return void
     */
    public function test_uploaded_pi_validation()
    {
        $wrongExtension = UploadedFile::fake()->create('document.pdf');
        $bigSize = UploadedFile::fake()->image('image.jpg')->size(101);

        $user = User::factory()->create([
            'access_level' => 3
        ]);

        $responseWrongExtension = $this->actingAs($user)->post('/post', [
            'title' => 'Post',
            'title_visibility' => true,
            'description' => 'Leírás',
            'content' => 'Tartalom',
            'post_image' => $wrongExtension,
            'position' => 1,
            'section_id' => 1
        ]);

        $responseBigSize = $this->actingAs($user)->post('/post', [
            'title' => 'Post',
            'title_visibility' => true,
            'description' => 'Leírás',
            'content' => 'Tartalom',
            'post_image' => $bigSize,
            'position' => 1,
            'section_id' => 1
        ]);

        Storage::disk('local')->assertMissing('images/'.$wrongExtension->hashName());
        $responseWrongExtension->assertSessionHasErrors('post_image');

        Storage::disk('local')->assertMissing('images/'.$bigSize->hashName());
        $responseBigSize->assertSessionHasErrors('post_image');
        
        $this->assertCount(0, Post::all());
    }

    /**
     * Test a post can be updated by editor.
     * 
     * @return void
     */
    public function test_a_post_can_be_updated_by_editor()
    {
        //$this->withoutExceptionHandling();
        
        $image = UploadedFile::fake()->image('image.jpg');

        $this->createParents();

        $user1 = User::factory()->create([
            'access_level' => 3
        ]);

        $user2 = User::factory()->create([
            'access_level' => 4
        ]);

        $this->post('/post', [
            'title' => 'Első cikkem',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.',
            'post_image' => $image,
            'position' => 1,
            'section_id' => 1
        ]);

        $post = Post::first();

        $response1 = $this->patch('/post/'.$post->id, [
            'title' => 'Első frissített cikkem',
            'title_visibility' => false,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első frissített cikkem.',
            'post_image' => '',
            'position' => 2,
            'section_id' => 1
        ]);

        $this->assertCount(1, Post::all());

        $this->assertEquals('Első frissített cikkem', Post::first()->title);
        $this->assertEquals(0, Post::first()->title_visibility);
        $this->assertEquals('elso-frissitett-cikkem', Post::first()->slug);
        $this->assertEquals('Az első cikkem.', Post::first()->description);
        $this->assertEquals('Ez az első frissített cikkem.', Post::first()->content);
        $this->assertEquals('images/'.$image->hashName(), Post::first()->post_image);
        $this->assertEquals(2, Post::first()->position);
        $this->assertEquals(1, Post::first()->section_id);

        $newImage = UploadedFile::fake()->image('newImage.jpg');

        $response2 = $this->actingAs($user2)->patch('/post/'.$post->id, [
            'title' => 'Első cikkem második frissítés',
            'title_visibility' => true,
            'description' => '',
            'content' => 'Ez az első cikkem második frissítése.',
            'post_image' => $newImage,
            'position' => 1,
            'section_id' => 1
        ]);

        Storage::disk('local')->assertMissing('images/'.$newImage->hashName());
        $this->assertEquals('Első frissített cikkem', Post::first()->title);
        $response2->assertStatus(403);

        $response3 = $this->actingAs($user1)->patch('/post/'.$post->id, [
            'title' => 'Első cikkem második frissítés',
            'title_visibility' => true,
            'description' => '',
            'content' => 'Ez az első cikkem második frissítése.',
            'post_image' => $newImage,
            'position' => 1,
            'section_id' => 1
        ]);

        Storage::disk('local')->assertExists('images/'.$newImage->hashName());
        $this->assertEquals('Első cikkem második frissítés', Post::first()->title);
        $this->assertEquals(1, Post::first()->title_visibility);
        $this->assertEquals('elso-cikkem-masodik-frissites', Post::first()->slug);
        $this->assertEquals('', Post::first()->description);
        $this->assertEquals('Ez az első cikkem második frissítése.', Post::first()->content);
        $this->assertEquals('images/'.$newImage->hashName(), Post::first()->post_image);
        $this->assertEquals(1, Post::first()->position);
        $this->assertEquals(1, Post::first()->section_id);

        $response1->assertRedirect('/fooldal/szekcio/elso-frissitett-cikkem');
        $response3->assertRedirect('/fooldal/szekcio/elso-cikkem-masodik-frissites');
    }

    /**
     * Test a post can be deleted by an editor.
     * 
     * @return void
     */
    public function test_a_post_can_be_deleted_by_editor()
    {
        //$this->withoutExceptionHandling();
        
        $this->createParents();

        $user1 = User::factory()->create([
            'access_level' => 3
        ]);

        $user2 = User::factory()->create([
            'access_level' => 4
        ]);

        $this->post('/post', [
            'title' => 'Első cikkem',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.',
            'post_image' => '',
            'position' => 1,
            'section_id' => 1
        ]);

        $post = Post::first();

        $response1 = $this->actingAs($user2)->delete('/post/'.$post->id);

        $this->assertCount(1, Post::all());
        $response1->assertStatus(403);

        $response2 = $this->actingAs($user1)->delete('/post/'.$post->id);

        $this->assertCount(0, Post::all());
        $response2->assertRedirect('/fooldal');
    }

    /**
     * Test get next post position.
     * 
     * @return void
     */
    public function test_get_next_post_position()
    {
        $next = Post::getNextPosition();

        $this->assertEquals(1, $next);
    }

    /**
     * Test set next position.
     * 
     * @return void
     */
    public function test_set_next_post_position()
    {
        $this->createParents();

        $this->post('/post', [
            'title' => 'Post',
            'title_visibility' => true,
            'description' => '',
            'content' => 'Tartalom',
            'post_image' => '',
            'position' => 1,
            'section_id' => 1
        ]);
        
        $this->post('/post', [
            'title' => 'Post2',
            'title_visibility' => true,
            'description' => 'Leírás',
            'content' => 'Tartalom2',
            'post_image' => '',
            'position' => Post::getNextPosition(),
            'section_id' => 1
        ]);

        $this->assertEquals(2, Post::find(2)->position);
    }

    /**
     * Test set default title visibility
     * 
     * @return void
     */
    public function test_set_default_post_title_visibility()
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
            'title_visibility' => true,
            'slug' => '',
            'position' => 1,
            'page_id' => 1
        ]);
        
        Post::create([
            'title' => 'Post',
            'slug' => '',
            'description' => '',
            'content' => 'Tartalom',
            'post_image' => '',
            'position' => 1,
            'section_id' => 1
        ]);

        $this->assertEquals(1, Post::first()->title_visibility);
    }

    /**
     * Test set post position if the value of the input data is nul.
     * 
     * @return void
     */
    public function test_set_post_position_if_null()
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
            'title_visibility' => true,
            'slug' => '',
            'position' => 1,
            'page_id' => 1
        ]);
        
        Post::create([
            'title' => 'Post',
            'slug' => '',
            'description' => '',
            'content' => 'Tartalom',
            'post_image' => '',
            'position' => '',
            'section_id' => 1
        ]);

        Post::create([
            'title' => 'Post',
            'slug' => '',
            'description' => '',
            'content' => 'Tartalom',
            'post_image' => '',
            'position' => 3,
            'section_id' => 1
        ]);

        $this->assertEquals(1, Post::first()->position);
        $this->assertEquals(3, Post::find(2)->position);
    }

    /**
     * Test retool positions if the request input position already exists.
     * 
     * @return void
     */
    public function test_retool_post_positions()
    {
        $this->withoutExceptionHandling();
        
        $this->createParents();

        $this->post('/section', [
            'title' => 'Szekció2',
            'title_visibility' => true,
            'position' => Section::getNextPosition(1),
            'page_id' => 1
        ]);

        $this->post('/post', [
            'title' => 'Post',
            'title_visibility' => true,
            'description' => '',
            'content' => 'Tartalom',
            'post_image' => '',
            'position' => 1,
            'section_id' => 1
        ]);

        $this->post('/post', [
            'title' => 'Post2',
            'title_visibility' => true,
            'description' => '',
            'content' => 'Tartalom',
            'post_image' => '',
            'position' => 2,
            'section_id' => 1
        ]);

        $this->post('/post', [
            'title' => 'Post3',
            'title_visibility' => true,
            'description' => '',
            'content' => 'Tartalom',
            'post_image' => '',
            'position' => 3,
            'section_id' => 1
        ]);

        $occupied = Post::where('position', 2)->first();
        $this->assertNotNull($occupied);

        $occupiedItems = Post::where('position', '>=', 2)->get();
        $this->assertCount(2, $occupiedItems);

        $this->post('/post', [
            'title' => 'Post4',
            'title_visibility' => true,
            'description' => '',
            'content' => 'Tartalom',
            'post_image' => '',
            'position' => 2,
            'section_id' => 1
        ]);

        $this->post('/post', [
            'title' => 'Post',
            'title_visibility' => true,
            'description' => '',
            'content' => 'Tartalom',
            'post_image' => '',
            'position' => 1,
            'section_id' => 2
        ]);

        $first = Post::first();
        $third = Post::find(2);
        $forth = Post::find(3);
        $second = Post::find(4);
        $firstAtSecond = Post::find(5);

        $this->assertCount(5, Post::all());
        $this->assertEquals(1, $firstAtSecond->position);
        $this->assertEquals(1, $first->position);
        $this->assertEquals(2, $second->position);
        $this->assertEquals(3, $third->position);
        $this->assertEquals(4, $forth->position);
    }

    /**
     * Test get all posts for a section.
     * 
     * @return void
     */
    public function test_get_all_posts_for_a_section()
    {
        $this->withoutExceptionHandling();
        
        $this->createParents();

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
            'section_id' => 1
        ]);

        $this->post('/post', [
            'title' => 'Post2',
            'title_visibility' => true,
            'description' => '',
            'content' => 'Tartalom',
            'post_image' => '',
            'position' => 2,
            'section_id' => 1
        ]);

        $this->post('/post', [
            'title' => 'Post',
            'title_visibility' => true,
            'description' => '',
            'content' => 'Tartalom',
            'post_image' => '',
            'position' => 1,
            'section_id' => 2
        ]);

        $this->assertCount(2, Section::all());
        $this->assertCount(3, Post::all());

        $posts = Section::find(1)->posts;

        $this->assertEquals(2, $posts->count());
        $this->assertEquals('Post1', $posts->find(1)->title);
        $this->assertEquals('Post2', $posts->find(2)->title);
    }

    /**
     * Test the post title is unique in section while storing.
     *
     * @return void
     */
    public function test_post_title_is_unique_in_section_while_storing()
    {
        $this->withoutExceptionHandling();
    
        $this->createParents();

        $this->post('/post', [
            'title' => 'Első cikkem',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.',
            'post_image' => '',
            'position' => 1,
            'section_id' => 1
        ]);

        $response = $this->post('/post', [
            'title' => 'Első cikkem',
            'title_visibility' => true,
            'description' => 'Az első cikkem címe egyedi.',
            'content' => 'Ez az első cikkem.',
            'post_image' => '',
            'position' => 1,
            'section_id' => 1
        ]);

        $this->assertCount(1, Post::all());
        $response->assertSessionHasErrors('title');
    }

     /**
     * Test the postn title is unique in this section while updating.
     * 
     * @return void
     */
    public function test_post_title_is_unique_while_updating()
    {
        $this->withoutExceptionHandling();

        $this->createParents();

        $this->post('/post', [
            'title' => 'Első cikkem',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.',
            'post_image' => '',
            'position' => 1,
            'section_id' => 1
        ]);

        $post = Post::first();

        $this->post('/post', [
            'title' => 'Poszt2',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.',
            'post_image' => '',
            'position' => 2,
            'section_id' => 1
        ]);

        $response = $this->patch('/post/'.$post->id, [
            'title' => 'Poszt2',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.',
            'post_image' => '',
            'position' => 1,
            'section_id' => 1
        ]);

        $this->assertCount(2, Post::all());
        $this->assertEquals('Első cikkem', Post::first()->title);
        $response->assertSessionHasErrors('title');
    }
}
