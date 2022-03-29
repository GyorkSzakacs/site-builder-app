<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileUploader\Uploader;
use App\Services\FileUploader\ImageConstraints;
use Illuminate\Support\Facades\Storage;;

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

    /**
     * Dowload the selected image.
     * 
     * @param string $image
     * @return void
     */
    public function dowload($image)
    {
        return Storage::download('images/'.$image); 
    }

    /**
     * Delete the selected image.
     * 
     * @param string $image
     * @return void
     */
    public function destroy($image)
    {
        Storage::disk('local')->delete('images/'.$image);

        return redirect('/galery');
    }
}
