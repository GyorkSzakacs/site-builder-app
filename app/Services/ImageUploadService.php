<?php

namespace App\Services;

use App\Services\FileUploadService;

Class ImageUploadService implements FileUploadService
{
    public static $acceptedExtensions = [ 'jpg', 'png', 'gif'];
    
    public function validateExtension($extension)
    {
       if(in_array($extension, self::$acceptedExtensions)){
            return true;
        }
        
        return false;
    }
}