<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

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

    /**
     * Test an image can be downloaded.
     *
     * @return void
     */
    public function test_an_image_can_be_dowloaded()
    {
        $this->withoutExceptionHandling();
        
        $image = UploadedFile::fake()->image('image.jpg');

        $this->post('/image', [
            'file' => $image
        ]);

        $path = $image->hashName();

        $response = $this->post('/image/'.$path);

        $response->assertDownload($path);
    }

    /**
     * Test an image can be deleted.
     *
     * @return void
     */
    public function test_an_image_can_be_deleted()
    {
        $this->withoutExceptionHandling();
        
        $image = UploadedFile::fake()->image('image.jpg');

        $this->post('/image', [
            'file' => $image
        ]);

        $path = $image->hashName();

        $response = $this->delete('/image/'.$path);

        Storage::disk('local')->assertMissing('images/'.$image->hashName());
        $response->assertRedirect('/galery');
    }

    /**
     * Test that an image can't be deleted if it is contained by a post content.
     * 
     * @return void
     */
    public function test_an_image_can_not_be_deleted()
    {
        $this->withoutExceptionHandling();
        
        $image = UploadedFile::fake()->image('image.jpg');

        $this->post('/image', [
            'file' => $image
        ]);

        $path = $image->hashName();

        $user = User::factory()->create([
            'access_level' => 2
        ]);

        $this->actingAs($user)->post('/page', [
            'title' => 'Főoldal',
            'title_visibility' => true,
            'position' => 1,
            'category_id' => 1
        ]);

        $this->post('/section', [
            'title' => 'Szekció',
            'title_visibility' => true,
            'position' => 1,
            'page_id' => 1
        ]);

        $this->post('/post', [
            'title' => 'Első cikkem',
            'title_visibility' => true,
            'description' => 'Az első cikkem.',
            'content' => 'Ez az első cikkem.<img src ="/images/'.$path.'" width="200" alt="kép">',
            'post_image' => '',
            'position' => 1,
            'section_id' => 1
        ]);
    
        $response = $this->from('/image/image.jpg')->delete('/image/'.$path);

        Storage::disk('local')->assertExists('images/'.$image->hashName());
        $response->assertRedirect('/image/image.jpg');
        $response->assertSessionHasErrors('delete');
    }
}
