# Phase

## Why
Phase aims to integrate Laravel, Vuex, & Vue Router as seamlessly as possible. All phase routes specified in your `routes/web.php` are automatically configured for slick SPA navigation. All configured api calls will automatically be committed into your vuex store. Data loaded through your view controllers is immediately available in the vuex store. No waiting for separate api calls, No `mutation` boilerplate: `state.count = count`. No chance of your vue-router configuration getting out of sync with your web routes. No reason to give up the nice Route -> Controller -> Page view flow.

## Installation
  - `npm i -D @phased/phase` (meta package)
  - `composer require phased/routing`
  - `composer require phased/state`

  - * Note Currently routing depends on state being installed, but further decoupling is planned so that the two packages may be used independently. State however is standalone at this point and can be used by itself if no SPA routing is required. just install `@phased/state` instead of the `@phased/phase` meta package.

## Setup
  - For frontend vuex integration, import the `hydrate` helper `new Store(hydrate({ ...yourState }))`.
  - For Vue Router, dynamically import routes from `@phased` and use that for your route definitions

## Routing
  - Web Routes
    - `routes/web.php` -> `Route::phase('/url/to/page', 'YourController@method');`
    - -> in your controller, `return Phase::view();`
    - Now benefit from preloaded vuex data on page initial page load, and automatic updated vuex store on page changes!
    - For pure API calls that just want to update vuex store automatically you can use `return response()->vuex()` with any additional data passed as if it was `->json`, `->vuex($extraData)`
  - Folder structure...

## State Management
  - Api
    - `Vuex::module($namespace, $state);`
    - `Vuex::state($state);`
  - Collections
    - `->toVuex($namespace, $key)`
  - Models
    - Vuexable Trait
    - `->toVuex($namespace, $key)` (same as collections)
  - Lazy Loading
  - Module Loaders/Module Definitions
    - `Vuex::register([ UserModuleDefinition::class ]);` Put this into AppServiceProvider
    - `Vuex::load($namespace, $key);` will load the data from the module definition above. If not specified, the namespace will attempt to be pulled from the class name, but can be overridden using `protected $namespace = 'users'` for example. The `$key` will both be the where in the front end the data will be saved, and the name of the function on the class to be called.

## Troubleshooting
  - `dd(Vuex::toArray());` will dump all the currently saved vuex state
  - For Api calls, the mutation will be visible from within the Vue DevTools mutations tab
