<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Compose categories with nav bar
        View::composer('layouts.navigation', function ($view) {
            
            $menuItems = Category::orderBy('position', 'asc')
                            ->get();

            $view->with('menuItems', $menuItems);
        });
    }
}
