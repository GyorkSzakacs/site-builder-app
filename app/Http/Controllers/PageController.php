<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Http\Requests\PageRequest;
use App\Services\TitleValidator\TitleValidator;
use App\Traits\BackRedirector;

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
     * Create new page
     * 
     * @param PageRequiest $request
     * @return void
     */
    public function store(PageRequest $request)
    {
        if(!$request->user()->hasManagerAccess())
        {
            return abort(403);
        }
        
        $this->validator->setValidDataFromRequest($request);

        if(!$this->validator->isTitleUniqueForStoring())
        {
            return $this->redirectBackWithError('title', $this->validator->getErrorMessage());
        }

        Page::create($this->getOrderedValidData());

        return redirect('/dashboard');
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
        $this->validator->setValidDataFromRequest($request);

        if(!$this->validator->isTitleUniqueForUpdating($page->id))
        {
            return $this->redirectBackWithError('title', $this->validator->getErrorMessage());
        }

        $page->update($this->getOrderedValidData());

        return redirect('/dashboard');
    }

    /**
     * Delete the selected page.
     * 
     * @param Page $page
     * @return void
     */
    public function destroy(Page $page)
    {
        $page->delete();

        return redirect('/dashboard');
    }

    /**
     * Get ordered valid data for storing process.
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
            'position' => $this->validator->validData['position']
        ];
    }
}
