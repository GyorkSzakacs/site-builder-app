<?php

namespace App\Services\FileUploader;

Class Uploader
{
    public static $acceptedExtensions = [ 'jpg', 'png', 'gif'];

    public static $maxSize = 100000;

    /**
     * Extension of uploaded file.
     * 
     * @var string
     */
    private $extension;

    /**
     * Size of uploaded file.
     * 
     * @var int
     */

    public function __construct($file)
    {
        $this->setExtension($file);
        $this->setSize($file);
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
     * Validate file size.
     * 
     * @return boolean
     */
    public function validateSize()
    {
       if($this->size < self::$maxSize){
            return true;
        }
        
        return false;
    }

    /**
     * Set extension.
     * 
     * @param object $file
     * 
     * @return void
     */
    private function setExtension($file)
    {
        $this->extension = $file->getClientOriginalExtension();
    }

    /**
     * Set size.
     * 
     * @param object $file
     * 
     * @return void
     */
    private function setSize($file)
    {
        $this->size = $file->getSize();
    }
}