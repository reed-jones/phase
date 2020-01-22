<?php

declare(strict_types=1);

namespace Phased\Routing;

use Exception;
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
     * Bootstrap services.
     */
    public function boot(): void
    {
        // php artisan vendor:publish --provider="Phased\Routing\PhaseServiceProvider" --tag="config"
        $this->publishes([__DIR__.'/config.stub.php' => config_path('phase.php')], 'config');

        // Route macros Route::phase('/test', 'TestController@testing')
        $this->registerRouterMacro();

        // Hidden Route generation command
        $this->registerCommands();

        // register custom blade namespace.  allows to specify phase::bladeFile
        $this->registerBlades();

        // Bind facade
        $this->registerPhaseFacade();
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config.stub.php', 'phase');
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
                    ."Please use the controller@method syntax.\n"
                    ."Failed on '{$route->uri}' route.");
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
        view()->addNamespace('phase', base_path('vendor/reed-jones/phase/src/views'));
    }

    public function registerPhaseFacade(): void
    {
        App::bind(PhaseFactory::class, function () {
            return new PhaseFactory;
        });
    }
}
