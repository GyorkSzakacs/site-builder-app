<?php

namespace App\Services;

interface FileUploadService
{
    public function validateExtension($extension);
}