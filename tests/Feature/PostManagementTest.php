<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;

class PostManagementTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test a post can be created.
     *
     * @return void
     */
    public function test_a_post_can_be_created()
    {
        $this->withoutExceptionHandling();
        
        $image = UploadedFile::fake()->image('image.jpg');

        $response = $this->post('/post', [
            'title' => 'Első cikkem',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.',
            'post_image' => $image,
            'position' => 1,
            'section_id' => 1
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, Post::all());

        Storage::disk('local')->assertExists('images/'.$image->hashName());
        $this->assertEquals('images/'.$image->hashName(), Post::first()->post_image);

        $this->assertEquals('elso-cikkem', Post::first()->slug);
    }

    /**
     * Test input data requirement validation.
     * 
     * @return void
     */
    public function test_post_input_data_requirement_validation()
    {
        $response = $this->post('/post', [
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
        $response = $this->post('/post', [
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
        
        $response = $this->post('/post', [
            'title' => 'Első cikkem',
            'title_visibility' => true,
            'description' => '',
            'content' => 'Ez az első cikkem.',
            'post_image' => '',
            'position' => 1,
            'section_id' => 1
        ]);

        $response->assertStatus(200);
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

        $responseWrongExtension = $this->post('/post', [
            'title' => 'Post',
            'title_visibility' => true,
            'description' => 'Leírás',
            'content' => 'Tartalom',
            'post_image' => $wrongExtension,
            'position' => 1,
            'section_id' => 1
        ]);

        $responseBigSize = $this->post('/post', [
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
     * Test a post can be updated.
     * 
     * @return void
     */
    public function test_a_post_can_be_updated()
    {
        $this->withoutExceptionHandling();
        
        $image = UploadedFile::fake()->image('image.jpg');

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

        $this->patch('/post/'.$post->id, [
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

        $this->patch('/post/'.$post->id, [
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

    }
}
