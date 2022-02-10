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
        $valid_data = $request->validated();

        Page::create($valid_data);

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
        $valid_data = $request->validated();
        
        $page->update($valid_data);

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
}
