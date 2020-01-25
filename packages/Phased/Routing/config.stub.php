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
    | Error redirects.
    |--------------------------------------------------------------------------
    |
    | Page redirection for Server side errors
    */
    'redirects' => [
        401 => 'Auth.LoginPage',
        403 => 'Auth.LoginPage',
        404 => 'Errors.MissingPage',
        500 => 'Errors.ServerError',
    ],

    /*
    |--------------------------------------------------------------------------
    | Main entry assets
    |--------------------------------------------------------------------------
    */
    'assets' => [
        'js' => ['js/app.js'],
        'sass' => ['sass/app.scss'],
    ],
];
