<?php

use Illuminate\Support\Facades\Route;

it('registers ::phase() route macro', function () {
    $route = Route::phase('/', 'HomeController@HomePage');
    $routes = Route::getRoutes();

    assertNotEmpty($routes->getRoutes());
    assertEquals($route, $routes->getRoutes()[0]);
    assertEquals(['GET', 'HEAD'], $route->methods);
    assertEquals('/', $route->uri);
    assertEquals([
        'uses' => 'HomeController@HomePage',
        'controller' => 'HomeController@HomePage',
    ], $route->action);
});

it('merges both default & stub configs', function () {
    $sass = config('phase.assets.sass'); // from config.stub.php
    $public = config('phase.assets.public'); // from config.defaults.php

    assertEquals($sass, []);
    assertEquals($public, public_path());
});

it('registers the phase:routes command')
    ->artisan('phase:routes')
    ->assertExitCode(0);

it('registers the phase::app view namespace', function () {
    assertEquals(
        view()->getFinder()->getHints()['phase'],
        [base_path('vendor/phased/routing/views')]
    );
});

it('registers the Phase facade', function () {
    assertArrayHasKey('action', app('Phased\Routing\Factories\PhaseFactory')->getRoutes()[0]);
});
