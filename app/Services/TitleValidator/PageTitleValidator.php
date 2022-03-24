<?php

namespace App\Services\TitleValidator;

use App\Services\TitleValidator\TitleValidator;
use App\Models\Page;

class PageTitleValidator implements TitleValidator
{
    /**
     * Get valid data from requeest.
     * 
     * @param object $request
     * @return array
     */
    public function getValidDataFromRequest(object $request)
    {
        $validated = $request->validated();

        return [
            'title' => $validated['title'],
            'slug' => '',
            'title_visibility' => $validated['title_visibility'],
            'category_id' => isset($validated['category_id']) ? $validated['category_id'] : '',
            'position' => $validated['position']
        ];
    }

    /**
     * Check that the given page title in unique for storing.
     * 
     * @param string $title
     * @return boolean
     */
    public function isTitleUniqueForStoring(string $title)
    {
        $sameTitle = Page::Where('title', $title)->get();

        if($sameTitle->count() > 0)
        {
            return false;
        }

        return true;
    }

    /**
     * Check that the given page title is unique for updating.
     * 
     * @param string $title
     * @param int $id
     * @return boolean
     */
    public function isTitleUniqueForUpdating(string $title, int $id)
    {
        $sameTitle = Page::Where([
                                    ['id', '<>', $id],
                                    ['title', $title]
                                ])->get();

        if($sameTitle->count() > 0)
        {
            return false;
        }

        return true;
    }

    /**
     * Get the error message for page title.
     * 
     * @return string
     */
    public function getErrorMessage()
    {
        return 'Ezzel a címmel már létezik oldal!';
    }
}