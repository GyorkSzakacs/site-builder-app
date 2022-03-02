<?php

namespace Tests\Unit;

use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\FileUploader\Uploader;

class ImageUploadTest extends TestCase
{
    /**
     * Test an image can be uploaded.
     *
     * @return void
     */
    public function test_an_image_can_be_uploaded()
    {
        $image = UploadedFile::fake()->image('image.jpg');

        $uploader = new Uploader($image);

        $path = $uploader->upload();
        
        Storage::disk('local')->assertExists('images/'.$image->hashName());
        
        $imagePath = 'images/'.$image->hashName();
        $this->assertEquals($imagePath, $path);
    }
}
