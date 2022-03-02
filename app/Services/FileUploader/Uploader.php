<?php

namespace App\Services\FileUploader;

Class Uploader
{
    /**
     * The accepted extensions for uploaded file.
     * 
     * @var array
     */
    private $acceptedExtensions = [];

    /**
     * The maximum size for valid uploaded file.
     * 
     * @var int
     */
    private $maxSize;

    /**
     * The name of the directory to where uploaded file should be moved.
     * 
     * @var string
     */
    private $directory;

    /**
     * Uploaded file.
     * 
     * @var object
     */
    private $file;

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

    public function __construct($file, FileConstraints $fileConstraints)
    {
        $this->file = $file;
        $this->setExtension();
        $this->setSize();

        $this->acceptedExtensions = $fileConstraints->getAcceptedExtensions();
        $this->maxSize = $fileConstraints->getMaxSize();
        $this->directory = $fileConstraints->getDirectory();
    }

    /**
     * Store uploaded file.
     * 
     * @return string $path
     */
    public function upload()
    {
        $path = $this->file->store($this->directory);

        return $path;
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
       if(!in_array($this->extension, $this->acceptedExtensions)){
            array_push(
                $this->errorMessage,
                'Nem megfelelő kiterjesztésű fájl! Támogatott: '.$this->getExtensionsString()
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
       if($this->size > $this->maxSize){
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
    private function setExtension()
    {
        $this->extension = $this->file->getClientOriginalExtension();
    }

    /**
     * Convert accepted extensions to string.
     * 
     * @return string $extensions
     */
    public function getExtensionsString()
    {
        $extensions = '';

        foreach($this->acceptedExtensions as $acceptedExtension){
            $extensions .= $acceptedExtension.' ';
        }

        return $extensions;
    }

    /**
     * Set size.
     * 
     * @param object $file
     * 
     * @return void
     */
    private function setSize()
    {
        $this->size = $this->file->getSize();
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