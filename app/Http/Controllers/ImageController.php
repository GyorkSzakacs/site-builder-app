<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileUploader\Uploader;
use App\Services\FileUploader\ImageConstraints;
use Illuminate\Support\Facades\Storage;;
use App\Models\Post;
use App\Traits\BackRedirector;
use Illuminate\Support\Facades\Gate;

class ImageController extends Controller
{
    use BackRedirector;

    /**
     * Store validted uploaded image.
     * 
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        Gate::authorize('image-upload');
        
        $path = '';

        $uploadedImage = $request->file;

        if($uploadedImage != null)
        {
            $uploader = new Uploader($uploadedImage, new ImageConstraints());

            if(!$uploader->validateFile()){
                return response()->json(['error'=> $uploader->getErrorMessage()]);
            }

            $path = asset($uploader->upload());
        }

        return response()->json(['location' => $path]);
    }

    /**
     * Download the selected image.
     * 
     * @param Request $request
     * @param string $image
     * @return void
     */
    public function download(Request $request, $image)
    {
        Gate::authorize('image-download');

        return Storage::download('images/'.$image); 
    }

    /**
     * Delete the selected image.
     * 
     * @param Request $request
     * @param string $image
     * @return void
     */
    public function destroy(Request $request, $image)
    {
        Gate::authorize('image-delete');
        
        $posts = Post::where('content', 'LIKE', '%'.$image.'%')->get();

        if($posts->count() > 0)
        {
            return $this->redirectBackWithError('delete', 'A kiválasztot kép nem törölhető!');
        }

        Storage::disk('local')->delete('images/'.$image);

        return redirect('/galery');
    }
}
