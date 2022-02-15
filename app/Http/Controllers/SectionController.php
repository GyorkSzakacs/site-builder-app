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
        $validData = $request->validate([
            'tittle' => 'required',
            'tittle_visibility' => 'boolean',
            'slug' => '',
            'position' => '',
            'page_id' => ''
        ]);
        
        Section::create($validData);
    }

    /**
     * Update the selected section.
     * 
     * @param Request $request
     * @param Section $section
     * @return void
     */
    public function update(Request $request, Section $section)
    {
        $section->update([
            'tittle' => $request->tittle,
            'tittle_visibility' => $request->tittle_visibility,
            'slug' => '',
            'position' => $request->position,
            'page_id' => $request->page_id
        ]);
    }

    /**
     * Delete the selected section.
     * 
     * @param Selction $section
     * @return void
     */
    public function destroy(Section $section)
    {
        $section->delete();
    }
}
