<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\Category;
use App\Http\Requests\PageRequest;
use App\Services\TitleValidator\TitleValidator;
use App\Traits\BackRedirector;
use Illuminate\Support\Facades\Gate;

class PageController extends Controller
{
    use BackRedirector;

    /**
     * PageTitleValidator instace.
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
     * Render the root view.
     * 
     * @return View
     */
    public function index()
    {
        if(Gate::denies('first-register'))
        {
            $firstCategory = Category::orderBy('position', 'asc')
                                        ->first();

            $firstPage = $firstCategory->pages
                                        ->sortBy('position')
                                        ->first();

            return View('page.index', ['page' => $firstPage]);
        }

        return View('auth.first-register');
    }

    /**
     * Render the index view with the selected page.
     * 
     * @param string $slug
     * @return View
     */
    public function show(string $slug)
    {
       $page = Page::where('slug', $slug)
                        ->first();
        
        return View('page.index', ['page' => $page]);
    }


    /**
     * Get the screen with the form for creation of a page.
     * 
     * @return View
     */
    public function create()
    {
        $this->authorize('create', Page::class);

        $categories = Category::all();

        return view('page.create', ['categories' => $categories]);
    }

    /**
     * Create new page
     * 
     * @param PageRequiest $request
     * @return void
     */
    public function store(PageRequest $request)
    {
        $this->authorize('create', Page::class);
        
        $this->validator->setValidDataFromRequest($request);

        if(!$this->validator->isTitleUniqueForStoring())
        {
            return $this->redirectBackWithError('title', $this->validator->getErrorMessage());
        }

        Page::create($this->getOrderedValidData());

        return redirect('/dashboard');
    }

    /**
     * Get the screen with the form for update of a page.
     * 
     * @param Request $request
     * @param Page $page
     * @return View
     */
    public function edit(Request $request, Page $page)
    {
        $this->authorize('update', $page);
        
        $categories = Category::all();

        $max = Page::getNextPosition($page->category_id) - 1;

        return view('page.update', ['page' => $page, 'categories' => $categories, 'max' => $max]);
    }

    /**
     * Update the selected page details
     * 
     * @param PageRequest $request
     * @param Page $page
     * @return void
     */
    public function update(PageRequest $request, Page $page)
    {
        $this->authorize('update', $page);
        
        $this->validator->setValidDataFromRequest($request);

        if(!$this->validator->isTitleUniqueForUpdating($page->id))
        {
            return $this->redirectBackWithError('title', $this->validator->getErrorMessage());
        }

        $this->setPositionToUpdate($page->id);

        $page->update($this->getOrderedValidData());

        return redirect('/dashboard');
    }

    /**
     * Delete the selected page.
     * 
     * @param Reqest $request
     * @param Page $page
     * @return void
     */
    public function destroy(Request $request, Page $page)
    {
        $this->authorize('delete', $page);
        
        $page->delete();

        return redirect('/dashboard');
    }

    /**
     * Get ordered valid data .
     * 
     * @return array
     */
    protected function getOrderedValidData()
    {
        return [
            'title' => $this->validator->validData['title'],
            'slug' => '',
            'title_visibility' => $this->validator->validData['title_visibility'],
            'category_id' => isset($this->validator->validData['category_id']) ? $this->validator->validData['category_id'] : '',
            'position' => isset($this->validator->validData['position']) ? $this->validator->validData['position'] : ''
        ];
    }

    /**
     * Set position to update a page.
     * 
     * @param int $id
     * @return void
     */
    protected function setPositionToUpdate(int $id)
    {
        $oldCategory = Page::find($id)->category_id;
        $newCategory = $this->validator->validData['category_id'];

        if($oldCategory != $newCategory)
        {
            $this->validator->validData['position'] = Page::getNextPosition($newCategory);
        }

        return;
    }
}
