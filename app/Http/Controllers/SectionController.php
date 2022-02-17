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
        Section::create($this->getValidData($request));
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
}
