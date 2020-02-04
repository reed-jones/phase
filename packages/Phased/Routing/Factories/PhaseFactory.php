<?php

declare(strict_types=1);

namespace Phased\Routing\Factories;

class PhaseFactory
{
    /** @var array $routes */
    protected $routes = [];

    /**
     * Adds the supplied route to the 'phase' section of the app.
     *
     * @param string $uri
     * @param array $action
     *
     * @return void
     */
    public function addRoute(...$args)
    {
        [$uri, $action] = $args;
        array_push($this->routes, [
            'uri' => $uri,
            'action' => $action,
        ]);
    }

    /**
     * Gets the currently registered routes.
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Automatically switches between JSON api response &
     * Blade views for SPA's.
     *
     * @param string [$blade=null]
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function view($blade = null)
    {

        $blade = $blade ?? config('phase.entry');

        $jsonResponse = config('phase.state') ? 'phase' : 'json';

        return request()->expectsJson()
            ? response()->{$jsonResponse}()
            : view($blade);
    }
}
