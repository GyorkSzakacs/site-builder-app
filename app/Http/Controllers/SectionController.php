<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Http\Requests\SectionRequest;
use App\Services\TitleValidator\TitleValidator;
use App\Traits\BackRedirector;

class SectionController extends Controller
{
    use BackRedirector;

    /**
     * SectionTitleValidator instace.
     * 
     * @var object
     */
    private $validator;

    /**
     * Create a new class instance.
     * 
     * @param TitleValidator $validator
     * @return void
     */
    public function __construct(TitleValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Create a new section.
     * 
     * @param SectionRequest $request
     * @return void
     */
    public function store(SectionRequest $request)
    {
        $this->validator->setValidDataFromRequest($request);
        
        if(!$this->validator->isTitleUniqueForStoring())
        {
            return $this->redirectBackWithError('title', $this->validator->getErrorMessage());
        }

        $newSection = Section::create($this->getOrderedValidData());

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
        $this->validator->setValidDataFromRequest($request);
        
        if(!$this->validator->isTitleUniqueForUpdating($section->id))
        {
            return $this->redirectBackWithError('title', $this->validator->getErrorMessage());
        }

        $section->update($this->getOrderedValidData());

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
     * Get ordered valid data for storing process.
     * 
     * @return array
     */
    protected function getOrderedValidData()
    {
        return [
            'title' => $this->validator->validData['title'],
            'slug' => '',
            'title_visibility' => $this->validator->validData['title_visibility'],
            'page_id' => $this->validator->validData['page_id'],
            'position' => $this->validator->validData['position']
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
