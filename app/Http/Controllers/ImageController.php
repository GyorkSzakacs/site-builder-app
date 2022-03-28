<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileUploader\Uploader;
use App\Services\FileUploader\ImageConstraints;

class ImageController extends Controller
{
    /**
     * Store validted uploaded image.
     * 
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $path = '';

        $uploadedImage = $request->file;

        if($uploadedImage != null)
        {
            $uploader = new Uploader($uploadedImage, new ImageConstraints());

            if(!$uploader->validateFile()){
                return response()->json(['error'=> $uploader->getErrorMessage()]);
            }

            $path = $uploader->upload();
        }

        return response()->json(['location' => $path]);
    }
}
