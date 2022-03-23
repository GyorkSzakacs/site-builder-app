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

        if(is_a($path, $this->getClassName()))
        {
            return $path;
        }

        $validData = $this->getValidData($validated, $path);
        
        if(!$this->isTitleUniqueInSectionForStoring($validData['title'], $validData['section_id']))
        {
            return $this->redirectBackWithTitleError();
        }

        $newPost = Post::create($validData);

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

        $validData = $this->getValidData($validated, $path);
        
        if(!$this->isTitleUniqueInSectionForUpdating($validData['title'], $validData['section_id'], $post->id))
        {
            return $this->redirectBackWithTitleError();
        }

        
        if(empty($path))
        {
            $post->update([
                'title' => $validData['title'],
                'slug' => '',
                'title_visibility' => $validData['title_visibility'],
                'description' => $validData['description'],
                'content' => $validData['content'],
                'section_id' => $validData['section_id'],
                'position' => $validData['position']
            ]);
        }
        else
        {
            $post->update($validData);
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
     * Check the title is unique in this section while storing.
     * 
     * @param string $title
     * @param int $section_id
     * @return boolean
     */
    protected function isTitleUniqueInSectionForStoring($title, $section_id)
    {
        $titleInSection = Post::where([
                                        ['title', $title],
                                        ['section_id', $section_id]
                                    ])->get();

        if($titleInSection->count() > 0 )
        {
            return false;
        }

        return true;
    }

    /**
     * Check the title is unique in this section while updating.
     * 
     * @param string $title
     * @param int $section_id
     * @param int $id
     * @return boolean
     */
    protected function isTitleUniqueInSectionForUpdating($title, $section_id,  $id)
    {
        $titleInSection = Post::where([
                                        ['id', '<>', $id],
                                        ['title', $title],
                                        ['section_id', $section_id]
                                    ])->get();

        if($titleInSection->count() > 0 )
        {
            return false;
        }

        return true;
    }

    /**
     * Redirect back with error for title.
     * 
     * @return void
     */
    protected function redirectBackWithTitleError()
    {
        return back()->withErrors(['title' => 'Ezzel a címmel már létezik poszt ebben a szekcióban!'])->withInput();
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
