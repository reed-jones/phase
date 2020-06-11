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
    | Determines whether to use the vue-router integration
    |--------------------------------------------------------------------------
    |
    | By default vue-router will be integrated if the Phased\Routing package
    | has been installed
    */
    'routing' => class_exists('\Phased\Routing\Facades\Phase'),

    /*
    |--------------------------------------------------------------------------
    | Window key to save the initial state
    |--------------------------------------------------------------------------
    |
    | This is where the state will be stored on page load,
    | (If state integration is enabled of course)
    */
    'initial_state_key' => '__PHASE_STATE__',

    /*
    |--------------------------------------------------------------------------
    | <script id=initial_state_id />
    |--------------------------------------------------------------------------
    |
    | This is the id save to the state <script /> tag. Useful for removing the
    | <script> tag after load, or customizing in the event of name collisions
    */
   'initial_state_id' => 'phase-state',

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
    | Turn SSR On/Off
    */
    'ssr' => false,

    /*
    |--------------------------------------------------------------------------
    | Client Hydration
    |--------------------------------------------------------------------------
    |
    | Enable or Disable Client side hydration for SSR enabled apps
    */
    'hydrate' => false,

    /*
    |--------------------------------------------------------------------------
    | Main entry assets
    |--------------------------------------------------------------------------
    */
    'assets' => [
        'resources' => resource_path(),
        'public' => public_path(),
        'ssr' => [
            'server' => 'js/app-server.js',
            'client' => 'js/app-client.js'
        ]
    ],
];
