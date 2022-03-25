<?php

namespace App\Traits;

trait BackRedirector
{
     /**
     * Redirect back with error for title.
     * 
     * @param string $attribute
     * @param string $errorMessage
     * @return void
     */
    protected function redirectBackWithError($attribute, $errorMessage)
    {
        return back()->withErrors([$attribute => $errorMessage])->withInput();
    }
}