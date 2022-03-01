<?php

namespace App\Services\FileUploader;

Class Uploader
{
    public static $acceptedExtensions = [ 'jpg', 'jpeg', 'png', 'gif'];

    public static $maxSize = 102400;

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
    private $size;

    /**
     * Validation error message.
     * 
     * @var array
     */
    private $errorMessage = [];

    public function __construct($file)
    {
        $this->setExtension($file);
        $this->setSize($file);
    }

    /**
     * Validate the uploaded file.
     * 
     * @return boolean
     */
    public function validateFile()
    {
        $this->validateExtension();

        $this->validateSize();

        $validationErrors = $this->getErrorMessage();

        if(empty($validationErrors)){
            return true;
        }

        return false;
    }
    
    /**
     * Validate file extension.
     * 
     * @return boolean
     */
    public function validateExtension()
    {
       if(!in_array($this->extension, self::$acceptedExtensions)){
            array_push(
                $this->errorMessage,
                'Nem megfelelő kiterjesztésű fájl! Támogatott fájlkiterjesztések: jpg, jpeg, png, gif'    
            );
        }
        
        return $this;
    }

    /**
     * Validate file size.
     * 
     * @return boolean
     */
    public function validateSize()
    {
       if($this->size > self::$maxSize){
            array_push(
                $this->errorMessage,
                'Túl nagy a fájl mérete!'    
            );
        }
        
        return $this;
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

    /**
     * Get validation error message.
     * 
     * @return string
     */
    public function getErrorMessage()
    {
        if(!empty($this->errorMessage)){
            return $this->errorMessage[0];
        }

        return '';
    }
}