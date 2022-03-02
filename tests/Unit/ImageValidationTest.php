<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Http\UploadedFile;
use App\Services\FileUploader\Uploader;
use App\Services\FileUploader\ImageConstraints;

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
        
        $uploader1 = new Uploader($okExtension, new ImageConstraints());
        $uploader2 = new Uploader($wrongExtension, new ImageConstraints());

        $uploader1->validateExtension();
        $uploader2->validateExtension();

        $this->assertEquals('', $uploader1->getErrorMessage());
        $this->assertEquals(
            'Nem megfelelő kiterjesztésű fájl! Támogatott: jpg jpeg png gif ',
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
        $okSize = UploadedFile::fake()->image('image.jpg')->size(100);
        $wrongSize = UploadedFile::fake()->image('image.jpg')->size(101);
        
        $uploader1 = new Uploader($okSize, new ImageConstraints());
        $uploader2 = new Uploader($wrongSize, new ImageConstraints());

        $uploader1->validateSize();
        $uploader2->validateSize();

        $this->assertEquals('', $uploader1->getErrorMessage());
        $this->assertEquals(
            'Túl nagy a fájl mérete!',
            $uploader2->getErrorMessage()
        );
    }

    /**
     * Test the validation of uploaded file.
     *
     * @return void
     */
    public function test_validation_of_uploaded_file()
    {
        $okExtension = UploadedFile::fake()->image('image.jpg');
        $wrongExtension = UploadedFile::fake()->create('document.pdf');
        $okSize = UploadedFile::fake()->image('image.jpg')->size(100);
        $wrongSize = UploadedFile::fake()->image('image.jpg')->size(101);
        
        $uploader1 = new Uploader($okExtension, new ImageConstraints());
        $uploader2 = new Uploader($wrongExtension, new ImageConstraints());
        $uploader3 = new Uploader($okSize, new ImageConstraints());
        $uploader4 = new Uploader($wrongSize, new ImageConstraints());


        $validated1 = $uploader1->validateFile();
        $validated2 = $uploader2->validateFile();
        $validated3 = $uploader3->validateFile();
        $validated4 = $uploader4->validateFile();

        $this->assertTrue($validated1);
        $this->assertFalse($validated2);
        $this->assertTrue($validated3);
        $this->assertFalse($validated4);
    }
}
