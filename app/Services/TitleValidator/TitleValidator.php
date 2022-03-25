<?php

namespace App\Services\TitleValidator;

interface TitleValidator
{
    public function setValidDataFromRequest(object $request);
    public function getValidData();
    public function isTitleUniqueForStoring();
    public function isTitleUniqueForUpdating(int $id);
    public function getErrorMessage();
}