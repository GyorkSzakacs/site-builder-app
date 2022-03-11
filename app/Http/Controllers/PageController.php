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
        Page::create($this->getValidData($request));

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
        $page->update($this->getValidData($request));

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
}
