<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
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
        
        $response = $this->post('/post', [
            'title' => 'Első cikkem',
            'slug' => '',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.',
            'post_image' => 'post.jpg',
            'position' => 1,
            'section_id' => 1
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, Post::all());
    }

    /**
     * Upload test.
     * 
     * @return void
     */
    public function test_a_post_image_can_be_uploaded()
    {
        Storage::fake('images');

        $this->assertTrue(true);
    }
}
