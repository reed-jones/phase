<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Phased\State\Facades\Vuex;

class PhaseServiceProvider extends ServiceProvider
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
        if (!$this->app->runningInConsole() && !request()->expectsJson()) {
            Vuex::lazyLoad('user', 'profile');
        }
    }
}
