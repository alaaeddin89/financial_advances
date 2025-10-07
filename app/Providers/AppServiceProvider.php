<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Gate::define('cashier', function(User $user) {
            return $user->role == "cashier";
        });
        Gate::define('admin', function(User $user) {
            return $user->role == "admin";
        });
        Gate::define('accountant', function(User $user) {
            return $user->role == "accountant";
        });

        View::composer('*', function ($view) {
            if (auth()->check()) {
                $view->with('unreadNotifications', auth()->user()->unreadNotifications);
            } else {
                $view->with('unreadNotifications', collect());
            }
        });
       
        
      

      

    }
}
