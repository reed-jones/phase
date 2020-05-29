<?php

use Illuminate\Http\JsonResponse;
use Phased\Routing\Facades\Phase;

it("outputs registered routes when requested", function () {
    assertSame(
        Phase::getRoutes(),
        [[
            "uri" => "/",
            "action" => [
                "middleware" => [],
                "uses" => "Phased\Tests\Routing\Controllers\TestController@HelloWorld",
                "controller" => "Phased\Tests\Routing\Controllers\TestController@HelloWorld",
                "namespace" => "Phased\Tests\Routing\Controllers",
                "prefix" => null,
                "where" => []
            ]
        ]]
    );
});

it('adds routes to the route array', function () {
    assertSame(
        Phase::getRoutes(),
        [[
            "uri" => "/",
            "action" => [
                "middleware" => [],
                "uses" => "Phased\Tests\Routing\Controllers\TestController@HelloWorld",
                "controller" => "Phased\Tests\Routing\Controllers\TestController@HelloWorld",
                "namespace" => "Phased\Tests\Routing\Controllers",
                "prefix" => null,
                "where" => []
            ]
        ]]
    );

    Phase::addRoute('/test', [
        "middleware" => [],
        "uses" => "Phased\Tests\Routing\Controllers\TestController@TestRoute",
        "controller" => "Phased\Tests\Routing\Controllers\TestController@TestRoute",
        "namespace" => "Phased\Tests\Routing\Controllers",
        "prefix" => null,
        "where" => []
    ]);

    assertSame(
        Phase::getRoutes(),
        [
            [
                "uri" => "/",
                "action" => [
                    "middleware" => [],
                    "uses" => "Phased\Tests\Routing\Controllers\TestController@HelloWorld",
                    "controller" => "Phased\Tests\Routing\Controllers\TestController@HelloWorld",
                    "namespace" => "Phased\Tests\Routing\Controllers",
                    "prefix" => null,
                    "where" => []
                ]
            ], [
                'uri' => '/test',
                'action' => [
                    "middleware" => [],
                    "uses" => "Phased\Tests\Routing\Controllers\TestController@TestRoute",
                    "controller" => "Phased\Tests\Routing\Controllers\TestController@TestRoute",
                    "namespace" => "Phased\Tests\Routing\Controllers",
                    "prefix" => null,
                    "where" => []
                ]
            ]
        ]
    );
});

it('returns an json vuex response api response', function () {
    assertInstanceOf(JsonResponse::class, Phase::api());

    assertEquals(['$vuex' => []], Phase::api()->getData($assoc = true));
});
