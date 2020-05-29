<?php

use Illuminate\Support\Facades\Artisan;

it('test artisan command table', function () {
    Artisan::call('phase:routes');

    $resultAsText = Artisan::output();

    assertSame(
        "+--------------+-----+---------------------------+------------+\n" .
            "| Group Prefix | URI | Navigation Name           | Middleware |\n" .
            "+--------------+-----+---------------------------+------------+\n" .
            "|              | /   | TestController@HelloWorld |            |\n" .
            "+--------------+-----+---------------------------+------------+\n",
        $resultAsText
    );
});

it('test artisan command json', function () {
    Artisan::call('phase:routes --json');

    $resultAsText = Artisan::output();

    assertSame(
        '[{"prefix":null,"uri":"\/","name":"TestController@HelloWorld","middleware":""}]' . "\n",
        $resultAsText
    );
});

it('test artisan command json config', function () {
    Artisan::call('phase:routes --json --config');

    $resultAsText = Artisan::output();

    $configKeys = array_keys(json_decode($resultAsText, true)['config']);

    assertSame(
        [
            'entry',
            'state',
            'routing',
            'initial_state_key',
            'initial_state_id',
            'redirects',
            'ssr',
            'hydrate',
            'assets',
        ],
        $configKeys
    );
});
