<?php

namespace Phased\State\Middleware;

use Closure;
use Phased\State\Facades\Vuex;

class ModuleLoaderMiddleware
{
    public function handle($request, Closure $next, ...$selectors)
    {
        collect(explode('|', implode(',', $selectors)))->each(function ($selectors) {
            $args = explode(',', $selectors);
            $module = array_shift($args);
            $key = array_shift($args);
            Vuex::load($module, [$key => $args]);
        });

        return $next($request);
    }
}
