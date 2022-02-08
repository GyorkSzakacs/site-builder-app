<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends Controller
{
    /**
     * Create new page
     * 
     * @param Requiest $request
     * @return void
     */
    public function store(Request $request)
    {
        Page::create([
            'tittle' => $request->tittle,
            'slug' => '',
            'tittle_visibility' => $request->tittle_visibility,
            'position' => $request->position,
            'category_id' => $request->category_id
        ]);
    }

    /**
     * Update the selected page details
     * 
     * @param Request $request
     * @param Page $page
     * @return void
     */
    public function update(Request $request, Page $page)
    {
        $page->update([
            'tittle' => $request->tittle,
            'slug' => '',
            'tittle_visibility' => $request->tittle_visibility,
            'position' => $request->position,
            'category_id' => $request->category_id
        ]);
    }
}
