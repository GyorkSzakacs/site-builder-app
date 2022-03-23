<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Http\Requests\PageRequest;

class PageController extends Controller
{
    /**
     * Create new page
     * 
     * @param PageRequiest $request
     * @return void
     */
    public function store(PageRequest $request)
    {
        $validData = $this->getValidData($request);

        if(!$this->isPageTitleUniqueForStoring($validData['title']))
        {
            return $this->redirectBackWithTitleError();
        }

        Page::create($validData);

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
        $validData = $this->getValidData($request);

        if(!$this->isPageTitleUniqueForUpdating($validData['title'], $page->id))
        {
            return $this->redirectBackWithTitleError();
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
     * Get valid input data.
     * 
     * @param PageRequest $request
     * @return array
     */
    protected function getValidData(PageRequest $request)
    {
        $validated = $request->validated();

        return [
            'title' => $validated['title'],
            'slug' => '',
            'title_visibility' => $validated['title_visibility'],
            'category_id' => isset($validated['category_id']) ? $validated['category_id'] : '',
            'position' => $validated['position']
        ];
    }

    /**
     * Check that given title is unique for storing.
     * 
     * @param string $title
     * @return boolean
     */
    protected function isPageTitleUniqueForStoring($title)
    {
        $sameTitle = Page::Where('title', $title)->get();

        if($sameTitle->count() > 0)
        {
            return false;
        }

        return true;
    }

    /**
     * Check that given title is unique for storing.
     * 
     * @param string $title
     * @param int $id
     * @return boolean
     */
    protected function isPageTitleUniqueForUpdating($title, $id)
    {
        $sameTitle = Page::Where([
                                    ['id', '<>', $id],
                                    ['title', $title]
                                ])->get();

        if($sameTitle->count() > 0)
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
        return back()->withErrors(['title' => 'Ezzel a címmel már létezik oldal!'])->withInput();
    }
}
