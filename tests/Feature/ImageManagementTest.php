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

    /**
     * Test a big image can't be uploaded.
     *
     * @return void
     */
    public function test_a_big_image_can_not_be_uploaded()
    {
        $this->withoutExceptionHandling();
        
        $image = UploadedFile::fake()->image('image.jpg')->size(101);

        $response = $this->post('/image', [
            'file' => $image
        ]);

        
        Storage::disk('local')->assertMissing('images/'.$image->hashName());

        $response->assertStatus(200)
                    ->assertJson([
                        'error' => 'Túl nagy a fájl mérete!'
                    ]);
    }

    /**
     * Test a pdf file can't be uploaded as an imege.
     *
     * @return void
     */
    public function test_a_pdf_can_not_be_uploaded_as_an_image()
    {
        $this->withoutExceptionHandling();
        
        $image = UploadedFile::fake()->create('document.pdf');

        $response = $this->post('/image', [
            'file' => $image
        ]);

        
        Storage::disk('local')->assertMissing('images/'.$image->hashName());

        $response->assertStatus(200)
                    ->assertJson([
                        'error' => 'Nem megfelelő kiterjesztésű fájl! Támogatott: jpg jpeg png gif '
                    ]);
    }
}
