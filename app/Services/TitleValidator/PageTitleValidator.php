<?php

namespace App\Services\TitleValidator;

use App\Services\TitleValidator\TitleValidator;
use App\Models\Page;

class PageTitleValidator implements TitleValidator
{
    /**
     * Valid data array from PageRequest.
     * 
     * @var array
     */
    public $validData = [];
    
    /**
     * Get valid data from requeest.
     * 
     * @param object $request
     * @return array
     */
    public function setValidDataFromRequest(object $request)
    {
        $this->validData = $request->validated();

        return $this;
    }

    /**
     * Get valid data.
     * 
     * @return array
     */
    public function getValidData()
    {
        return $this->validData;
    }

    /**
     * Check that the given page title is unique for storing.
     * 
     *@return boolean
     */
    public function isTitleUniqueForStoring()
    {
        $sameTitle = Page::Where('title', $this->validData['title'])->get();

        if($sameTitle->count() > 0)
        {
            return false;
        }

        return true;
    }

    /**
     * Check that the given page title is unique for updating.
     * 
     *@param int $id
     * @return boolean
     */
    public function isTitleUniqueForUpdating(int $id)
    {
        $sameTitle = Page::Where([
                                    ['id', '<>', $id],
                                    ['title', $this->validData['title']]
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