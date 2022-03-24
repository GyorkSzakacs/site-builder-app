<?php

namespace App\Services\TitleValidator;

interface TitleValidator
{
    public function getValidDataFromRequest(object $request);
    public function isTitleUniqueForStoring(string $title);
    public function isTitleUniqueForUpdating(string $title, int $id);
    public function getErrorMessage();
}