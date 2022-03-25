<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Services\FileUploader\Uploader;
use App\Services\FileUploader\ImageConstraints;
use App\Services\TitleValidator\TitleValidator;
use App\Traits\BackRedirector;

class PostController extends Controller
{
    use BackRedirector;
    
    /**
     * PostTitleValidator instace.
     * 
     * @var object
     */
    private $validator;

    /**
     * Create a new class instance.
     * 
     * @param TitleValidator $validator
     * @return void
     */
    public function __construct(TitleValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Store post data.
     * 
     * @param PostRequest $request
     * @return void
     */
    public function store(PostRequest $request)
    {
        $this->validator->setValidDataFromRequest($request);
        
        $path = $this->storeImage($this->validator->validData['post_image']);

        if(is_a($path, $this->getClassName()))
        {
            return $path;
        }

        if(!$this->validator->isTitleUniqueForStoring())
        {
            return $this->redirectBackWithError('title', $this->validator->getErrorMessage());
        }

        $newPost = Post::create($this->getOrderedValidData($path));

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
        $this->validator->setValidDataFromRequest($request);
        
        $path = $this->storeImage($this->validator->validData['post_image']);

        if(is_a($path, $this->getClassName())){
            return $path;
        }

        if(!$this->validator->isTitleUniqueForUpdating($post->id))
        {
            return $this->redirectBackWithError('title', $this->validator->getErrorMessage());
        }

        
        if(empty($path))
        {
            $post->update([
                'title' => $this->validator->validData['title'],
                'slug' => '',
                'title_visibility' => $this->validator->validData['title_visibility'],
                'description' => $this->validator->validData['description'],
                'content' => $this->validator->validData['content'],
                'section_id' => $this->validator->validData['section_id'],
                'position' => $this->validator->validData['position']
            ]);
        }
        else
        {
            $post->update($this->getOrderedValidData($path));
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
                return $this->redirectBackWithError('post_image', $uploader->getErrorMessage());
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
     * Get the ordered validated post data with stored image path for storing process.
     * 
     * @param string $path
     * @return array
     */
    protected function getOrderedValidData($path)
    {
        return [
            'title' => $this->validator->validData['title'],
            'slug' => '',
            'title_visibility' => $this->validator->validData['title_visibility'],
            'description' => $this->validator->validData['description'],
            'content' => $this->validator->validData['content'],
            'post_image' => $path,
            'section_id' => $this->validator->validData['section_id'],
            'position' => $this->validator->validData['position']
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
