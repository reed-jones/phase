<?php
/**
 * A whole bunch of silly hacks
 */

if (!function_exists('access_protected')) {
    // access protected property
    function access_protected($obj, $prop) {
        $reflection = new ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }
}

if (!function_exists('getNamespacedRoute')) {
    // Attempts to prefix the RouteServiceProvider namespace
    // if available
    function getNamespacedRoute($class) {
        $routeServiceProvider = app()->getProvider(\App\Providers\RouteServiceProvider::class);
        return property_exists($routeServiceProvider, 'namespace' )
            ? access_protected($routeServiceProvider, 'namespace').'\\'.$class : $class;
    }
}

if (!function_exists('getRouteByAction')) {
    // looks up route by class/action
    function getRouteByAction($class) {
        $routeCollection = \Illuminate\Support\Facades\App::make('router')->getRoutes();
        return $routeCollection->getByAction($class) ?? $routeCollection->getByAction(getNamespacedRoute($class));
    }
}

class PhaseRouteResolutionExceptions extends Exception { /**  */ }
if (!function_exists('phaseRoute')) {
    function phaseRoute($class) {
        $route = getRouteByAction($class);
        if (!$route) {
            throw new PhaseRouteResolutionExceptions("Could not find route: {$class}");
        }
        return $route->uri();
    }
}
