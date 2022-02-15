<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;

class SectionController extends Controller
{
    /**
     * Create a new section.
     * 
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        Section::create([
            'tittle' => $request->tittle,
            'tittle_visibility' => $request->tittle_visibility,
            'slug' => '',
            'position' => $request->position,
            'page_id' => $request->page_id
        ]);
    }
}
