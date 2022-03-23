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
        
        if(!$this->isTitleUniqueOnPageForStoring($validData['title'], $validData['page_id']))
        {
            return $this->redirectBackWithTitleError();
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
        $validData = $this->getValidData($request);
        
        if(!$this->isTitleUniqueOnPageForUpdating($validData['title'], $validData['page_id'], $section->id))
        {
            return $this->redirectBackWithTitleError();
        }

        $section->update($validData);

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
        
        return [
            'title' => $validated['title'],
            'slug' => '',
            'title_visibility' => $validated['title_visibility'],
            'page_id' => $validated['page_id'],
            'position' => $validated['position']
        ];
    }

    /**
     * Check the title is unique on this page while storing.
     * 
     * @param string $title
     * @param int $page_id
     * @return boolean
     */
    protected function isTitleUniqueOnPageForStoring($title, $page_id)
    {
        $titleOnPage = Section::where([
                                        ['title', $title],
                                        ['page_id', $page_id]
                                    ])->get();

        if($titleOnPage->count() > 0 )
        {
            return false;
        }

        return true;
    }

    /**
     * Check the title is unique on this page while updating.
     * 
     * @param string $title
     * @param int $page_id
     * @param int $id
     * @return boolean
     */
    protected function isTitleUniqueOnPageForUpdating($title, $page_id,  $id)
    {
        $titleOnPage = Section::where([
                                        ['id', '<>', $id],
                                        ['title', $title],
                                        ['page_id', $page_id]
                                    ])->get();

        if($titleOnPage->count() > 0 )
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
        return back()->withErrors(['title' => 'Ezzel a címmel már létezik szekció ezen az oldalon!'])->withInput();
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
