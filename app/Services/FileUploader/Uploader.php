<?php

namespace App\Services\FileUploader;

Class Uploader
{
    public static $acceptedExtensions = [ 'jpg', 'png', 'gif'];

    /**
     * Extension of uploaded file.
     * 
     * @var string
     */
    private $extension;

    public function __construct($file)
    {
        $this->setExtension($file);
    }
    
    /**
     * Validate file extension.
     * 
     * @return boolean
     */
    public function validateExtension()
    {
       if(in_array($this->extension, self::$acceptedExtensions)){
            return true;
        }
        
        return false;
    }

    /**
     * Set extension.
     * 
     * @param string $extension
     * 
     * @return void
     */
    private function setExtension($file)
    {
        $this->extension = $file->getClientOriginalExtension();
    }
}