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
    | Main entry assets
    |--------------------------------------------------------------------------
    */
    'assets' => [
        'resources' => resource_path(),
        'public' => public_path()
    ],
];
