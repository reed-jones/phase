<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Phase SPA blade File
    |--------------------------------------------------------------------------
    |
    | The default `phase::app` will load the default phase blade file. For
    | customization, you will need to create your own entry blade file
    */
    'entry' => 'phase::app',

    /*
    |--------------------------------------------------------------------------
    | Determines whether to use the vuex state integration
    |--------------------------------------------------------------------------
    |
    | By default state management will be integrated if the Phased\State package
    | has been installed
    */
    'state' => class_exists('\Phased\State\Facades\Vuex'),


    /*
    |--------------------------------------------------------------------------
    | Error redirects.
    |--------------------------------------------------------------------------
    |
    | Page redirection for Server side errors
    */
    'redirects' => [],

    /*
    |--------------------------------------------------------------------------
    | Server Side Rendering
    |--------------------------------------------------------------------------
    |
    | https://github.com/spatie/laravel-server-side-rendering
    */
    'ssr' => false,
    // 'ssr' => [
    //     'client' => 'js/app-client.js',
    //     'server' => 'js/app-server.js'
    // ],

    /*
    |--------------------------------------------------------------------------
    | Main entry assets
    |--------------------------------------------------------------------------
    */
    'assets' => [
        'resources' => resource_path(),
        'public' => public_path(),
        'js' => [],
        'sass' => []
    ],
];
