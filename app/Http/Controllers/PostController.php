<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Services\FileUploader\Uploader;
use App\Services\FileUploader\ImageConstraints;

class PostController extends Controller
{
    /**
     * Store post data.
     * 
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_visibility' => 'boolean',
            'description' => 'nullable|string|max:255',
            'content' => 'required|string',
            'post_image' => 'nullable|file',
            'position' => 'integer',
            'section_id' => 'integer'
        ]);
        
        $path = '';
        $uploadedImage = $validated['post_image'];

        if($uploadedImage != null)
        {
            $uploader = new Uploader($uploadedImage, new ImageConstraints());

            if(!$uploader->validateFile()){
                return back()->withErrors(['post_image' => $uploader->getErrorMessage()])->withInput();
            }

            $path = $uploader->upload();
        }
        
        Post::create([
            'title' => $validated['title'],
            'slug' => '',
            'title_visibility' => $validated['title_visibility'],
            'description' => $validated['description'],
            'content' => $validated['content'],
            'post_image' => $path,
            'position' => $validated['position'],
            'section_id' => $validated['section_id']
        ]);
    }

    /**
     * Update the selected post data.
     * 
     * @param Request $request
     * @param Post $post
     * @return void
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_visibility' => 'boolean',
            'description' => 'nullable|string|max:255',
            'content' => 'required|string',
            'post_image' => 'nullable|file',
            'position' => 'integer',
            'section_id' => 'integer'
        ]);
        
        $path = '';

        $uploadedImage = $validated['post_image'];

        if($uploadedImage != null)
        {
            $uploader = new Uploader($uploadedImage, new ImageConstraints());

            if(!$uploader->validateFile()){
                return back()->withErrors(['post_image' => $uploader->getErrorMessage()])->withInput();
            }

            $path = $uploader->upload();
        }
        
        if(empty($path))
        {
            $post->update([
                'title' => $validated['title'],
                'slug' => '',
                'title_visibility' => $validated['title_visibility'],
                'description' => $validated['description'],
                'content' => $validated['content'],
                'position' => $validated['position'],
                'section_id' => $validated['section_id']
            ]);
        }
        else
        {
            $post->update([
                'title' => $validated['title'],
                'slug' => '',
                'title_visibility' => $validated['title_visibility'],
                'description' => $validated['description'],
                'content' => $validated['content'],
                'post_image' => $path,
                'position' => $validated['position'],
                'section_id' => $validated['section_id']
            ]);
        }
    }

    /**
     * Delete the selected post.
     * 
     * @param Post $post
     * @return void
     */
    public function destroy(Post $post)
    {
        $post->delete();
    }
}
