<?php

namespace Phased\Tests\Routing;

use Illuminate\Support\Facades\Route;

class PhaseServiceProviderTest extends TestCase
{
    public function test_route_macro_is_registered()
    {
        $route = Route::phase('/', 'HomeController@HomePage');
        $routes = Route::getRoutes();

        $this->assertNotEmpty($routes->getRoutes());
        $this->assertEquals($route, $routes->getRoutes()[0]);
        $this->assertEquals(['GET', 'HEAD'], $route->methods);
        $this->assertEquals('/', $route->uri);
        $this->assertEquals([
            'uses' => 'HomeController@HomePage',
            'controller' => 'HomeController@HomePage',
        ], $route->action);
    }
}
