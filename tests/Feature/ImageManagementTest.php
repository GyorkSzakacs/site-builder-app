<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageManagementTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test an image can be uploaded.
     *
     * @return void
     */
    public function test_an_image_can_be_uploaded()
    {
        $this->withoutExceptionHandling();
        
        $image = UploadedFile::fake()->image('image.jpg');

        $response = $this->post('/image', [
            'file' => $image
        ]);

        
        Storage::disk('local')->assertExists('images/'.$image->hashName());

        $response->assertStatus(200)
                    ->assertJson([
                        'location' => 'images/'.$image->hashName()
                    ]);
    }
}
