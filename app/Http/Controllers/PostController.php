<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\Page;
use App\Models\Section;
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
     * Render show a selected post view.
     * 
     * @param string $pageSlug
     * @param string $sectionSlug
     * @param string $postSlug
     * @return View
     */
    public function show(string $pageSlug, string $sectionSlug, string $postSlug)
    {
        $page = Page::where('slug', $pageSlug)->first();

        $section = $page->sections->where('slug', $sectionSlug)->first();

        $post = $section->posts->where('slug', $postSlug)->first();

        return View('post.show', ['post' => $post]);
    }

    /**
     * Render the new post for a page screen.
     * 
     * @param int $id
     * @return View
     */
    public function create(int $id)
    {
        $this->authorize('create', Post::class);
        
        $next = Post::getNextPosition($id);

        return View('post.create', ['sectionId' => $id, 'next' => $next]);
    }

    /**
     * Store post data.
     * 
     * @param PostRequest $request
     * @return void
     */
    public function store(PostRequest $request)
    {
        $this->authorize('create', Post::class);
        
        $this->validator->setValidDataFromRequest($request);
        
        $path = $this->storeImage(isset($this->validator->validData['post_image']) ?  $this->validator->validData['post_image'] : null);

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
     * Render the edit post view.
     * 
     * @param Post $post
     * @return View
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        
        $max = Post::getNextPosition($post->section_id) - 1;

        return View('post.update', ['post' => $post, 'max' => $max]);
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
        $this->authorize('update', $post);
        
        $this->validator->setValidDataFromRequest($request);
        
        $path = $this->storeImage(isset($this->validator->validData['post_image']) ?  $this->validator->validData['post_image'] : null);
        
        if(is_a($path, $this->getClassName())){
            return $path;
        }

        if(!$this->validator->isTitleUniqueForUpdating($post->id))
        {
            return $this->redirectBackWithError('title', $this->validator->getErrorMessage());
        }

        $orderedData = $this->getOrderedValidData($path);
        
        if(empty($path))
        {
            unset($orderedData['post_image']);

            $post->update($orderedData);
        }
        else
        {
            $post->update($orderedData);
        }

        return $this->redirectToPost($post);
    }

    /**
     * Delete the selected post.
     * 
     * @param Request $request
     * @param Post $post
     * @return void
     */
    public function destroy(Request $request, Post $post)
    {
        $this->authorize('delete', $post);
        
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
