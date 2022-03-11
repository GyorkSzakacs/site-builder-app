<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Services\FileUploader\Uploader;
use App\Services\FileUploader\ImageConstraints;

class PostController extends Controller
{
    /**
     * Store post data.
     * 
     * @param PostRequest $request
     * @return void
     */
    public function store(PostRequest $request)
    {
        $validated = $request->validated();
        
        $path = $this->storeImage($validated['post_image']);

        if(is_a($path, $this->getClassName())){
            return $path;
        }
        
        $newPost = Post::create($this->getValidData($validated, $path));

        return $this->redirectToPost($newPost);
    }

    /**
     * Update the selected post data.
     * 
     * @param PostRequest $request
     * @param Post $post
     * @return void
     */
    public function update(PostRequest $request, Post $post)
    {
        $validated = $request->validated();
        
        $path = $this->storeImage($validated['post_image']);

        if(is_a($path, $this->getClassName())){
            return $path;
        }
        
        if(empty($path))
        {
            $post->update([
                'title' => $validated['title'],
                'slug' => '',
                'title_visibility' => $validated['title_visibility'],
                'description' => $validated['description'],
                'content' => $validated['content'],
                'section_id' => $validated['section_id'],
                'position' => $validated['position']
            ]);
        }
        else
        {
            $post->update($this->getValidData($validated, $path));
        }

        return $this->redirectToPost($post);
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

        return redirect('/'.$post->section->page->slug);
    }

    /**
     * Validate and store uploaded image.
     * 
     * @param object $uploadedImage
     * @return string $path
     */
    protected function storeImage($uploadedImage)
    {
        $path = '';

        if($uploadedImage != null)
        {
            $uploader = new Uploader($uploadedImage, new ImageConstraints());

            if(!$uploader->validateFile()){
                return back()->withErrors(['post_image' => $uploader->getErrorMessage()])->withInput();
            }

            $path = $uploader->upload();
        }

        return $path;
    }

    /**
     * Get the class name of the required response ofject if the uploaded image is invalid.
     * 
     * @return string
     */
    protected function getClassName()
    {
        return 'Illuminate\Http\RedirectResponse';
    }

    /**
     * Get the validated post data to store.
     * 
     * @param object $validated
     * @param string $path
     * @return array
     */
    protected function getValidData($validated, $path)
    {
        return [
            'title' => $validated['title'],
            'slug' => '',
            'title_visibility' => $validated['title_visibility'],
            'description' => $validated['description'],
            'content' => $validated['content'],
            'post_image' => $path,
            'section_id' => $validated['section_id'],
            'position' => $validated['position']
        ];
    }

    /**
     * Redirect to current post.
     * 
     * @param Post $post
     * @return RedirectResponse
     */
    protected function redirectToPost(Post $post)
    {
        $postSection = $post->section;
        $postSectionSlug = $postSection->slug;
        $postPageSlug = $postSection->page->slug;

        return redirect('/'.$postPageSlug.'/'.$postSectionSlug.'/'.$post->slug);
    }
}
