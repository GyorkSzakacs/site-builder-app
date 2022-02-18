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
        $newSection = Section::create($this->getValidData($request));

        return $this->redirectToPage($newSection);
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
        $section->update($this->getValidData($request));

        return $this->redirectToPage($section);
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

        return $this->redirectToPage($section);
    }

    /**
     * Get validated input data.
     * 
     * @param SectionRequest $request
     * @return array
     */
    protected function getValidData(SectionRequest $request)
    {
        return $request->safe()->merge(['slug' => ''])->all();
    }

    /**
     * Redirect to page of the section.
     * 
     * @param Section $section
     */
    protected function redirectToPage($section)
    {
        $pageSlug = $section->page->slug;

        return redirect('/'.$pageSlug);
    }
}
