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

        $uploader1->validateExtension();
        $uploader2->validateExtension();

        $this->assertEquals('', $uploader1->getErrorMessage());
        $this->assertEquals(
            'Nem megfelelő kiterjesztésű fájl! Támogatott fájlkiterjesztések: jpg, jpeg, png, gif',
            $uploader2->getErrorMessage()
        );
    }

    /**
     * Test the validation of size.
     *
     * @return void
     */
    public function test_validation_of_sie()
    {
        $okSize = UploadedFile::fake()->image('image.jpg')->size(97);
        $wrongSize = UploadedFile::fake()->image('image.jpg')->size(101);
        
        $uploader1 = new Uploader($okSize);
        $uploader2 = new Uploader($wrongSize);

        $uploader1->validateSize();
        $uploader2->validateSize();

        $this->assertEquals('', $uploader1->getErrorMessage());
        $this->assertEquals(
            'Túl nagy a fájl mérete!',
            $uploader2->getErrorMessage()
        );
    }
}
