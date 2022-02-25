<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Http\UploadedFile;
use App\Services\FileUploader\Uploader;

class ImageValidationTest extends TestCase
{
    /**
     * Test the validation of extencion.
     *
     * @return void
     */
    public function test_validation_of_extension()
    {
        $okExtension = UploadedFile::fake()->image('image.jpg');
        $wrongExtension = UploadedFile::fake()->create('document.pdf');
        
        $uploader1 = new Uploader($okExtension);
        $uploader2 = new Uploader($wrongExtension);

        $this->assertTrue($uploader1->validateExtension());
        $this->assertFalse($uploader2->validateExtension());
    }

    /**
     * Test the validation of size.
     *
     * @return void
     */
    public function test_validation_of_sie()
    {
        $okSize = UploadedFile::fake()->image('image.jpg')->size(95);
        $wrongSize = UploadedFile::fake()->image('image.jpg')->size(101);
        
        $uploader1 = new Uploader($okSize);
        $uploader2 = new Uploader($wrongSize);

        $this->assertTrue($uploader1->validateSize());
        $this->assertFalse($uploader2->validateSize());
    }
}
