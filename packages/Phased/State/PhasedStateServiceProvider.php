<?php

declare(strict_types=1);

namespace Phased\State;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/** Macros */
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
use Phased\State\Factories\VuexFactory;
use Phased\State\Mixins\VuexCollectionMixin;
use Phased\State\Mixins\VuexResponseMixin;

class PhasedStateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        App::singleton(VuexFactory::class, function () {
            return new VuexFactory;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // apply response & collection mixins
        $this->applyMixins();

        // blade directives @app @vuex
        $this->setDirectives();
    }

    /**
     * Sets up the utility mixins
     */
    public function applyMixins()
    {
        // Response macros response()->phase()
        Response::mixin(new VuexResponseMixin);

        // Collection macros collect([])->toVuex('namespace', 'key')
        Collection::mixin(new VuexCollectionMixin);
    }

    /**
     * Sets up the blade directives
     */
    public function setDirectives()
    {
        Blade::directive('vuex', function () {
            return "<?='<script id=\'initial-state\'>window.__PHASED_STATE__='.Phased\State\Facades\Vuex::toJson().'</script>';?>";
        });
    }
}
