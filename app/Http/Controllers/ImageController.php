<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileUploader\Uploader;
use App\Services\FileUploader\ImageConstraints;
use Illuminate\Support\Facades\Storage;;
use App\Models\Post;
use App\Traits\BackRedirector;

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
        if(!$request->user()->hasEditorAccess())
        {
            return abort(403);
        }

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
     * @param Request $request
     * @param string $image
     * @return void
     */
    public function dowload(Request $request, $image)
    {
        if(!$request->user()->hasEditorAccess())
        {
            return abort(403);
        }

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
        if(!$request->user()->hasEditorAccess())
        {
            return abort(403);
        }

        $posts = Post::where('content', 'LIKE', '%'.$image.'%')->get();

        if($posts->count() > 0)
        {
            return $this->redirectBackWithError('delete', 'A kiválasztot kép nem törölhető!');
        }

        Storage::disk('local')->delete('images/'.$image);

        return redirect('/galery');
    }
}
