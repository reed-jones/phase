<?php

namespace Phased\Tests\Routing;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            'Phased\State\PhasedStateServiceProvider',
            'Phased\Routing\PhasedRoutingServiceProvider',
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Vuex' => 'Phased\State\Facades\Vuex',
            'Phase' => 'Phased\Routing\Facades\Phase',
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        Route::group([
            'namespace' => 'Phased\Tests\Routing\Controllers',
            'middleware' => [],
        ], function () {
            Route::phase('/', 'TestController@HelloWorld');
        });
        View::addLocation(__DIR__ . '/views');
    }
}
