<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Http\Requests\PageRequest;
use App\Services\TitleValidator\TitleValidator;

class PageController extends Controller
{
    /**
     * Create new page
     * 
     * @param PageRequiest $request
     * @param TitleValidator $validator
     * @return void
     */
    public function store(PageRequest $request, TitleValidator $validator)
    {
        $validData = $validator->getValidDataFromRequest($request);

        if(!$validator->isTitleUniqueForStoring($validData['title']))
        {
            return $this->redirectBackWithTitleError($validator->getErrorMessage());
        }

        Page::create($validData);

        return redirect('/dashboard');
    }

    /**
     * Update the selected page details
     * 
     * @param PageRequest $request
     * @param TitleValidator $validator
     * @param Page $page
     * @return void
     */
    public function update(PageRequest $request, TitleValidator $validator, Page $page)
    {
        $validData = $validator->getValidDataFromRequest($request);

        if(!$validator->isTitleUniqueForUpdating($validData['title'], $page->id))
        {
            return $this->redirectBackWithTitleError($validator->getErrorMessage());
        }

        $page->update($validData);

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
     * Redirect back with error for title.
     * 
     * @param string $errorMessage
     * @return void
     */
    protected function redirectBackWithTitleError($errorMessage)
    {
        return back()->withErrors(['title' => $errorMessage])->withInput();
    }
}
