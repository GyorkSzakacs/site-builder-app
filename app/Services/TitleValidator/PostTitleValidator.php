<?php

namespace App\Services\TitleValidator;

use App\Services\TitleValidator\TitleValidator;
use App\Models\Post;

class PostTitleValidator implements TitleValidator
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
     * Check that the given post title is unique in current section for storing.
     * 
     * @return boolean
     */
    public function isTitleUniqueForStoring()
    {
        $titleInSection = Post::where([
                                        ['title', $this->validData['title']],
                                        ['section_id', $this->validData['section_id']]
                                ])->get();

        if($titleInSection->count() > 0 )
        {
            return false;
        }

        return true;
    }

    /**
     * Check that the given post title is unique in current section for updating.
     * 
     * @param int $id
     * @return boolean
     */
    public function isTitleUniqueForUpdating(int $id)
    {
        $titleInSection = Post::where([
                                    ['id', '<>', $id],
                                    ['title', $this->validData['title']],
                                    ['section_id', $this->validData['section_id']]
                                ])->get();

        if($titleInSection->count() > 0 )
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
        return 'Ezzel a címmel már létezik poszt ebben a szekcióban!';
    }
}