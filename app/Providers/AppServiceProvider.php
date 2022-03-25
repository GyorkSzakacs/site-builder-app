<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TitleValidator\TitleValidator;
use App\Services\TitleValidator\PageTitleValidator;
use App\Services\TitleValidator\SectionTitleValidator;
use App\Services\TitleValidator\PostTitleValidator;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\PostController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(PageController::class)
                    ->needs(TitleValidator::class)
                    ->give(function(){
                        return new PageTitleValidator();
                    });

        $this->app->when(SectionController::class)
                    ->needs(TitleValidator::class)
                    ->give(function(){
                        return new SectionTitleValidator();
                    });

        $this->app->when(PostController::class)
                    ->needs(TitleValidator::class)
                    ->give(function(){
                        return new PostTitleValidator();
                    });
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
