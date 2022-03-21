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
        $validData = $this->getValidData($request);
        
        if($validData == null)
        {
            return back()->withErrors(['title' => 'Ezzel a címmel már létezik szekció ezen az oldalon!'])->withInput();
        }

        $newSection = Section::create($validData);

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
        $validated = $request->validated();

        $titleOnPage = Section::where([
                                        ['title', $validated['title']],
                                        ['page_id', $validated['page_id']]
                                    ])->get();

        if($titleOnPage->count() > 0 )
        {
            return;
        }
        
        return [
            'title' => $validated['title'],
            'slug' => '',
            'title_visibility' => $validated['title_visibility'],
            'page_id' => $validated['page_id'],
            'position' => $validated['position']
        ];
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
