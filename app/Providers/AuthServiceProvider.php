<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('first-register', function(?User $user){
            return User::all()->count() == 0;
        });

        Gate::define('register', function(User $user){
            return $user->hasAdminAccess();
        });

        Gate::define('image-upload', function(User $user){
            return $user->hasEditorAccess();
        });

        Gate::define('image-download', function(User $user){
            return $user->hasEditorAccess();;
        });

        Gate::define('image-delete', function(User $user){
            return $user->hasEditorAccess();;
        });

        Gate::define('image-view', function(User $user){
            return $user->hasEditorAccess();;
        });
    }
}
