<?php

declare(strict_types=1);

namespace Phased\State;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
/* Macros */
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Phased\State\Commands\MakeModuleLoader;
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
        App::singleton(VuexFactory::class, function ($app) {
            return new VuexFactory($app);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->autoDiscoverModuleLoaders();

        $this->registerCommands();

        // apply response & collection mixins
        $this->applyMixins();

        // blade directives @vuex
        $this->setDirectives();
    }

    protected function autoDiscoverModuleLoaders()
    {
        // TODO: Register folders as namespaced modules
        $automatic = collect(glob(app_path().'/VuexLoaders/*ModuleLoader.php'))
            ->map(function ($file) {
                $dropPath = Str::replaceFirst(app_path().'/', Container::getInstance()->getNamespace(), $file);
                $dropExtension = Str::replaceLast('.php', '', $dropPath);

                return str_replace('/', '\\', $dropExtension);
            })
            ->toArray();

        Vuex::register($automatic);
    }

    /**
     * Registers ModuleLoader generation cli command.
     */
    public function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([MakeModuleLoader::class]);
        }
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
