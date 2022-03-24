<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TitleValidator\TitleValidator;
use App\Services\TitleValidator\PageTitleValidator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TitleValidator::class, PageTitleValidator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
