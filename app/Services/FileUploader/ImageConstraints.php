<?php

namespace App\Services\FileUploader;

use App\Services\FileUploader\FileConstraints;

class ImageConstraints implements FileConstraints
{
    /**
     * Get the accepted extensions for uploaded image file.
     * 
     * @return array
     */
    public function getAcceptedExtensions()
    {
        return ['jpg', 'jpeg', 'png', 'gif'];
    }

    /**
     * Get maximum file size for the uploaded image.
     * 
     * @return int
     */
    public function getMaxSize()
    {

    }

    /**
     * Get directory for uploaded images.
     * 
     * @return string
     */
    public function getDirectory()
    {
        return 'images';
    }
}