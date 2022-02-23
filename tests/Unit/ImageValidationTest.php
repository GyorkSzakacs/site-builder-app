<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ImageUploadService;

class ImageValidationTest extends TestCase
{
    /**
     * Test the validation of extencion.
     *
     * @return void
     */
    public function test_validation_of_extension()
    {
        $upload = new ImageUploadService;
        $okExtension = 'jpg';
        $wrongExtension = 'docx';
        
        $this->assertTrue($upload->validateExtension($okExtension));
        $this->assertFalse($upload->validateExtension($wrongExtension));
    }
}
