<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Http\Requests\SectionRequest;

class SectionController extends Controller
{
    /**
     * Create a new section.
     * 
     * @param SectionRequest $request
     * @return void
     */
    public function store(SectionRequest $request)
    {
        $validData = $request->validated();
        
        Section::create($validData);
    }

    /**
     * Update the selected section.
     * 
     * @param SectionRequest $request
     * @param Section $section
     * @return void
     */
    public function update(SectionRequest $request, Section $section)
    {
        $validData = $request->validated();
        
        $section->update($validData);
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