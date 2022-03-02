<?php

namespace App\Services\FileUploader;

interface FileConstraints
{
    public function getAcceptedExtensions();
    public function getMaxSize();
    public function getDirectory();
}