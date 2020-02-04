<?php

declare(strict_types=1);

namespace Phased\Routing;

use Exception;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Phased\Routing\Commands\GeneratePhaseRouter;
use Phased\Routing\Facades\Phase;
use Phased\Routing\Factories\PhaseFactory;

class PhasedRoutingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind facade
        $this->registerPhaseFacade();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->recursiveMergeConfigFrom([
            __DIR__ . '/config.defaults.php',
            __DIR__ . '/config.stub.php'
        ], 'phase');

        // php artisan vendor:publish --provider="Phased\Routing\PhaseServiceProvider" --tag="config"
        $this->publishes([__DIR__ . '/config.stub.php' => config_path('phase.php')], 'config');

        // Route macros Route::phase('/test', 'TestController@testing')
        $this->registerRouterMacro();

        // Hidden Route generation command
        $this->registerCommands();

        // register custom blade namespace. allows to specify phase::bladeFile
        $this->registerBlades();
    }
    protected function recursiveMergeConfigFrom($paths, $key)
    {
        $config = collect($paths)->reduce(function ($config, $path) {
            return array_merge_phase($config, require $path);
        }, []);

        if (!($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $this->app['config']->set($key, $config);
        }
    }

    /**
     * Sets up the Route::phase macro.
     */
    public function registerRouterMacro(): void
    {
        Route::macro('phase', function (...$args) {
            $route = $this->match(['GET', 'HEAD'], ...$args);

            $controller = $route->action['controller'] ?? null;

            // make sure its not a closure
            // & make sure its controller@method
            if (is_string($controller) && Str::is('*@*', $controller)) {
                Phase::addRoute($route->uri, $route->action);
            } else {
                throw new Exception("Route::phase is not compatible with closures.\n"
                    . "Please use the controller@method syntax.\n"
                    . "Failed on '{$route->uri}' route.");
            }

            return $route;
        });
    }

    /**
     * Registers route generation cli command.
     */
    public function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([GeneratePhaseRouter::class]);
        }
    }

    public function registerBlades(): void
    {
        view()->addNamespace('phase', base_path('vendor/phased/routing/views'));
    }

    public function registerPhaseFacade(): void
    {
        App::singleton(PhaseFactory::class, function () {
            return new PhaseFactory;
        });
    }
}
