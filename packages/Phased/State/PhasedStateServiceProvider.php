<?php

declare(strict_types=1);

namespace Phased\State;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
/* Macros */
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Phased\State\Facades\Vuex;
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
        $automatic = collect(glob(app_path().'/VuexLoaders/*ModuleLoader.php'))
            ->map(function ($file) {
                return str_replace('/', '\\', Str::replaceLast('.php', '', Str::replaceFirst(app_path().'/', Container::getInstance()->getNamespace(), $file)));
            })
            ->toArray();

        Vuex::register($automatic);

        // apply response & collection mixins
        $this->applyMixins();

        // blade directives @app @vuex
        $this->setDirectives();
    }

    /**
     * Sets up the utility mixins.
     */
    public function applyMixins()
    {
        // Response macros response()->phase()
        Response::mixin(new VuexResponseMixin);

        // Collection macros collect([])->toVuex('namespace', 'key')
        Collection::mixin(new VuexCollectionMixin);
    }

    /**
     * Sets up the blade directives.
     */
    public function setDirectives()
    {
        Blade::directive('vuex', function () {
            return "<?='<script id=\'"
                .config('phase.initial_state_id', 'phase-state')."\'>window."
                .config('phase.initial_state_key', '__PHASE_STATE__')
                ."='.Phased\State\Facades\Vuex::toJson().'</script>';?>";
        });
    }
}
